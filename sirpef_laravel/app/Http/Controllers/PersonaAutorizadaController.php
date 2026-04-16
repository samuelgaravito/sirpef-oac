<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Const\ObtenerPersonasService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\PersonaAutorizada;
use App\Models\EventoPersona;
use App\Models\Evento;
use Illuminate\Support\Facades\Storage;

class PersonaAutorizadaController extends Controller
{
    
    public function verificarCedula($cedula)
    {
        // Verificar si la cédula existe en la base de datos
        $personaAutorizada = PersonaAutorizada::where('cedula', $cedula)->first();

        if ($personaAutorizada) {
            // Si existe, retornar la información en formato JSON
            return response()->json($personaAutorizada, 200);
        } else {
            // Si no existe, retornar un mensaje de error
            return response()->json(['message' => 'Persona autorizada no encontrada.'], 404);
        }
    }
 


    public function asignarPersonaAutorizada(Request $request)
{
    // Validar los campos necesarios
    $request->validate([
        'empleado_id' => 'required|integer',
        'evento_id' => 'required|integer',
        'cedula' => 'required|string',
        'nombre_completo' => 'required|string',
        'telefono' => 'required|string',
        'auto_cedu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'emple_cedu' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'carta' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Obtener los valores del request
    $empleadoId = $request->input('empleado_id');
    $eventoId = $request->input('evento_id');
    $cedula = $request->input('cedula');

    // Buscar el registro en EventoPersona usando empleado_id y evento_id
    $eventoPersona = EventoPersona::where('persona_id', $empleadoId)
                                   ->where('evento_id', $eventoId)
                                   ->first();

    // Si no se encuentra el registro en EventoPersona
    if (!$eventoPersona) {
        return response()->json(['message' => 'EventoPersona no encontrado.'], 404);
    }

    // Verificar si la persona autorizada ya existe según la cédula
    $personaAutorizada = PersonaAutorizada::where('cedula', $cedula)->first();

    // Si no existe, crearla
    if (!$personaAutorizada) {
        $personaAutorizada = PersonaAutorizada::create([
            'cedula' => $cedula,
            'nombre_completo' => $request->input('nombre_completo'),
            'telefono' => $request->input('telefono'),
            'estatus' => $request->input('estatus'),
        ]);
    }

    // Actualizar los campos de imagen en EventoPersona
    if ($request->hasFile('auto_cedu')) {
        $imagen1Path = $request->file('auto_cedu')->store('imagenes', 'public');
        $eventoPersona->imagen1 = $imagen1Path;
    }

    if ($request->hasFile('emple_cedu')) {
        $imagen2Path = $request->file('emple_cedu')->store('imagenes', 'public');
        $eventoPersona->imagen2 = $imagen2Path;
    }

    if ($request->hasFile('carta')) {
        $imagen3Path = $request->file('carta')->store('imagenes', 'public');
        $eventoPersona->imagen3 = $imagen3Path;
    }

    // Asignar el ID de PersonaAutorizada a EventoPersona
    $eventoPersona->persona_autorizada_id = $personaAutorizada->id;
    $eventoPersona->tipo = 'autorizado';

    /*
    $eventoPersona->imagen1 = Storage::url($eventoPersona->imagen1);
    $eventoPersona->imagen2 = Storage::url($eventoPersona->imagen2);
    $eventoPersona->imagen3 = Storage::url($eventoPersona->imagen3);cd 
*/
    
    // Guardar los cambios en EventoPersona
    $eventoPersona->save();

    return response()->json(['message' => 'Persona autorizada asignada y EventoPersona actualizado.', 'evento_persona' => $eventoPersona], 200);
}





public function obtenerPersonasConEvento($tipo) {
    // Obtener la consulta de personas
    $personasQuery = ObtenerPersonasService::obtenerPersonas();

    // Realizar un join con la tabla tb_evento_persona, tb_personas_autorizadas y tb_ministerio
    $personasAutorizadas = $personasQuery->join('tb_evento_persona', 'tbl_personas.id', '=', 'tb_evento_persona.persona_id')
        ->join('tb_personas_autorizadas', 'tb_evento_persona.persona_autorizada_id', '=', 'tb_personas_autorizadas.id') // Join con la tabla de personas autorizadas
        ->join('tb_ministerio', 'tbl_personas.ministerio_id', '=', 'tb_ministerio.id') // Join con la tabla de ministerios
        ->whereNotNull('tb_evento_persona.persona_autorizada_id')
        ->where('tb_evento_persona.tipo', $tipo) // Filtrar por tipo directamente
        ->select(
            'tbl_personas.id as persona_id', // Renombrar para evitar conflictos
            'tbl_personas.nombre_completo as persona_nombre', // Renombrar para evitar conflictos
            'tbl_personas.cedula as persona_cedula', // Renombrar para evitar conflictos
            'tbl_personas.telefono as persona_telefono', // Renombrar para evitar conflictos
            'tbl_personas.ministerio_id as ministerio_id', // Incluir ministerio_id
            'tb_ministerio.nombre as ministerio_nombre', // Incluir el nombre del ministerio
            'tb_evento_persona.persona_autorizada_id', 
            'tb_evento_persona.id as registro_evento', 
            'tb_evento_persona.estatus', 
            'tb_evento_persona.imagen1', 
            'tb_evento_persona.imagen2', 
            'tb_evento_persona.imagen3',
            'tb_evento_persona.evento_id', // Eliminar el espacio aquí
            'tb_personas_autorizadas.id as autorizada_id', // Renombrar para evitar conflictos
            'tb_personas_autorizadas.nombre_completo as autorizada_nombre', // Renombrar para evitar conflictos
            'tb_personas_autorizadas.cedula as autorizada_cedula', // Renombrar para evitar conflictos
            'tb_personas_autorizadas.telefono as autorizada_telefono', // Renombrar para evitar conflictos
            'tb_personas_autorizadas.estatus as autorizada_estatus' // Renombrar para evitar conflictos
        )
        ->orderBy('tb_personas_autorizadas.id', 'asc') // Ordenar por nombre completo de manera ascendente
        ->get();

    // Retornar la lista de personas autorizadas en formato JSON
    return response()->json($personasAutorizadas, 200);
}

public function obtenerPersonasFeDeVida($tipo) {
    // Obtener la consulta de personas
    $personasQuery = ObtenerPersonasService::obtenerPersonas();

    // Realizar un join con la tabla tb_evento_persona, tb_personas_autorizadas, tb_ministerio y tbl_registros
    $personasAutorizadas = $personasQuery->join('tb_evento_persona', 'tbl_personas.id', '=', 'tb_evento_persona.persona_id')
        ->join('tb_ministerio', 'tbl_personas.ministerio_id', '=', 'tb_ministerio.id')
        ->leftJoin('tbl_registros', 'tb_evento_persona.id', '=', 'tbl_registros.evento_persona_id') // Join con la tabla de registros
        ->where('tb_evento_persona.tipo', $tipo)
        ->whereNotNull('tb_evento_persona.imagen1') 
        ->whereNotNull('tb_evento_persona.imagen2') 
        ->whereNotNull('tb_evento_persona.imagen3') // Filtrar por tipo directamente
        ->select(
            'tbl_personas.id as persona_id', // Renombrar para evitar conflictos
            'tbl_personas.nombre_completo as persona_nombre', // Renombrar para evitar conflictos
            'tbl_personas.cedula as persona_cedula', // Renombrar para evitar conflictos
            'tbl_personas.telefono as persona_telefono', // Renombrar para evitar conflictos
            'tbl_personas.ministerio_id as ministerio_id', // Incluir ministerio_id
            'tb_ministerio.nombre as ministerio_nombre', // Incluir el nombre del ministerio
            'tb_evento_persona.persona_autorizada_id', 
            'tb_evento_persona.id as registro_evento', 
            'tb_evento_persona.estatus', 
            'tb_evento_persona.imagen1', 
            'tb_evento_persona.imagen2', 
            'tb_evento_persona.imagen3',
            'tb_evento_persona.evento_id', // Eliminar el espacio aquí
            'tbl_registros.voto', // Incluir otros campos del registro si es necesario

        )
        ->get();

    // Retornar la lista de personas autorizadas en formato JSON
    return response()->json($personasAutorizadas, 200);
}


public function obtenerPersonasAutorizadas(Request $request,$tipo) {
    // Obtener el tipo desde la solicitud
    //$tipo = $request->input('tipo');

    // Obtener la lista de personas autorizadas
    if($tipo == 'autorizado') {
        $response = $this->obtenerPersonasConEvento($tipo);
    } else {
        $response = $this->obtenerPersonasFeDeVida($tipo);
    }
    $personasAutorizadas = $response->getData(); // Obtener los datos de la respuesta JSON

    // Crear un array para almacenar la nueva respuesta
    $resultado = [];

    // Recorrer cada persona autorizada
    foreach ($personasAutorizadas as $persona) {
        // Obtener el evento correspondiente
        $evento = DB::table('tb_evento')
            ->select('titulo')
            ->where('id', $persona->evento_id) // Asegúrate de usar el nombre correcto de la columna
            ->first();

        // Agregar el título del evento a la persona
        $persona->evento_titulo = $evento ? $evento->titulo : null; // Si no hay evento, asignar null

        // Agregar la persona con el título del evento al resultado
        $resultado[] = $persona;
    }

    // Retornar la nueva respuesta en formato JSON
    return response()->json($resultado, 200);
}

public function actualizarEstatus(Request $request, $id)
{
    // Validar la entrada
    $request->validate([
        'estatus' => 'required|in:activo,rechazado', // Aceptar solo 'activo' o 'rechazado'
    ]);

    // Buscar el registro de EventoPersona
    $eventoPersona = EventoPersona::find($id);
    if (!$eventoPersona) {
        return response()->json(['msg' => 'Registro no encontrado.'], 404);
    }
    // Actualizar el estatus
    $eventoPersona->estatus = $request->estatus;
    $eventoPersona->save(); // Guardar los cambios en la base de datos
    return response()->json(['msg' => 'Estatus actualizado correctamente.']);
}




public function actualizarCortesiaEntregada(Request $request)
{
    // Obtener el usuario logueado
    $user = Auth::user();

    // Validar que el usuario tenga un evento activo
    if (!$user->configUser ->evento_activo) {
        return response()->json(['mensaje' => 'No hay evento activo para el usuario.'], 400);
    }

    // Obtener el evento activo
    $evento = Evento::find($user->configUser ->evento_activo);

    if (!$evento) {
        return response()->json(['mensaje' => 'Evento no encontrado.'], 404);
    }

    // Obtener el valor de cortesia_entregada del request
    $cortesiaEntregada = $request->input('cortesia_entregada');

    // Validar que cortesia_entregada sea un número entero
    if (!is_numeric($cortesiaEntregada) || intval($cortesiaEntregada) < 0) {
        return response()->json(['mensaje' => 'El valor de cortesia entregada debe ser un número entero mayor o igual a 0.'], 400);
    }

    // Convertir a entero
    $cortesiaEntregada = intval($cortesiaEntregada);

    // Calcular el total permitido
    $totalPermitido = $evento->cortesia - $evento->cortesia_entregada;

    // Verificar si la cantidad recibida puede ser sumada
    if ($cortesiaEntregada > $totalPermitido) {
        // Solo se permite sumar hasta el total permitido
        $cortesiaEntregada = $totalPermitido;
    }

    // Sumar la cantidad permitida al campo cortesia_entregada
    $evento->cortesia_entregada += $cortesiaEntregada;

    // Guardar el evento
    $evento->save();

    // Mensaje de éxito con la cantidad permitida
    return response()->json([
        'mensaje' => 'Cortesia entregada actualizada con éxito.',
        'cantidad_permitida' => $cortesiaEntregada
    ]);
}


    public function obtenerCortesias()
    {
        // Obtener el usuario logueado
        $user = Auth::user();

        // Validar que el usuario tenga un evento activo
        if (!$user->configUser->evento_activo) {
            return response()->json(['mensaje' => 'No hay evento activo para el usuario.'], 400);
        }

        // Obtener el evento activo
        $evento = Evento::find($user->configUser->evento_activo);

        if (!$evento) {
            return response()->json(['mensaje' => 'Evento no encontrado.'], 404);
        }

        // Calcular las cortesías faltantes
        $cortesiasFaltantes = $evento->cortesia - $evento->cortesia_entregada;

        // Retornar la información
        return response()->json([
            'cortesia' => $evento->cortesia,
            'cortesia_entregada' => $evento->cortesia_entregada,
            'cortesias_faltantes' => $cortesiasFaltantes,
        ]);
    }



}