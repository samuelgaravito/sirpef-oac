<?php
namespace App\Http\Services\Statistic;

use App\Http\Services\Const\ObtenerPersonasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Ministerio;
use App\Models\Evento;

class EstadisticaParticipacionFeDeVidaService
{
    static public function GetResumenDataFeDeVida($fechaDesde = null, $fechaHasta = null)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Obtén las personas que cumplen con los criterios
        $personasQuery = ObtenerPersonasService::obtenerPersonas();

        // Conteo total de personas
        $countTotal = $personasQuery->count();
        
        // Filtrar por registros de votos
        $totalVotosQuery = clone $personasQuery;
        $totalVotosQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            } else {
                $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
            }
            // Filtrar por descripción 'formulario'
            $query->where('descripcion', 'Formulario');
        });

        // Filtrar por votos "true"
        $totalVotoTrueQuery = clone $personasQuery;
        $totalVotoTrueQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
            $query->where('voto', true);
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            } else {
                $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
            }
            // Filtrar por descripción 'formulario'
            $query->where('descripcion', 'Formulario');
        });

        // Filtrar por votos "false"
        $totalVotoFalseQuery = clone $personasQuery;
        $totalVotoFalseQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
            $query->where('voto', false);
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            } else {
                $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
            }
            // Filtrar por descripción 'formulario'
            $query->where('descripcion', 'Formulario');
        });

        // Filtrar por votos "null"
        $totalVotoNullQuery = clone $personasQuery;
        $totalVotoNullQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
            $query->whereNull('voto');
            if ($fechaDesde && $fechaHasta) {
                $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                      ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
            } else {
                $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
            }
            // Filtrar por descripción 'formulario'
            $query->where('descripcion', 'Formulario');
        });


          // Filtrar por votos "true"
          $totalVotoTruepresencialQuery = clone $personasQuery;
          $totalVotoTruepresencialQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
              $query->where('voto', true);
              if ($fechaDesde && $fechaHasta) {
                  $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                        ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
              } else {
                  $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
              }
              // Filtrar por descripción 'formulario'
              $query->where('descripcion', 'presencial');
          });


              // Filtrar por votos "true"
              $totalVotoTruecorreoQuery = clone $personasQuery;
              $totalVotoTruecorreoQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                  $query->where('voto', true);
                  if ($fechaDesde && $fechaHasta) {
                      $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                            ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                  } else {
                      $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                  }
                  // Filtrar por descripción 'formulario'
                  $query->where('descripcion', 'correo');
              });


        // Obtener los resultados
        $totalVotos = $totalVotosQuery->count();
        $totalVotoTrue = $totalVotoTrueQuery->count();
        $totalVotoFalse = $totalVotoFalseQuery->count();
        $totalVotoNull = $totalVotoNullQuery->count(); // Contar los votos nulos
        $totalVotoTruepresencial = $totalVotoTruepresencialQuery->count();
        $totalVotoTruecorreo = $totalVotoTruecorreoQuery->count();

        
        // Retornar los resultados
        return [
            ['Aceptados Correo', $totalVotoTruecorreo],
            ['Aceptados Presencial', $totalVotoTruepresencial],
            ['Aceptados Externos', $totalVotoTrue],
            ['Rechazados Externos', $totalVotoFalse],
            ['Pendientes Externos', $totalVotoNull],
          
            
        ];
    }
}