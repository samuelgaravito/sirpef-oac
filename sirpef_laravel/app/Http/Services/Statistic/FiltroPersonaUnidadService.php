<?php

namespace App\Http\Services\Statistic;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\Ministerio;
use App\Models\TipoCaso; // Importar el modelo TipoCaso
use Carbon\Carbon;
use App\Http\Services\Const\ObtenerPersonasService;

class FiltroPersonaUnidadService  
{
    /**
     * Filtra personas por unidad, estado de voto y tipo de caso.
     *
     * @param int $unid El ID de la unidad (ministerio). 0 para todas las unidades.
     * @param string $voto_status El estado del voto ('a', 'b', 'c', etc.).
     * @param int|null $tipoCasoId El ID del tipo de caso para filtrar. Null para todos.
     * @param string|null $fechaDesde Fecha de inicio para el rango de registros.
     * @param string|null $fechaHasta Fecha de fin para el rango de registros.
     * @return JsonResponse
     */
    static public function filterPersonasXUnid($unid, $voto_status, $tipoCasoId = null, $fechaDesde = null, $fechaHasta = null)
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Inicializar la consulta base de personas
        $personasQuery = null;

        // Si $unid es 0, obtener todas las personas
        if ($unid == 0) {
            // Obtener todas las personas sin filtros de unidad
            $personasQuery = ObtenerPersonasService::obtenerPersonas($user);
        } else {
            // Obtener todos los ministerios hijos (herencia) a partir del ID de unidad
            $MyUnid = json_decode($user->configUser->unid_activa, true);
            $todosLosMinisterios = array_map('intval', self::obtenerTodosLosMinisteriosHijos($unid ?: $MyUnid[0]));

            // Actualizar la configuración del usuario para aplicar el filtro de unidad
            $user->configUser->oficina_asignada = json_encode($todosLosMinisterios);
            $user->configUser->unid_activa = json_encode($todosLosMinisterios);

            // Obtener las personas basadas en los filtros de unidad
            $personasQuery = ObtenerPersonasService::obtenerPersonas($user);
        }
        
        // --- Aplicar filtro por Tipo de Caso ---
        if ($tipoCasoId !== null && $tipoCasoId > 0) {
            $todosLosTiposDeCasoHijos = self::obtenerTodosLosTiposDeCasoHijos($tipoCasoId);

            $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($todosLosTiposDeCasoHijos) {
                $query->whereIn('id_tipo_caso', $todosLosTiposDeCasoHijos);
            });
        }

        // Aplicar condiciones según el voto_status (estatus_caso)
        switch ($voto_status) {

            case 'a': // Estatus: Orientado
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->where('estatus_caso', 'Orientado');
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', Carbon::today()->toDateString());
                    }
                });
                break;

            case 'b': // Estatus: En Tramite
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->where('estatus_caso', 'En Tramite');
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', Carbon::today()->toDateString());
                    }
                });
                break;

            case 'c': // Estatus: Resultado Directo
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->where('estatus_caso', 'Resultado Directo');
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', Carbon::today()->toDateString());
                    }
                });
                break;

            case 'd': // Estatus: Remitido a Otro
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->where('estatus_caso', 'Remitido a Otro');
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', Carbon::today()->toDateString());
                    }
                });
                break;

            case 'e': // Estatus: Cerrado
                $personasQuery->whereHas('registrosEventoActivo', function ($query) use ($fechaDesde, $fechaHasta) {
                    $query->where('estatus_caso', 'Cerrado');
                    if ($fechaDesde && $fechaHasta) {
                        $query->whereDate('tbl_registros.created_at', '>=', $fechaDesde)
                              ->whereDate('tbl_registros.created_at', '<=', $fechaHasta);
                    } else {
                        $query->whereDate('tbl_registros.created_at', '=', Carbon::today()->toDateString());
                    }
                });
                break;

            default:
                // Si el estatus de caso es 'todos', no se aplica un filtro de estatus de caso
                // Si no es un estatus válido, se devuelve un error 400
                if ($voto_status !== 'todos') {
                     return response()->json(['message' => 'Estado de caso inválido'], 400);
                }
                break;
        }

        $personas = $personasQuery->select(
            'tbl_personas.id', 
            'nombre_completo', 
            'cedula', 
            'correo_electronico', 
            'cargo', 
            'telefono', 
            'tipo_empleado_id'
        )->with(['tipoEmpleado', 'registrosEventoActivo.tipoCaso']) // Cargar la relación tipoCaso en registros
        ->get();
        
        // Devolver la información de las personas
        return response()->json($personas->map(function ($persona) {
            // Obtener el registro activo, si existe
            $registroActivo = $persona->registrosEventoActivo->first();
            $tipoCaso = $registroActivo && $registroActivo->tipoCaso ? $registroActivo->tipoCaso->tipo : 'Sin info';
            $descripcion = $registroActivo && $registroActivo->descripcion ? $registroActivo->descripcion : 'Sin info';
            $estatusCaso = $registroActivo ? $registroActivo->estatus_caso : 'Sin info'; // Obtener el estatus_caso

            return [
                'id' => $persona->id,
                'nombre_completo' => $persona->nombre_completo,
                'cedula' => $persona->cedula,
                'correo_electronico' => $persona->correo_electronico,
                'cargo' => $persona->cargo,
                'telefono' => $persona->telefono,
                'tipo_empleado_id' => $persona->tipo_empleado_id,
                'tipo_empleado' => $persona->tipoEmpleado ? $persona->tipoEmpleado->tipo : null,
                'tipo_caso' => $tipoCaso, // Agregar el nombre del tipo de caso
                'descripcion' => $descripcion,
                'estatus_caso' => $estatusCaso,
                'punto_n' => $registroActivo->puntoCuenta ? $registroActivo->puntoCuenta->numero_punto : 'Sin punto', // Agregar el número de punto
                'registro_id' => $registroActivo ? $registroActivo->id : null,
            ];
        }));
    }

    /**
     * Obtiene todos los ministerios hijos recursivamente a partir del ID proporcionado.
     *
     * @param int $unid El ID del ministerio padre.
     * @return array
     */
    static private function obtenerTodosLosMinisteriosHijos($unid)
    {
        $ministerioIds = [$unid];
        $hijosPendientes = [$unid];

        while (!empty($hijosPendientes)) {
            $hijoId = array_shift($hijosPendientes);
            $hijos = Ministerio::where('ministerio_padre_id', $hijoId)->pluck('id')->toArray();

            $ministerioIds = array_merge($ministerioIds, $hijos);
            $hijosPendientes = array_merge($hijosPendientes, $hijos);
        }

        return array_unique($ministerioIds);
    }
    
    /**
     * Obtiene todos los tipos de caso hijos recursivamente a partir del ID proporcionado.
     *
     * @param int $tipoCasoId El ID del tipo de caso padre.
     * @return array
     */
    static private function obtenerTodosLosTiposDeCasoHijos($tipoCasoId)
    {
        // Comenzamos con el tipo de caso proporcionado
        $tipoCasoIds = [$tipoCasoId];
        $hijosPendientes = [$tipoCasoId];

        // Iterar para obtener todos los tipos de caso hijos recursivamente
        while (!empty($hijosPendientes)) {
            $hijoId = array_shift($hijosPendientes);
            $hijos = TipoCaso::where('tipo_caso_padre_id', $hijoId)->pluck('id')->toArray();

            $tipoCasoIds = array_merge($tipoCasoIds, $hijos);
            $hijosPendientes = array_merge($hijosPendientes, $hijos);
        }

        return array_unique($tipoCasoIds);
    }
}