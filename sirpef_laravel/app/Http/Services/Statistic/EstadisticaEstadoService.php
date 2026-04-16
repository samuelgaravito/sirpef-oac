<?php

namespace App\Http\Services\Statistic;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\UnidadAdscrita;
use App\Models\Estado;
use App\Models\Persona;
use App\Models\Registro;
use App\Http\Services\Const\ObtenerPersonasService;

class EstadisticaEstadoService
{
    static public function conteoRegistrosPorEstado($fechaDesde = null, $fechaHasta = null)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $eventoActivoId = $user->configUser->evento_activo;

        if (!$eventoActivoId) {
            return response()->json([]); // No mostrar nada si configUser-> es null
        }

        $personasQuery = ObtenerPersonasService::obtenerPersonas();

        // Consulta principal para registros
        $registrosQuery = Registro::selectRaw('tbl_estados.id as estado_id, COUNT(*) as total_registros, 
            SUM(CASE WHEN voto = true THEN 1 ELSE 0 END) as votos_verdaderos, 
            SUM(CASE WHEN voto = false THEN 1 ELSE 0 END) as votos_falsos')
            ->join('tb_evento_persona', 'tb_evento_persona.id', '=', 'tbl_registros.evento_persona_id')
            ->join('tbl_personas', 'tbl_personas.id', '=', 'tb_evento_persona.persona_id')
            ->join('tbl_parroquias', 'tbl_parroquias.id', '=', 'tbl_personas.parroquia_id')
            ->join('tbl_municipio', 'tbl_municipio.id', '=', 'tbl_parroquias.municipio_id')
            ->join('tbl_estados', 'tbl_estados.id', '=', 'tbl_municipio.estado_id')
            ->whereHas('eventoPersona', function ($query) use ($eventoActivoId, $personasQuery) {
                $query->where('evento_id', $eventoActivoId)
                    ->whereHas('persona', function ($query) use ($personasQuery) {
                        $query->whereIn('id', $personasQuery->pluck('id'));
                    });
            });

        // Aplicar filtros de fecha si están presentes
        if ($fechaDesde && $fechaHasta) {
            $registrosQuery->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
        }

        // Agrupar por estado y obtener los datos
        $conteo = $registrosQuery->groupBy('tbl_estados.id')
            ->get()
            ->map(function ($item) {
                $estado = Estado::find($item->estado_id);
                return [
                    'estado_id' => $item->estado_id,
                    'coordenadas' => [$estado->coordenadas_x, $estado->coordenadas_y],
                    'nombre_estado' => $estado->estado,
                    'total_registros' => $item->total_registros,
                    'votos_verdaderos' => $item->votos_verdaderos,
                    'votos_falsos' => $item->votos_falsos,
                ];
            });

        return response()->json($conteo);
    }
}