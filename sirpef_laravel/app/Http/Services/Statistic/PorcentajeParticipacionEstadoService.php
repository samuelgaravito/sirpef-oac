<?php

namespace App\Http\Services\Statistic;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\UnidadAdscrita;
use App\Models\Registro;
use App\Models\Persona;
use App\Models\Estado;
use Carbon\Carbon;
use App\Http\Services\Const\ObtenerPersonasService;

class PorcentajeParticipacionEstadoService
{
    static public function calcularPorcentajeParticipacionPorEstado($fechaDesde = null, $fechaHasta = null)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $eventoActivoId = $user->configUser->evento_activo;

        if (!$eventoActivoId) {
            return response()->json([]); // No mostrar nada si evento_activo es null
        }

        $personasQuery = ObtenerPersonasService::obtenerPersonas();

        // Obtén la cantidad de personas por estado filtradas por unidades adscritas
        $personasPorEstado = Persona::selectRaw('tbl_estados.id as estado_id, COUNT(*) as total_personas')
            ->join('tbl_parroquias', 'tbl_personas.parroquia_id', '=', 'tbl_parroquias.id')
            ->join('tbl_municipio', 'tbl_parroquias.municipio_id', '=', 'tbl_municipio.id')
            ->join('tbl_estados', 'tbl_municipio.estado_id', '=', 'tbl_estados.id')
            ->whereIn('tbl_personas.id', function ($query) use ($eventoActivoId, $personasQuery) {
                $query->select('persona_id')
                    ->from('tb_evento_persona')
                    ->where('evento_id', $eventoActivoId)
                    ->whereIn('persona_id', $personasQuery->pluck('id'));
            })
            ->groupBy('tbl_estados.id')
            ->get()
            ->keyBy('estado_id');

        // Obtén la cantidad de personas con registros por estado filtradas por unidades adscritas y por fecha
        $personasConRegistroPorEstado = Registro::selectRaw('tbl_estados.id as estado_id, COUNT(DISTINCT ep.persona_id) as total_votos')
    ->join('tb_evento_persona as ep', 'tbl_registros.evento_persona_id', '=', 'ep.id')
    ->join('tbl_personas', 'ep.persona_id', '=', 'tbl_personas.id')
    ->join('tbl_parroquias', 'tbl_personas.parroquia_id', '=', 'tbl_parroquias.id')
    ->join('tbl_municipio', 'tbl_parroquias.municipio_id', '=', 'tbl_municipio.id')
    ->join('tbl_estados', 'tbl_municipio.estado_id', '=', 'tbl_estados.id')
    ->whereIn('ep.persona_id', function ($query) use ($eventoActivoId, $personasQuery) {
        $query->select('persona_id')
            ->from('tb_evento_persona')
            ->where('evento_id', $eventoActivoId)
            ->whereIn('persona_id', $personasQuery->pluck('id'));
    })
    ->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
    ->whereDate('tbl_registros.created_at', '<=', $fechaHasta)
    ->groupBy('tbl_estados.id')
    ->get()
    ->keyBy('estado_id');

        // Obtén todos los estados
        $estados = Estado::all();

        // Combina los datos de los estados con los registros de votos
        $data = $estados->map(function ($estado) use ($personasPorEstado, $personasConRegistroPorEstado) {
            $estado_id = $estado->id;
            $totalPersonas = $personasPorEstado->get($estado_id)->total_personas ?? 0;
            $totalVotos = $personasConRegistroPorEstado->get($estado_id)->total_votos ?? 0;

            return (object) [
                'id' => $estado->id,
                'name' => $estado->estado, // Ajusta esto según el campo correcto para el nombre del estado
                'total_personas' => $totalPersonas,
                'total_votos' => $totalVotos,
                'porcentaje_participacion' => $totalPersonas > 0 ? ($totalVotos * 100) / $totalPersonas : 0,
            ];
        })->values(); // Convertir a array de objetos

        // Devuelve los resultados como JSON
        return response()->json($data);
    }
}