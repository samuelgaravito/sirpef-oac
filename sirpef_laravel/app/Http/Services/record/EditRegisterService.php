<?php

namespace App\Http\Services\record;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\Registro;
use App\Models\EventoPersona;

class EditRegisterService{
    static public function editRegistro(Request $request, $personaId)
    {
        $user = auth()->user();

        $persona = Persona::find($personaId);

        if (!$persona) return response()->json(['error' => 'Persona no encontrada'], 404);
    
        $EventoPersona = EventoPersona::where('persona_id', $personaId)
        ->where('evento_id', $user->configUser->evento_activo)
        ->first();
                
        if (!$EventoPersona) return response()->json(['error' => 'Registro no encontrado'], 404);
        
        $registro = Registro::where('evento_persona_id', $EventoPersona->id)
        ->first();

        $registro->voto = !$registro->voto;
        $registro->save();
    
        return response()->json(['message' => 'Registro actualizado con éxito']);
    }
}