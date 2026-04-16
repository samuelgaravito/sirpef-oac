<?php

namespace App\Http\Services\Statistic;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use Carbon\Carbon;
use App\Http\Services\Const\ObtenerPersonasService;

class FiltroPersonaEstadoService  
{
    /**
     * Filtra personas por estado y estado de voto (voto_status).
     * @param int $estado_id - ID del estado
     * @param int $voto_status - Estado de voto: 0 (no participó), 1 (sí participó), 2 (total participación), 3 (faltantes)
     * @param string|null $fechaDesde - Fecha inicial para el filtro (opcional)
     * @param string|null $fechaHasta - Fecha final para el filtro (opcional)
     * @return JsonResponse - Resultado de la consulta en formato JSON
     */
    static public function filterPersonasXEstado($estado_id, $voto_status, $fechaDesde = null, $fechaHasta = null) {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Obtener las personas basadas en la participación
        $personasQuery = ObtenerPersonasService::obtenerPersonas($user);

        $personasQuery->whereHas('parroquia.municipio.estado', function ($query) use ($estado_id) {
            $query->where('id', $estado_id);
        });

        // Aplicar el filtro por estado del voto (0: No participó, 1: Sí participó, 2: Total participación, 3: Faltantes)
        switch ($voto_status) {
            case 0: // No participó (voto false)
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->where('voto', false);
                    if ($fechaDesde && $fechaHasta) {
                        // Filtrar por rango de fechas si se proporciona
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        // Filtrar por la fecha actual si no se proporciona un rango
                        $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                    }
                });
                break;

            case 1: // Sí participó (voto true)
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->where('voto', true);
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                    }
                });
                break;

            case 2: // Total de participación (con votos registrados)
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                    }
                });
                break;

            case 3: // Faltantes de participación (sin votos registrados)
                $personasQuery->whereDoesntHave('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', date('Y-m-d'));
                    }
                });
                break;

            default:
                return response()->json(['message' => 'Estado de voto inválido'], 400);
        }

        // Obtener los resultados finales con los campos requeridos
        $personas = $personasQuery->select(
            'tbl_personas.id', 
            'nombre_completo', 
            'cedula', 
            'correo_electronico', 
            'cargo', 
            'telefono', 
            'tipo_empleado_id' // Agregar este campo
        )->with(['tipoEmpleado', 'registrosEventoActivo']) // Cargar la relación tipoEmpleado y registrosEventoActivo
        ->get();
        

        // Devolver la información de las personas incluyendo el tipo de empleado
        return response()->json($personas->map(function ($persona) {

            $registroActivo = $persona->registrosEventoActivo->first();
            $voto = $registroActivo ? $registroActivo->voto : null;
            $descripcion = $voto == true ? $registroActivo->descripcion : null; // Obtener la descripción si el voto es true
            

            return [
                'id' => $persona->id,
                'nombre_completo' => $persona->nombre_completo,
                'cedula' => $persona->cedula,
                'correo_electronico' => $persona->correo_electronico,
                'cargo' => $persona->cargo,
                'telefono' => $persona->telefono,
                'tipo_empleado_id' => $persona->tipo_empleado_id,
                'tipo_empleado' => $persona->tipoEmpleado ? $persona->tipoEmpleado->tipo : null, // Obtener el tipo de empleado
                'descripcion' => $descripcion
            ];
        }));
    }
}