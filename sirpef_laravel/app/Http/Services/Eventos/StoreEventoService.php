<?php

namespace App\Http\Services\Eventos;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Ministerio;

class StoreEventoService
{
    static public function getMinisterios()
    {
        // Obtiene todos los ministerios
        $ministerios = Ministerio::all();
        // Retorna los ministerios en formato JSON
        return response()->json($ministerios);
    }

    static public function storeEvento(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'ministerio_ids' => 'required|array',
            'cortesia' => 'required|integer|min:0', // Validación para cortesia
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Recibe los IDs de los ministerios como una variable
        $ministerioIds = $request->input('ministerio_ids');

        // Crea un nuevo evento
        $evento = new Evento();
        $evento->titulo = $request->input('titulo');
        $evento->descripcion = $request->input('descripcion');
        $evento->fecha_inicio = $request->input('fecha_inicio');
        $evento->fecha_fin = $request->input('fecha_fin');
        $evento->estatus = false;

        // Convierte el array de IDs de ministerios en una cadena JSON
        $ministerioIdsJson = json_encode($ministerioIds);

        // Asigna la cadena JSON al campo ministerio_id
        $evento->ministerio_id = $ministerioIdsJson;

        // Asigna los nuevos campos
        $evento->cortesia = $request->input('cortesia'); // Asignar el campo cortesia
   
        // Guarda el evento
        $evento->save();

        // Retorna un mensaje de éxito
        return response()->json(['mensaje' => 'Evento creado con éxito', 'id' => $evento->id]);
    }
}