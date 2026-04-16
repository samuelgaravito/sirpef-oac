<?php

namespace App\Http\Services\Statistic;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\UnidadAdscrita;
use App\Models\Persona;


class ConteoGeneroService
{


    //funcion para el calculo estadistico de participacion por genero
    static public function contarPersonasPorGenero() 
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
    
        $tipoIds = [
            '0.1' => 1,
            '0.2' => 2,
            '0.3' => 3,
            '0.4' => 4
        ];
    
        $enteIds = [
            '0.5' => 1,
            '0.6' => 2,
            '0.7' => 3,
            '0.8' => 4,
            '0.9' => 5
        ];
    
        $unidActiva = (string) $user->unid_activa;
        $isAdmin = $user->isAdmin();
    
        $filtroUnidadAdscrita = function ($query) use ($tipoIds, $enteIds, $unidActiva, $user, $isAdmin) {
            if ($isAdmin) {
                if ($unidActiva == '0') {
                    // No filter needed
                } elseif (isset($tipoIds[$unidActiva])) {
                    $query->where('tipo_id', $tipoIds[$unidActiva]);
                } elseif (isset($enteIds[$unidActiva])) {
                    $query->where('entes_id', $enteIds[$unidActiva]);
                } else {
                    $query->where('id', $unidActiva);
                }
            } else {
                if ($user->role_id == 2 && $unidActiva == '0') {
                    $unidadesAdscritas = UnidadAdscrita::whereHas('personas', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })->pluck('id');
                    $query->whereIn('id', $unidadesAdscritas);
                } else {
                    $query->where('id', $unidActiva)
                          ->orWhere('entes_id', $user->entes_id);
                }
            }
        };
    
        $personasQuery = Persona::with('unidadAdscrita')
                                ->whereHas('unidadAdscrita', $filtroUnidadAdscrita);
    
        $personas = collect();
        if ($isAdmin || ($user->role_id == 2 || $unidActiva == '0')) {
            $personas = $personasQuery->get();
        } else {
            $personaUsuario = $user->persona()->first();
    
            if (!$personaUsuario) {
                if ($unidActiva == '0') {
                    $unidadesAdscritas = UnidadAdscrita::whereHas('personas', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })->get();
    
                    if ($unidadesAdscritas->count() > 1) {
                        $personas = $personasQuery->whereIn('uni_ads_id', $unidadesAdscritas->pluck('id'))->get();
                    } else {
                        $personas = $personasQuery->whereHas('unidadAdscrita', function ($query) use ($unidActiva) {
                            $query->where('id', $unidActiva);
                        })->get();
                    }
                } else {
                    $personas = $personasQuery->get();
                }
            } else {
                $personas = collect([$personaUsuario]);
    
                $otrasPersonas = $personasQuery->whereHas('unidadAdscrita', function ($query) use ($personaUsuario) {
                    $query->where('codigo', $personaUsuario->unidadAdscrita->codigo)
                          ->where('ente_id', $personaUsuario->entes_id);
                })->get();
    
                $personas = $personas->merge($otrasPersonas)->unique('id');
            }
        }
    
        $totalMasculino = $personas->where('sexo', 'M')->count();
        $totalFemenino = $personas->where('sexo', 'F')->count();
    
        return response()->json([
            'total_masculino' => $totalMasculino,
            'total_femenino' => $totalFemenino
        ]);
    }


}