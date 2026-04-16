<?php

namespace App\Http\Services\Record;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\EventoPersona;
use App\Http\Controllers\ConstController;

class ObtenerCedulaService
{
    public static function obtenerPorCedula($cedula)
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Filtrar personas basadas en el evento activo y ministerio_id
        $ConstController = new ConstController();
        $personasFiltradas = $ConstController->obtenerPersonas();

        // Buscar la persona por cédula dentro de las personas filtradas
        $persona = $personasFiltradas->firstWhere('cedula', $cedula);

        if (!$persona) {
            return response()->json([
                'msg' => 'El número de cédula que está ingresando no se encuentra en su base de datos, verifica el número de cédula o verifica si participa en el evento.'
            ], 404);
        }

        // Verificar si la persona está asociada al evento activo
        $eventoPersona = EventoPersona::where('persona_id', $persona->id)
            ->where('evento_id', $user->configUser->evento_activo)
            ->first();

        if (!$eventoPersona) {
            return response()->json([
                'msg' => 'La persona no está asociada al evento activo.'
            ], 404);
        }

        // Verificar si hay registros asociados a este evento_persona
        $existeRegistro = $eventoPersona->registros()->exists();

        // Preparar las respuestas
        $respuesta = [
            'id' => $persona->id,
            'nombre_completo' => $persona->nombre_completo,
            'cedula' => $persona->cedula,
            'organismo' => $persona->ministerio ? $persona->ministerio->nombre : 'No especificado',
            'telefono' => $persona->telefono ? $persona->telefono : 'No especificado',
        ];

        $respuesta2 = [
            'Ubicación' =>  $persona->parroquia ? "{$persona->parroquia->municipio->estado->estado} - {$persona->parroquia->municipio->municipio} - {$persona->parroquia->parroquias}"  : 'No especificado',
            'direccion' => $persona->direccion,
            'correo' => $persona->correo_electronico ? $persona->correo_electronico : 'Sin correo',
            'Empleado' => $persona->tipo_empleado ? $persona->tipo_empleado : 'Sin información',
           

        ];

        $respuesta3 = [
            'registro_existente' => $existeRegistro,
            'registro_fe' => count($eventoPersona->registros) > 0 ? $eventoPersona->registros[0]->voto : null
        ];

        $persona->estado_id = $persona->parroquia ? $persona->parroquia->municipio->estado->id : 0;
        $persona->municipio_id = $persona->parroquia ? $persona->parroquia->municipio->id : 0;
        $persona->parroquia_id = $persona->parroquia ? $persona->parroquia->id : 0;
        $persona->localizacion = $persona->parroquia ? "{$persona->parroquia->municipio->estado->estado} - {$persona->parroquia->municipio->municipio} - {$persona->parroquia->parroquias}"  : 'No especificado';
        $persona->telefono_2 = $persona->telefono_2 != null ? $persona->telefono_2 : '';
 

        $persona->tipo_empleado = $persona->tipo_empleado ? $persona->tipo_empleado : 'Sin información';
        $persona->nombrepais = $persona->pais ? $persona->pais->pais : 'Sin información';
        $persona->fecha_registro = $existeRegistro ? $eventoPersona->created_at : 'Sin información';

        $respuesta4 = [
             $persona
        ];
        

        return response()->json([$respuesta, $respuesta2, $respuesta3, $respuesta4]);
    }
}




/* 
<?php

namespace App\Http\Services\Record;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\EventoPersona;
use App\Http\Controllers\PersonaAutorizadaController; // Importar el controlador
use App\Models\Persona; // Asegúrate de importar el modelo Persona

class ObtenerCedulaService
{
    public static function obtenerPorCedula($cedula)
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Crear una instancia del controlador
        $personaAutorizadaController = new PersonaAutorizadaController();

        // Obtener las personas autorizadas
        $response = $personaAutorizadaController->obtenerPersonasAutorizadas(new Request());

        // Convertir la respuesta a un array
        $personasAutorizadas = json_decode($response->getContent(), true);

        // Filtrar las personas autorizadas que coincidan con la cédula
        $personasCoincidentes = collect($personasAutorizadas)->filter(function ($item) use ($cedula) {
            return $item['persona_cedula'] == $cedula || $item['autorizada_cedula'] == $cedula;
        });

        if ($personasCoincidentes->isEmpty()) {
            return response()->json([
                'msg' => 'El número de cédula que está ingresando no se encuentra en su base de datos, verifica el número de cédula o verifica si participa en el evento.'
            ], 404);
        }

        // Preparar la respuesta
        $respuestaFinal = [];
        foreach ($personasCoincidentes as $personaData) {
            // Obtener la persona completa usando la función obtenerPersona
            $persona = Persona::with(['parroquia.municipio.estado', 'centro', 'ente', 'unidadAdscrita']) // Asegúrate de cargar las relaciones necesarias
                ->find($personaData['persona_id']);

            // Verificar si la persona está asociada al evento activo
            $eventoPersona = EventoPersona::where('persona_id', $persona->id)
                ->where('evento_id', $user->configUser ->evento_activo)
                ->first();

            // Si no está asociada, saltar a la siguiente
            if (!$eventoPersona) {
                continue;
            }

            // Verificar si hay registros asociados a este evento_persona
            $existeRegistro = $eventoPersona->registros()->exists();

            // Preparar las respuestas
            $respuesta = [
                'id' => $persona->id,
                'nombre_completo' => $persona->nombre_completo,
                'cedula' => $persona->cedula,
                'ministerio' => $persona->ministerio->nombre ?: 'No especificado',
                'telefono' => $persona->telefono ?: 'No especificado',
            ];

            $respuesta2 = [
                'estado' => optional($persona->parroquia->municipio->estado)->estado ?: 'No especificado',
                'municipio' => optional($persona->parroquia->municipio)->municipio ?: 'No especificado',
                'parroquia' => optional($persona->parroquia)->parroquias ?: 'No especificado',
            ];

            $respuesta3 = [
                'registro_existente' => $existeRegistro,
                'autorizada' => [
                    'id' => $personaData['autorizada_id'],
                    'nombre_completo' => $personaData['autorizada_nombre'],
                    'cedula' => $personaData['autorizada_cedula'],
                    'telefono' => $personaData['autorizada_telefono'],
                    'estatus' => $personaData['autorizada_estatus'],
                ],
                'estatus_evento' => $eventoPersona->estatus, // Agregar el estatus del evento
            ];

            // Agregar las respuestas a la respuesta final
            $respuestaFinal[] = [
                'persona' => $respuesta,
                'detalles' => $respuesta2,
                'registro' => $respuesta3
            ];
        }

        return response()->json($respuestaFinal);
    } 

    
} */