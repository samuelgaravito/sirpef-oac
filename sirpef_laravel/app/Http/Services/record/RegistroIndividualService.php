<?php

namespace App\Http\Services\record;
use App\Http\Controllers\ConstController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\Registro;
use App\Models\UnidadAdscrita;
use App\Models\Auditoria;
use App\Models\EventoPersona;


class RegistroIndividualService
{
    static public function registervote(Request $request)
    {
        $validatedData = $request->validate([
            'vote' => 'required',
            'descripcion' => 'nullable',
            'hora_voto' => 'nullable',
            'persona_id' => 'required|integer',
        ]);
       
        $ConstController = new ConstController();
        $personas = $ConstController->obtenerPersonas();
    
        $persona = Persona::find($validatedData['persona_id']);
    
        if (!$persona || !$personas->pluck('id')->contains($persona->id)) {
            return response()->json(['msg' => 'La persona no existe o no tiene permiso para registrar un voto'], 404);
        }
    
        try {
            $eventoPersona = $persona->eventos()->where('evento_id', auth()->user()->configUser->evento_activo)->withPivot('id')->first();    
            if (!$eventoPersona) {
                return response()->json(['msg' => 'La persona no está asociada al evento activo'], 404);
            }
    
            $registro = new Registro;
            $registro->voto = $validatedData['vote'];
            $registro->descripcion = $validatedData['descripcion'];
            $registro->hora_voto = $validatedData['hora_voto'] ?? now();
            $registro->evento_persona_id = $eventoPersona->pivot->id;
            $registro->save();
    
        } catch (\Throwable $th) {
            return response()->json([$th->getMessage()], 400);
        }
    
        return response()->json(["msg" => "Registrado correctamente"], 201);
    }
}