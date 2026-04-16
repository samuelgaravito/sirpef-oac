<?php

namespace App\Http\Services\Statistic;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\UnidadAdscrita;
use App\Models\Registro;
use Carbon\Carbon;
use App\Http\Services\Const\ObtenerPersonasService;

class EstadisticaParticipacionHoraService
{
    static public function GetResumenDatahora($fechaDesde = null, $fechaHasta = null)
    {
       $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

      
// Obtener solo los IDs necesarios de una vez
$personasIds = ObtenerPersonasService::obtenerPersonas()->pluck('id')->toArray();

// Consulta optimizada con JOINs explícitos
$registros = Registro::selectRaw("
        EXTRACT(HOUR FROM hora_voto) as hora, 
        COUNT(*) as total, 
        SUM(CASE WHEN voto = true THEN 1 ELSE 0 END) as votos_true, 
        SUM(CASE WHEN voto = false THEN 1 ELSE 0 END) as votos_false
    ")
    ->join('tb_evento_persona', 'tbl_registros.evento_persona_id', '=', 'tb_evento_persona.id')
    ->where('tb_evento_persona.evento_id', $user->configUser->evento_activo)
    ->whereIn('tb_evento_persona.persona_id', $personasIds)
    ->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
    ->whereDate('tbl_registros.created_at', '<=', $fechaHasta)
    ->groupBy('hora')
    ->orderBy('hora', 'asc')
    ->get();

// Procesamiento del resultado
$hours = $registros->isEmpty() 
    ? range(0, 23) 
    : range($registros->first()->hora, $registros->last()->hora);

$respuesta = collect($hours)->map(function ($hora) use ($registros) {
    $registro = $registros->firstWhere('hora', $hora);
    return [
        'hora' => str_pad($hora, 2, '0', STR_PAD_LEFT),
        'total' => $registro ? $registro->total : 0,
        'votos_true' => $registro ? $registro->votos_true : 0,
        'votos_false' => $registro ? $registro->votos_false : 0,
    ];
});

return response()->json($respuesta);
    }
}






  /* $personasQuery = ObtenerPersonasService::obtenerPersonas();

        $registrosQuery = Registro::selectRaw("
        EXTRACT(HOUR FROM hora_voto) as hora, 
        COUNT(*) as total, 
        SUM(CASE WHEN voto = true THEN 1 ELSE 0 END) as votos_true, 
        SUM(CASE WHEN voto = false THEN 1 ELSE 0 END) as votos_false
    ")
    ->whereDate('created_at', '>=', $fechaDesde)
    ->whereDate('created_at', '<=', $fechaHasta)
    ->whereHas('eventoPersona', function ($query) use ($user) {
        $query->where('evento_id', $user->configUser->evento_activo);
    })
    ->whereHas('eventoPersona.persona', function ($query) use ($personasQuery) {
        $query->whereIn('id', $personasQuery->pluck('id'));
    });

        $registros = $registrosQuery
            ->groupBy('hora')
            ->orderBy('hora', 'asc')
            ->get();

        $firstHour = $registros->first() ? $registros->first()->hora : 0;
        $lastHour = $registros->last() ? $registros->last()->hora : 23;

        $respuesta = collect(range($firstHour, $lastHour))->map(function ($hora) use ($registros) {
            $registro = $registros->firstWhere('hora', $hora);
            return [
                'hora' => str_pad($hora, 2, '0', STR_PAD_LEFT),
                'total' => $registro ? $registro->total : 0,
                'votos_true' => $registro ? $registro->votos_true : 0,
                'votos_false' => $registro ? $registro->votos_false : 0,
            ];
        });

        return response()->json($respuesta);*/

