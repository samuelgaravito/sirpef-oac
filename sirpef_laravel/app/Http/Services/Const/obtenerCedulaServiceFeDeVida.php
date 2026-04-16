<?php

namespace App\Http\Services\Const;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\EventoPersona;
use App\Models\Evento;
use App\Models\Registro;
use App\Http\Controllers\ConstController;

class obtenerCedulaServiceFeDeVida
{
    static public function obtenerPorCedulaFeDeVida($cedula)
    {

        /* ESTA ES LA DEL FORMULARIO EL EXTERIOR */
        
        // Obtener el usuario autenticado
       // $user = auth()->user();

        $persona = Persona::where('cedula', str_replace('.', '', $cedula))->first();
/*
        // Filtrar personas basadas en el evento activo y ministerio_id
        $ConstController = new ConstController();
        $personasFiltradas = $ConstController->obtenerPersonas();

        // Buscar la persona por cédula dentro de las personas filtradas
        $persona = $personasFiltradas->firstWhere('cedula', $cedula);
*/
        if (!$persona) {
            return response()->json([
                'msg' => 'El número de cédula que está ingresando no se encuentra, verifica el número de cédula o verifica si participa en el evento.'
            ], 404);
        }

        if($persona->tipo_empleado_id == '7') {
            return response()->json([
                'msg' => 'Solo son permitidos los jubilados o pensionados'
            ], 404);
        }

        $personaEvento = EventoPersona::where('persona_id', $persona->id)->first(); 

        // AQUI FALTA LA VERIFICACION DE QUE SI EL EVENTO ESTA ACTIVO
        
    // Obtener los registros del evento activo
$registro = Registro::where('evento_persona_id', $personaEvento->id)->first();

$msg = '';
/*
if($registro) {

    if ($registro->voto === null) {
        return response()->json([
            'msg' => 'Su Solicitud está Pendiente por revisión',
            'status' => $registro->voto
        ], 200);
    } elseif ($registro->voto === true) {
        return response()->json([
            'msg' => 'Su Solicitud ha sido Aceptada. Descargue su Comprobante de Fe de vida',
            'status' => $registro->voto
        ], 200);
    } else {
        $msg = 'Su Solicitud ha sido Rechazada. Vuelva a actualizar sus datos';
        
    }
}*/



if($registro) {

    if ($registro->voto === true) {
        return response()->json([
            'msg' => 'Su Solicitud ha sido Aceptada. Descargue su Comprobante de Fe de vida',
            'status' => $registro->voto
        ], 200); 
}

}

return response()->json([
    'msg' => 'El número de cédula que está ingresando no se encuentra',
], 200);



        // Verificar si la persona está asociada al evento activo
      
        $eventoPersona = EventoPersona::join('tb_evento', 'tb_evento.id', '=', 'tb_evento_persona.evento_id')
            ->where('tb_evento.estatus', true) // Validar que el estatus del evento sea true
            ->orderBy('tb_evento_persona.created_at', 'desc') // Ordenar por fecha de creación en orden descendente
            ->select('tb_evento_persona.*') // Selecciona los campos que necesites de EventoPersona
            ->first(); // Obtener el último registro

            // solo valida que el evento de $eventoPersona este activo

        if (!$eventoPersona) {
            return response()->json([
                'msg' => $eventoPersona
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
            'cargo' => $persona->cargo ? $persona->cargo : 'No especificado',
            'telefono' => $persona->telefono ? $persona->telefono : 'No especificado',
        ];

        $respuesta2 = [
            'Ubicación' =>  $persona->parroquia ? "{$persona->parroquia->municipio->estado->estado} - {$persona->parroquia->municipio->municipio} - {$persona->parroquia->parroquias}"  : 'No especificado',
            'direccion' => $persona->direccion,
            'correo' => $persona->correo_electronico ? $persona->correo_electronico : 'Sin correo',
            'Empleado' => $persona->tipo_empleado ? $persona->tipo_empleado : 'Sin información',
           

        ];

        $respuesta3 = [
            'registro_existente' => $existeRegistro
        ];

        $persona->estado_id = $persona->parroquia ? $persona->parroquia->municipio->estado->id : 0;
        $persona->municipio_id = $persona->parroquia ? $persona->parroquia->municipio->id : 0;
        $persona->parroquia_id = $persona->parroquia ? $persona->parroquia->id : 0;
        $persona->localizacion = $persona->parroquia ? "{$persona->parroquia->municipio->estado->estado} - {$persona->parroquia->municipio->municipio} - {$persona->parroquia->parroquias}"  : 'No especificado';


        $persona->tipo_empleado = $persona->tipo_empleado ? $persona->tipo_empleado : 'Sin información';
        $persona->nombrepais = $persona->pais ? $persona->pais->pais : 'Sin información';
        $persona->fecha_registro = $existeRegistro ? $eventoPersona->created_at : 'Sin información';

        $respuesta4 = [
             $persona
        ];

        return response()->json([
            'data' => $respuesta4,
            'msg' => $msg,
            'status' =>false,
            'registro' => $personaEvento->id
        ]);
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