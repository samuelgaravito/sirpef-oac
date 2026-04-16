<?php 

namespace App\Http\Services\config;
use App\Models\Tipo;
use Illuminate\Http\{
    Request,
    JsonResponse
};



class TipoService {



static public function indexTipos()
    {
        $tipos = Tipo::all();

        return response()->json(['tipos' => $tipos]);
    }


    static public function createTipo()
    {
        return response()->json(['message' => 'Crear tipo']);
    }


    static public function storeTipo(Request $request)
    {
        $tipo = new Tipo();

        $tipo->tipo_oficina = $request->input('tipo_oficina');

        $tipo->save();

        return response()->json(['message' => 'Tipo creado con éxito', 'tipo' => $tipo]);
    }


    static public function showTipo($id)
    {
        $tipo = Tipo::find($id);

        return response()->json(['tipo' => $tipo]);
    }


    static public function editTipo($id)
    {
        $tipo = Tipo::find($id);

        return response()->json(['tipo' => $tipo]);
    }


    static public function updateTipo(Request $request, $id)
    {
      $tipo = Tipo::find($id);

        $tipo->tipo_oficina = $request->input('tipo_oficina');

        $tipo->save();

        return response()->json(['message' => 'Tipo actualizado con éxito', 'tipo' => $tipo]);
    }


    static public function destroyTipo($id)
    {
        $tipo = Tipo::find($id);

        $tipo->delete();

        return response()->json(['message' => 'Tipo eliminado con éxito']);
    }

}