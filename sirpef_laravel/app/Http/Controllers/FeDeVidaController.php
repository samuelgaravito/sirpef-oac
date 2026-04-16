<?php

namespace App\Http\Controllers;
use App\Models\Persona;
use App\Models\EventoPersona;
use App\Models\Evento;
use App\Models\Registro;
use Illuminate\Http\Request;
use App\Http\Services\record\UserInfoService;


class FeDeVidaController extends Controller
{
 // Función para actualizar una persona existente
 public function actualizarJubilado(Request $request, $id)
 {
     // Validar los datos de entrada
     $request->validate([
         'nombre_completo' => 'sometimes|required|string|max:255',
         'cedula' => 'sometimes|required|string|max:20|unique:tbl_personas,cedula,' . $id,
         'telefono' => 'sometimes|required|string|max:15',
         'telefono_2' => 'sometimes|max:15',
         'direccion' => 'sometimes|required|string|max:255',
         'tipo_empleado_id' => 'sometimes|required|integer',
         'pais_id' => 'sometimes|required|integer',
         'parroquia_id' => 'sometimes|nullable|integer', // Validar que exista en tbl_parroquias
         'ministerio_id' => 'sometimes|required|integer',
         'correo_electronico' => 'required|string',
         'causa_pension'=> 'nullable|string',
         'imagen1' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
         'imagen2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
         'imagen3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
         'where' => 'string'
     ]);
 
     // Buscar la persona por ID
     $persona = Persona::find($id);
 
     if (!$persona) {
         return response()->json(['message' => 'Persona no encontrada'], 404);
     }

 /*
     $persona->update($request->only([
         'nombre_completo',
         'cedula',
         'telefono',
         'direccion',
         'tipo_empleado_id',
         'ministerio_id',
         'parroquia_id',
         'pais_id',
     ]));*/


     // Asignar los valores a las propiedades del modelo
        $persona->nombre_completo = $request->input('nombre_completo', $persona->nombre_completo);
        $persona->cedula = $request->input('cedula', $persona->cedula);
        $persona->telefono = $request->input('telefono', $persona->telefono);
        $persona->telefono_2 = $request->input('telefono_2') != null ? $request->input('telefono_2') : null;
        $persona->direccion = $request->input('direccion', $persona->direccion);
        $persona->tipo_empleado_id = $request->input('tipo_empleado_id', $persona->tipo_empleado_id);
        $persona->ministerio_id = $request->input('ministerio_id', $persona->ministerio_id);
        $persona->parroquia_id = $request->input('parroquia_id') ? $request->input('parroquia_id') : 1; // Ternaria para parroquia_id
        //$persona->pais_id = $request->input('pais_id', $persona->pais_id);
        $persona->pais_id = $request->input('pais_id') ? $request->input('pais_id') : 170; // Ternaria para parroquia_id

        $persona->correo_electronico = $request->input('correo_electronico', $persona->correo_electronico);
        $persona->causa_pension = $request->input('causa_pension', $persona->causa_pension);
        $persona->fecha_nacimiento = $request->input('fecha_nacimiento', $persona->fecha_nacimiento);
        // Guardar los cambios
        $persona->save();
        
     // Ahora, vamos a buscar el registro en EventoPersona
     $eventoPersona = EventoPersona::where('persona_id', $id)->first();
 
     if (!$eventoPersona) {
         return response()->json(['message' => 'EventoPersona no encontrado.'], 404);
     }
 
     // Validar que el tipo sea "fe-de-vida"
     if ($eventoPersona->tipo !== 'fe-de-vida') {
         return response()->json(['message' => 'El tipo de EventoPersona no es "fe-de-vida".'], 400);
     }
 
     // Buscar el evento asociado y validar su estatus
     $evento = Evento::find($eventoPersona->evento_id);
 
     if (!$evento) {
         return response()->json(['message' => 'Evento no encontrado.'], 404);
     }
 
     // Validar que el estatus del evento sea verdadero
     if (!$evento->estatus) { // Cambiado a !$evento->estatus para verificar si es false
         return response()->json(['message' => 'El evento no está activo.'], 400);
     }
 
     // Guardar las imágenes si se han subido
     if ($request->hasFile('ceduJubilado')) {
         $imagen1Path = $request->file('ceduJubilado')->store('imagenes', 'public');
         $eventoPersona->imagen1 = $imagen1Path;
     }
 
     if ($request->hasFile('jubilado')) {
         $imagen2Path = $request->file('jubilado')->store('imagenes', 'public');
         $eventoPersona->imagen2 = $imagen2Path;
     }
 
     if ($request->hasFile('jubiAndCedu')) {
         $imagen3Path = $request->file('jubiAndCedu')->store('imagenes', 'public');
         $eventoPersona->imagen3 = $imagen3Path;
     }
 

     $eventoPersona->tipo = 'fe-de-vida';


     
     // Guardar los cambios en EventoPersona
     $eventoPersona->save();


  // Verificar si ya existe un registro para el evento persona

  $registro = Registro::where('evento_persona_id', $eventoPersona->id)->first();


  if ($registro) {

      // Actualizar el registro existente

      $registro->descripcion = $request->input('where') ? $request->input('where') : 'Formulario'; // Descripción opcional

      $registro->hora_voto = now(); // Hora actual
      $registro->voto = null; 

      // Guardar el registro actualizado

      $registro->save();

  } else {

      // Crear un nuevo registro en la tabla tbl_registros

      $registro = new Registro();

      $registro->voto = null; // O el valor que desees asignar

      $registro->descripcion = $request->input('where') ? $request->input('where') : 'Formulario'; // Descripción opcional

      $registro->hora_voto = now(); // Hora actual

      $registro->evento_persona_id = $eventoPersona->id; // ID del EventoPersona


      // Guardar el registro

      $registro->save();

  }

  $descripcion = "La cédula " . $request->input('cedula', $persona->cedula) . " tiene un estatus pendiente";

  $UserInfoService = new UserInfoService();

  $UserInfoService->getUserInfo($descripcion);

 
     return response()->json(['message' => 'Se han enviado los datos correctamente'], 200);
 }
}









