<?php

namespace App\Http\Services\Statistic;

use App\Http\Services\Const\ObtenerPersonasService;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;

class EstadisticaEdadSexoService
{
    static public function GetResumenDataBySexAge($fechaDesde = null, $fechaHasta = null)
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Obtén las personas que cumplen con los criterios desde ObtenerPersonasService
        $personasQuery = ObtenerPersonasService::obtenerPersonas();

        // Obtener rangos de edad
        $ageRanges = [
            '18-25' => [18, 25],
            '26-33' => [26, 33],
            '34-41' => [34, 41],
            '42-49' => [42, 49],
            '50-55' => [50, 55],
            '56+' => [56, 120]
        ];

        // Inicializar contadores
        $counts = [
            'Si_Participó' => ['M' => [], 'F' => []],
            'No_Participó' => ['M' => [], 'F' => []],
            'Total_de_Participación' => ['M' => [], 'F' => []],
            'Participantes_faltantes' => ['M' => [], 'F' => []]
        ];

        // Inicializar contadores para rangos de edad
        foreach ($ageRanges as $range => $ages) {
            $counts['Si_Participó']['M'][$range] = 0;
            $counts['Si_Participó']['F'][$range] = 0;
            $counts['No_Participó']['M'][$range] = 0;
            $counts['No_Participó']['F'][$range] = 0;
            $counts['Total_de_Participación']['M'][$range] = 0;
            $counts['Total_de_Participación']['F'][$range] = 0;
            $counts['Participantes_faltantes']['M'][$range] = 0;
            $counts['Participantes_faltantes']['F'][$range] = 0;
        }

        // Calcular las edades en la base de datos y realizar la agregación
        $ageRangeConditions = [];
        foreach ($ageRanges as $range => $ages) {
            $ageRangeConditions[] = "WHEN EXTRACT(YEAR FROM AGE(fecha_nacimiento)) BETWEEN {$ages[0]} AND {$ages[1]} THEN '{$range}'";
        }
        $ageRangeSQL = "CASE " . implode(" ", $ageRangeConditions) . " ELSE 'unknown' END AS age_range";

        // Fecha actual por defecto si no se proporcionan las variables
        $fechaDesde = $fechaDesde ?: now()->toDateString();
        $fechaHasta = $fechaHasta ?: now()->toDateString();

        // Consultar la base de datos con agregaciones para los participantes y faltantes
        $results = $personasQuery->selectRaw("
                sexo, 
                $ageRangeSQL,
                SUM(CASE WHEN tr.voto = true THEN 1 ELSE 0 END) AS si_participo,
                SUM(CASE WHEN tr.voto = false THEN 1 ELSE 0 END) AS no_participo,
                COUNT(tr.id) AS total_participacion,
                COUNT(tbl_personas.id) - COUNT(tr.id) AS participantes_faltantes
            ")
            ->leftJoin('tb_evento_persona as ep', 'tbl_personas.id', '=', 'ep.persona_id')
            ->leftJoin('tbl_registros as tr', function ($join) use ($fechaDesde, $fechaHasta) {
                $join->on('ep.id', '=', 'tr.evento_persona_id')
                    ->whereDate('tr.created_at', '>=', $fechaDesde)
                    ->whereDate('tr.created_at', '<=', $fechaHasta);
            })
            ->groupBy('sexo', 'age_range','nombre_completo')
            ->get();

        // Procesar los resultados
        foreach ($results as $result) {
            if (isset($counts['Si_Participó'][$result->sexo][$result->age_range])) {
                $counts['Si_Participó'][$result->sexo][$result->age_range] += $result->si_participo;
                $counts['No_Participó'][$result->sexo][$result->age_range] += $result->no_participo;
                $counts['Total_de_Participación'][$result->sexo][$result->age_range] += $result->total_participacion;
                $counts['Participantes_faltantes'][$result->sexo][$result->age_range] += $result->participantes_faltantes;
            }
        }

        // Retornar los resultados en formato JSON
        return response()->json([
            "M" => [
                'Si' => $counts['Si_Participó']['M'],
                'No' => $counts['No_Participó']['M'],
                'faltantes' => $counts['Participantes_faltantes']['M'],
                'Total' => $counts['Total_de_Participación']['M'],
            ],
            "F" => [
                'Si' => $counts['Si_Participó']['F'],
                'No' => $counts['No_Participó']['F'],
                'faltantes' => $counts['Participantes_faltantes']['F'],
                'Total' => $counts['Total_de_Participación']['F'],
            ]
        ]);
    }
}