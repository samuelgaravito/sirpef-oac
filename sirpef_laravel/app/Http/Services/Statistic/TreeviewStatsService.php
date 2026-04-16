<?php

namespace App\Http\Services\Statistic;

use App\Http\Services\Const\ObtenerPersonasService;
use App\Models\EstatusTramite;
use App\Models\TipoCaso;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TreeviewStatsService
{
    /**
     * Obtiene y procesa las estadísticas para el gráfico de árbol.
     *
     * @return JsonResponse
     */
    public static function getStats(): JsonResponse
    {
        // 1. Obtener la consulta base de personas autorizadas para el usuario.
        $personasQuery = ObtenerPersonasService::obtenerPersonas(auth()->user());
        $personasIds = $personasQuery->pluck('id');

        // 2. Obtener todos los tipos de caso y todos los estatus de trámite para construir la estructura completa.
        $allTipoCasos = TipoCaso::all();
        $allEstatusTramite = EstatusTramite::all();
        $principalesEstatus = ['En Tramite', 'Orientado', 'Resultado Directo', 'Remitido a Otro', 'Cerrado'];

        // 3. Obtener los conteos reales de la base de datos.
        $latestSeguimiento = DB::table('tbl_seguimiento')
            ->select('registro_id', DB::raw('MAX(id) as last_seguimiento_id'))
            ->groupBy('registro_id');

        $stats = DB::table('tbl_registros as r')
            ->join('tb_evento_persona as ep', 'r.evento_persona_id', '=', 'ep.id')
            ->join('tbl_tipo_caso as tc', 'r.id_tipo_caso', '=', 'tc.id')
            ->leftJoinSub($latestSeguimiento, 'latest_s', 'r.id', '=', 'latest_s.registro_id')
            ->leftJoin('tbl_seguimiento as s', 's.id', '=', 'latest_s.last_seguimiento_id')
            ->leftJoin('tbl_estatus_tramite as et', 's.estatus_tramite_id', '=', 'et.id')
            ->whereIn('ep.persona_id', $personasIds)
            ->select(
                'tc.id as tipo_caso_id',
                'r.estatus_caso',
                'et.nombre_estatus'
            )
            ->get();

        $counts = $stats->groupBy('tipo_caso_id')->map(function ($group) {
            return $group->groupBy('estatus_caso')->map(function ($subGroup) {
                return $subGroup->groupBy('nombre_estatus');
            });
        });

        // 4. Construir la estructura completa y rellenarla con los conteos.
        $data = $allTipoCasos->map(function ($tipoCaso) use ($counts, $principalesEstatus, $allEstatusTramite) {
            $tipoCasoId = $tipoCaso->id;
            $tipoCasoCounts = $counts->get($tipoCasoId, collect());

            $getBreakdown = function ($estatusPrincipal) use ($tipoCasoCounts, $allEstatusTramite) {
                $breakdown = [];
                foreach ($allEstatusTramite as $estatus) {
                    $count = $tipoCasoCounts
                        ->get($estatusPrincipal, collect())
                        ->get($estatus->nombre_estatus, collect())
                        ->count();
                    $breakdown[] = ['id' => $estatus->id, 'name' => $estatus->nombre_estatus, 'value' => $count];
                }
                return $breakdown;
            };

            $contadores = [];
            foreach ($principalesEstatus as $estatus) {
                $key = strtolower(str_replace(' ', '_', $estatus));
                $contadores[$key] = $tipoCasoCounts->get($estatus, collect())->flatten()->count();
            }

            return [
                'id' => $tipoCasoId,
                'tipo' => $tipoCaso->tipo,
                'tipo_caso_padre_id' => $tipoCaso->tipo_caso_padre_id, // <-- CAMBIO REALIZADO AQUÍ
                'color' => $tipoCaso->color ?? '#CCCCCC',
                'total_por_tipo' => $tipoCasoCounts ? $tipoCasoCounts->flatten()->count() : 0,
                'en_tramite' => $contadores['en_tramite'],
                'orientados' => $contadores['orientado'],
                'resultado_directo' => $contadores['resultado_directo'],
                'remitidos' => $contadores['remitido_a_otro'],
                'cerrados' => $contadores['cerrado'],
                'en_tramite_breakdown' => $getBreakdown('En Tramite'),
                'orientados_breakdown' => $getBreakdown('Orientado'),
                'resultado_directo_breakdown' => $getBreakdown('Resultado Directo'),
                'remitidos_breakdown' => $getBreakdown('Remitido a Otro'),
                'cerrados_breakdown' => $getBreakdown('Cerrado'),
            ];
        });

        return response()->json($data);
    }
}
