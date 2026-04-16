<?php

namespace App\Http\Services\Eventos;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Evento;

use App\Models\Ministerio;


class UpdateEventoService{

    static public function editEvento(Request $request, $id)
    {
        // Obtiene el evento a editar
        $evento = Evento::find($id);

        $arr_minis = json_decode($evento->ministerio_id, true);

        $ministerios = Ministerio::whereIn("id", $arr_minis)->get();
      
        return response()->json(['evento' => $evento, "ministerios" => $ministerios]);

    }


    static public function updateEvento(Request $request, $id)
    {
        // Obtiene el evento a actualizar
        $evento = Evento::find($id);

        // Actualiza los campos del evento
        $evento->titulo = $request->input('titulo');
        $evento->descripcion = $request->input('descripcion');
        $evento->cortesia = $request->input('cortesia'); 
        $evento->fecha_inicio = $request->input('fecha_inicio');
        $evento->fecha_fin = $request->input('fecha_fin');


        // Obtiene los IDs de los ministerios seleccionados
        $ministerioIds = $request->input('ministerio_ids');

        // Convierte el array de IDs de ministerios en una cadena JSON
        $ministerioIdsJson = json_encode($ministerioIds);

        // Asigna la cadena JSON al campo ministerio_id
        $evento->ministerio_id = $ministerioIdsJson;

        // Guarda los cambios
        $evento->save();

        // Retorna un mensaje de éxito
        return response()->json(['mensaje' => 'Evento actualizado con éxito']);
    }


    static public function changeEventoStatus(Request $request, $id)
    {
        // Obtiene el evento a actualizar
        $evento = Evento::find($id);
    
        // Actualiza el campo status del evento
        $evento->estatus = !$evento->estatus;
    
        // Guarda los cambios
        $evento->save();
    
        // Si el estatus del evento es false, actualiza los usuarios que tienen este evento activo
        if (!$evento->estatus) {
            $users = User::where('evento_activo', $id)->where('is_admin', false)->get();
            foreach ($users as $user) {
                $user->configUser->evento_activo = null;
                $user->save();
            }
        }
    
        // Retorna un mensaje de éxito
        return response()->json(['mensaje' => 'Evento actualizado con éxito']);
    }
 


}