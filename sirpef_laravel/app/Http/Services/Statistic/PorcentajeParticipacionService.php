<?php

namespace App\Http\Services\Statistic;

use App\Http\Services\Const\ObtenerPersonasService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\Registro;
use App\Models\EventoPersona;
use App\Models\Ministerio;
use Illuminate\Support\Facades\DB;


class PorcentajeParticipacionService
{
    static public function calcularPorcentajeParticipacion($fechaDesde = null, $fechaHasta = null)
    {
       $user = auth()->user();
if (!$user) {
    return response()->json(['message' => 'No autenticado'], 401);
}

$fechaDesde = $fechaDesde ?? now()->startOfMonth()->toDateString();
$fechaHasta = $fechaHasta ?? now()->toDateString();

// 1. Obtener ministerios asignados (optimizado)
$ministeriosAsignados = json_decode($user->configUser->oficina_asignada, true) ?? [];

if (empty($ministeriosAsignados)) {
    return response()->json(['message' => 'El usuario no tiene ministerios asignados'], 404);
}

// 2. Obtener solo IDs de personas (sin cargar modelos completos)
$personasIds = Persona::whereIn('ministerio_id', $ministeriosAsignados)
    ->pluck('id');

if ($personasIds->isEmpty()) {
    return response()->json(['message' => 'No se encontraron personas en los ministerios asignados'], 404);
}

// 3. Obtener IDs de evento_persona (optimizado)
$eventoId = $user->configUser->evento_activo;
$eventoPersonaIds = EventoPersona::whereIn('persona_id', $personasIds)
    ->where('evento_id', $eventoId)
    ->pluck('id');

if ($eventoPersonaIds->isEmpty()) {
    return response()->json(['message' => 'No se encontraron personas participando en el evento activo'], 404);
}

// 4. Obtener estadísticas mejoradas
$resultados = DB::table('tbl_personas as p')
    ->select([
        'p.ministerio_id',
        DB::raw('COUNT(DISTINCT p.id) as total_personas'),
        DB::raw('SUM(CASE WHEN r.voto = TRUE THEN 1 ELSE 0 END) as votos_true'),
        DB::raw('SUM(CASE WHEN r.voto = FALSE THEN 1 ELSE 0 END) as votos_false'),
        DB::raw('COUNT(DISTINCT CASE WHEN r.id IS NOT NULL THEN p.id END) as personas_con_voto')
    ])
    ->leftJoin('tb_evento_persona as ep', function($join) use ($eventoPersonaIds) {
        $join->on('ep.persona_id', '=', 'p.id')
             ->whereIn('ep.id', $eventoPersonaIds);
    })
    ->leftJoin('tbl_registros as r', function($join) use ($fechaDesde, $fechaHasta) {
        $join->on('r.evento_persona_id', '=', 'ep.id')
             ->whereDate('r.created_at', '>=', $fechaDesde)
             ->whereDate('r.created_at', '<=', $fechaHasta);
    })
    ->whereIn('ep.id', $eventoPersonaIds)
    ->groupBy('p.ministerio_id')
    ->get();

$ministeriosIds = $resultados->pluck('ministerio_id')->unique();
$ministerios = Ministerio::whereIn('id', $ministeriosIds)
    ->pluck('nombre', 'id');

$data = $resultados->map(function ($item) use ($ministerios) {
    $totalVotos = $item->votos_true + $item->votos_false;
    
    $porcentajeParticipacion = $item->total_personas > 0 
        ? ($item->personas_con_voto / $item->total_personas) * 100
        : 0;

    return [
        'id' => $item->ministerio_id,
        'name' => $ministerios[$item->ministerio_id] ?? 'Desconocido',
        'total_personas' => $item->total_personas,
        'personas_con_voto' => $item->personas_con_voto,
        'total_votos' => $totalVotos,
        'votos_true' => $item->votos_true,
        'votos_false' => $item->votos_false,
        'porcentaje_participacion' => $porcentajeParticipacion,
    ];
})->values()->all();

return response()->json($data);
    }
}