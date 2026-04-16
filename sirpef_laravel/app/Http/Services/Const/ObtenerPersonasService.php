<?php

namespace App\Http\Services\Const;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Persona;
use App\Models\Ministerio;
use Log;

class ObtenerPersonasService
{
    static public function obtenerPersonas($data = null)
    {
        // Si $data no es null, usaremos sus valores en lugar de los del usuario autenticado
        $user = auth()->user();
        $personas = Persona::query();

        // Realizar un join con la tabla de tipo_empleado
        $personas->join('tb_tipo_empleados', 'tbl_personas.tipo_empleado_id', '=', 'tb_tipo_empleados.id')
            ->select('tbl_personas.*', 'tb_tipo_empleados.tipo as tipo_empleado'); // Selecciona los campos que necesites

        // Si $data está presente, se utiliza para filtrar
        if ($data !== null) {
            $personas = self::filtrarPorParticipacion($personas, $data);
        } else if ($user && $user->is_admin) {
            $personas = self::filtrarPorAdministrador($personas, $user);
        } else {
            $personas = self::filtrarPorUsuarioNoAdministrador($personas, $user);
        }
        return $personas; // Asegurarse de devolver una colección de personas
    }

    static private function filtrarPorAdministrador($personas, $user)
    {
        if (is_null($user->configUser->evento_activo) && is_null($user->configUser->oficina_asignada) && is_null($user->configUser->unid_activa)) {
            return $personas; // Si no hay filtros, devolver todas las personas
        }

        // Filtrar por evento activo si existe
        $personas = self::filtrarPorEventoActivo($personas, $user, 'ministerio_id');
        // Filtrar por unidad activa si existe
        $personas = self::filtrarPorUnidadActiva($personas, $user, 'ministerio_id');
        // Filtrar por oficina asignada si existe
        $personas = self::filtrarPorOficinaAsignada($personas, $user, 'ministerio_id');

        return $personas;
    }

    static private function filtrarPorUsuarioNoAdministrador($personas, $user)
    {
        $personas = self::filtrarPorEventoActivo($personas, $user, 'ministerio_id');
        $personas = self::filtrarPorUnidadActiva($personas, $user, 'ministerio_id');
        $personas = self::filtrarPorOficinaAsignada($personas, $user, 'ministerio_id');
        return $personas;
    }

    static private function filtrarPorParticipacion($personas, $data)
    {
        // Filtrar por evento activo en $data si existe
        $personas = self::filtrarPorEventoActivo($personas, $data, 'ministerio_id');
        // Filtrar por unidad activa en $data si existe
        $personas = self::filtrarPorUnidadActiva($personas, $data, 'ministerio_id');
        // Filtrar por oficina asignada en $data si existe
        $personas = self::filtrarPorOficinaAsignada($personas, $data, 'ministerio_id');

        return $personas;
    }

    static private function filtrarPorEventoActivo($personas, $user, $ministerioIdColumn)
    {
        try {
            if (!empty($user->configUser->evento_activo)) {
                $eventoId = $user->configUser->evento_activo;
                $personas->whereExists(function ($query) use ($eventoId, $user, $ministerioIdColumn) {
                    $query->select('persona_id')
                        ->from('tb_evento_persona')
                        ->where('evento_id', $eventoId)
                        ->whereColumn('persona_id', 'tbl_personas.id');

                    if (!empty($user->configUser->oficina_asignada)) {
                        $oficinaAsignada = json_decode($user->configUser->oficina_asignada, true);
                        $query->whereIn($ministerioIdColumn, $oficinaAsignada);
                    }
                });
            }
        } catch (\Exception $e) {
            Log::error("Error en filtrarPorEventoActivo: " . $e->getMessage());
            return $personas;
        }
        return $personas;
    }

    static private function filtrarPorUnidadActiva($personas, $user, $ministerioIdColumn)
{
    try {
        // Si unid_activa es [0], devolver todas las personas
        $unidActiva = json_decode($user->configUser ->unid_activa, true);
        if (is_array($unidActiva) && count($unidActiva) === 1 && $unidActiva[0] === 0) {
            return $personas; // Devolver todas las personas
        }

        if (!empty($unidActiva)) {
            // Obtener los ministerios del usuario
            $ministerios = Ministerio::whereIn('id', $unidActiva)->get();
            $ministerioIds = $ministerios->pluck('id')->toArray();
    
            // Añadir herencia para incluir todos los ministerios hijos
            foreach ($ministerios as $ministerio) {
                $todosLosHijos = [$ministerio->id];
                $hijosPendientes = [$ministerio->id];
    
                // Recursivamente obtener todos los hijos
                while (!empty($hijosPendientes)) {
                    $hijoId = array_shift($hijosPendientes);
                    $hijos = Ministerio::where('ministerio_padre_id', $hijoId)->pluck('id')->toArray();
    
                    $todosLosHijos = array_merge($todosLosHijos, $hijos);
                    $hijosPendientes = array_merge($hijosPendientes, $hijos);
                }
    
                // Unir los IDs de los ministerios hijos
                $ministerioIds = array_merge($ministerioIds, $todosLosHijos);
            }
    
            // Filtrar las personas por todos los ministerios, incluyendo los hijos
            $personas->whereIn($ministerioIdColumn, $ministerioIds);
        } elseif (empty($unidActiva)) {
            return $personas;
        }
    } catch (\Exception $e) {
        Log::error("Error en filtrarPorUnidadActiva: " . $e->getMessage());
        return $personas;
    }

    return $personas;
}

    static private function filtrarPorOficinaAsignada($personas, $user, $ministerioIdColumn)
    {
        try {
            if (!empty($user->configUser->oficina_asignada)) {
                $oficinaAsignada = json_decode($user->configUser->oficina_asignada, true);
                $personas->whereIn($ministerioIdColumn, $oficinaAsignada);
            }
        } catch (\Exception $e) {
            Log::error("Error en filtrarPorOficinaAsignada: " . $e->getMessage());
            return $personas;
        }

        return $personas;
    }



    static public function findByCedula($cedula)
    {
        // Realizar la consulta con join
        $persona = DB::table('tbl_personas')
            ->leftJoin('tb_tipo_empleados', 'tbl_personas.tipo_empleado_id', '=', 'tb_tipo_empleados.id')
            ->leftJoin('tbl_parroquias', 'tbl_personas.parroquia_id', '=', 'tbl_parroquias.id')
            ->leftJoin('tbl_municipio', 'tbl_parroquias.municipio_id', '=', 'tbl_municipio.id')
            ->leftJoin('tbl_estados', 'tbl_municipio.estado_id', '=', 'tbl_estados.id')
            ->select('tbl_personas.nombre_completo', 'tbl_personas.correo_electronico', 'tbl_personas.cedula', 'tbl_personas.direccion', 'tbl_personas.telefono', 'tbl_personas.ministerio_id',
            'tbl_personas.pais_id', 'tbl_personas.id', 'tbl_estados.id as estado_id',
            'tbl_municipio.id as municipio_id', 
            'tbl_parroquias.id as parroquia_id', 
            'tb_tipo_empleados.id as tipo_empleado_id',
            'tb_tipo_empleados.tipo as tipo_empleado',
            'tbl_personas.causa_pension as causa_pension'   
            )
            ->where('tbl_personas.cedula', $cedula)
            ->first();

        // Verificar si se encontró la persona
        if ($persona) {
            return response()->json($persona, 200); // Retornar la información de la persona
        } else {
            return response()->json(['message' => 'Persona no encontrada'], 404); // Retornar un mensaje de error
        }
    }
}