<?php

namespace App\Http\Controllers\Configurations;

use Illuminate\Http\{Request, JsonResponse};
use App\Http\Controllers\Controller;
use App\Models\Ente;


class EnteController extends Controller
{
    public function index()
    {
        $entes = Ente::all();

        return response()->json(['entes' => $entes]);
    }
    
    public function create()
    {
        return response()->json(['message' => 'Crear ente']);
    }
    
    public function store(Request $request)
    {
        $ente = new Ente();
        $ente->entes = $request->input('entes');
        $ente->save();
        return response()->json(['message' => 'Ente creado con éxito', 'ente' => $ente]);
    }
    
    public function show($id)
    {
        $ente = Ente::find($id);
        return response()->json(['ente' => $ente]);
    }
    
    public function edit($id)
    {
        $ente = Ente::find($id);
        return response()->json(['ente' => $ente]);
    }
    
    public function update(Request $request, $id)
    {
        $ente = Ente::find($id);
        $ente->entes = $request->input('entes');
        $ente->save();
        return response()->json(['message' => 'Ente actualizado con éxito', 'ente' => $ente]);
    }
    
    public function destroy($id)
    {
        $ente = Ente::find($id);
        $ente->delete();
        return response()->json(['message' => 'Ente eliminado con éxito']);
    }
}
