<?php

namespace App\Http\Services\Const;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\EventoPersona;
use App\Models\TipoEmpleado;
use App\Models\Evento;
use App\Models\Registro;
use App\Http\Controllers\ConstController;

class checkpdfFeDeVida
{
    static public function checkpdf($cedula)
    {

        /* ESTA ES LA DEL FORMULARIO EL EXTERIOR */
        
        // Obtener el usuario autenticado
       // $user = auth()->user();

        $persona = Persona::where('cedula', str_replace('.', '', $cedula))->first();
        
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
        $tipoEmpleado = TipoEmpleado::where('id', $persona->tipo_empleado_id)->first(); 


        // Verificar si la persona está asociada al evento activo
      
        $eventoPersona = EventoPersona::join('tb_evento', 'tb_evento.id', '=', 'tb_evento_persona.evento_id')
        ->where('tb_evento.estatus', true) // Validar que el estatus del evento sea true
        ->join('tbl_registros', 'tb_evento_persona.id', 'tbl_registros.evento_persona_id')
        ->orderBy('tb_evento_persona.created_at', 'desc') // Ordenar por fecha de creación en orden descendente
        ->select('tb_evento_persona.*', 'tbl_registros.voto') // Selecciona los campos que necesites de EventoPersona
        ->first(); // Obtener el último registro

        // solo valida que el evento de $eventoPersona este activo

    if (!$eventoPersona) {
        return response()->json([
            'msg' => $eventoPersona
        ], 404);
    }

    // Verificar si hay registros asociados a este evento_persona
    $existeRegistro = $eventoPersona->registros()->exists();

    if($eventoPersona->voto != true) return response()->json(['msg' => 'La solicitud no ha sido aprobada']);

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


        $persona->tipo_empleado = $tipoEmpleado ? $tipoEmpleado->tipo : 'Sin información';
        $persona->nombrepais = $persona->pais ? $persona->pais->pais : 'Sin información';
        $persona->fecha_registro = $existeRegistro ? $eventoPersona->created_at : 'Sin información';

        $respuesta4 = [
             $persona
        ];

        return response()->json($respuesta4);
    }
}

