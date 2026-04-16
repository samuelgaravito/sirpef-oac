<?php 

namespace App\Http\Services\config;
use App\Models\UnidadAdscrita;
use App\Models\Ente;
use App\Models\Tipo;

use Illuminate\Http\{
    Request,
    JsonResponse
};



class MinisterioService {

    static public function indexMinisterios()
    {
        $ministerios = Ministerio::all();

        return response()->json(['ministerios' => $ministerios]);
    }


    static public function createMinisterio()
    {
        // You may need to adjust this function to fit your needs
        // since there's no Tipo model in the provided code
        $tipos = Tipo::all();
        return response()->json(['message' => 'Crear ministerio', 'tipos' => $tipos]);
    }


    static public function storeMinisterio(Request $request)
    {
        $ministerio = new Ministerio();

        $ministerio->nombre = $request->input('nombre');

        $ministerio->descripcion = $request->input('descripcion');

        $ministerio->ministerio_padre_id = $request->input('ministerio_padre_id');

        $ministerio->save();

        return response()->json(['message' => 'Ministerio creado con éxito', 'ministerio' => $ministerio]);
    }


    static public function showMinisterio($id)
    {
        $ministerio = Ministerio::find($id);

        return response()->json(['ministerio' => $ministerio]);
    }


    static public function editMinisterio($id)
    {
        // You may need to adjust this function to fit your needs
        // since there's no Tipo model in the provided code
        $ministerio = Ministerio::find($id);
        $tipos = Tipo::all();
        return response()->json(['ministerio' => $ministerio, 'tipos' => $tipos]);
    }


    static public function updateMinisterio(Request $request, $id)
    {
        $ministerio = Ministerio::find($id);

        $ministerio->nombre = $request->input('nombre');

        $ministerio->descripcion = $request->input('descripcion');

        $ministerio->ministerio_padre_id = $request->input('ministerio_padre_id');

        $ministerio->save();

        return response()->json(['message' => 'Ministerio actualizado con éxito', 'ministerio' => $ministerio]);
    }


    static public function destroyMinisterio($id)
    {
        $ministerio = Ministerio::find($id);

        $ministerio->delete();

        return response()->json(['message' => 'Ministerio eliminado con éxito']);
    }



    static public function getMinisterio()
    {
        $excludedIds = [0]; // remove decimal values
        $ministerio = Ministerio::all(['id','nombre']);
        return response()->json(
            $ministerio,
        );
    }

}