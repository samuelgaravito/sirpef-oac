<?php

namespace App\Http\Services\Record;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\EventoPersona;
use App\Http\Controllers\PersonaAutorizadaController; // Importar el controlador
use App\Models\Persona; // Asegúrate de importar el modelo Persona

class ObtenerCedulaAutorizadoService
{
    public static function obtenerCedulaAutorizado($cedula)
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

                'nombre_auth' => $personaData['autorizada_nombre'],
                'cedula_auth' => $personaData['autorizada_cedula'],
                'telefono_auth' => $personaData['autorizada_telefono'],
                'estatus' => $personaData['autorizada_estatus'],
            

                //'estado' => optional($persona->parroquia->municipio->estado)->estado ?: 'No especificado',
                //'municipio' => optional($persona->parroquia->municipio)->municipio ?: 'No especificado',
                //'parroquia' => optional($persona->parroquia)->parroquias ?: 'No especificado',
      
                'registro_existente' => $existeRegistro,
                'estatus_evento' => $eventoPersona->estatus, // Agregar el estatus del evento
            ];

            // Agregar las respuestas a la respuesta final
            $respuestaFinal[] = $respuesta;
        }

        return response()->json($respuestaFinal);
    } 

    
}
