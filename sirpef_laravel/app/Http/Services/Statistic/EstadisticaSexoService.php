<?php

namespace App\Http\Services\Statistic;

use App\Http\Services\Const\ObtenerPersonasService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EstadisticaSexoService
{
    static public function GetResumenDataBySex($fechaDesde = null, $fechaHasta = null)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Utilizamos ObtenerPersonasService para obtener las personas filtradas
        $personasQuery = ObtenerPersonasService::obtenerPersonas();

        // Conteo total de personas
        $countTotal = $personasQuery->count();

        // Función para aplicar filtros de fecha a los registros
        $applyDateFilters = function ($query) use ($fechaDesde, $fechaHasta) {
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            }
        };

        // Consultas para contar la participación por sexo y voto
        $getParticipationCount = function ($sexo, $voto = null) use ($personasQuery, $applyDateFilters) {
            $query = clone $personasQuery;
            $query->where('sexo', $sexo);
            if (!is_null($voto)) {
                $query->whereHas('registrosEventoActivo', function ($subQuery) use ($voto, $applyDateFilters) {
                    $subQuery->where('voto', $voto);
                    $applyDateFilters($subQuery);
                });
            } else {
                $query->whereHas('registrosEventoActivo', function ($subQuery) use ($applyDateFilters) {
                    $applyDateFilters($subQuery);
                });
            }
            return $query->count();
        };

        // Contar participación por sexo y voto
        $totalVotosMasculino = $getParticipationCount('M');
        $totalVotosFemenino = $getParticipationCount('F');
        $totalVotoTrueMasculino = $getParticipationCount('M', true);
        $totalVotoTrueFemenino = $getParticipationCount('F', true);
        $totalVotoFalseMasculino = $getParticipationCount('M', false);
        $totalVotoFalseFemenino = $getParticipationCount('F', false);

        // Participantes faltantes por sexo
        $getMissingParticipationCount = function ($sexo) use ($personasQuery, $fechaDesde, $fechaHasta) {
            $query = clone $personasQuery;
            $query->where('sexo', $sexo);
            if ($fechaDesde && $fechaHasta) {
                return $query->whereDoesntHave('registrosEventoActivo', function ($subQuery) use ($fechaDesde, $fechaHasta) {
                    $subQuery->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                             ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                })->count();
            } else {
                return $query->doesntHave('registrosEventoActivo')->count();
            }
        };

        $totalVotoFaltanteMasculino = $getMissingParticipationCount('M');
        $totalVotoFaltanteFemenino = $getMissingParticipationCount('F');

        // Devolver el resultado en formato JSON
        return response()->json([
            'M'=>[  
                'Si' => $totalVotoTrueMasculino, 
                'No' => $totalVotoFalseMasculino, 
                'faltantes' => $totalVotoFaltanteMasculino,
            ],
            'F'=>[
                'Si' => $totalVotoTrueFemenino,
                'No' => $totalVotoFalseFemenino,
                'faltantes' => $totalVotoFaltanteFemenino,
            ],
            'Total_de_Personal' => $countTotal,
        ]);
    }
}