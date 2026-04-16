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
use App\Http\Services\record\UserInfoService;

class RegistroIndividualFeDeVidaService
{
    static public function registervoteFedeVida(Request $request)
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
            $eventoPersona = $persona->eventos()->where('evento_id', auth()->user()->configUser ->evento_activo)->withPivot('id')->first();    
            if (!$eventoPersona) {
                return response()->json(['msg' => 'La persona no está asociada al evento activo'], 404);
            }

            // Buscar el registro existente
            $registro = Registro::where('evento_persona_id', $eventoPersona->pivot->id)->first();

            if (!$registro) {
                return response()->json(['msg' => 'Registro no encontrado'], 404);
            }

            // Actualizar los campos del registro
            $registro->voto = $validatedData['vote'];
            //$registro->descripcion = $validatedData['descripcion'];
            $registro->hora_voto = $validatedData['hora_voto'] ?? now();
            $registro->save();

            // Obtener el rol y el nombre del usuario
            $user = auth()->user();
            $rol = $user->role->name; // Asegúrate de que el modelo User tenga una relación o atributo para el rol
            $nombreUsuario = $user->name; // Asegúrate de que el modelo User tenga un atributo para el nombre

            // Construir la descripción
            $descripcion = "{$rol} {$nombreUsuario} registró una participación a la persona con cédula: " . $persona->cedula;

            // Registrar la acción en la auditoría
            Auditoria::create([
                'descripcion' => $descripcion,
                'user_id' => auth()->id(), // ID del usuario autenticado
                'evento_id' => auth()->user()->configUser ->evento_activo, // ID del evento activo
                'persona_id' => $persona->id, // ID de la persona a la que se le realizó el registro
            ]);

            $UserInfoService = new UserInfoService();

            $UserInfoService->getUserInfo($descripcion); // Corrección aquí


        } catch (\Throwable $th) {
            return response()->json([$th->getMessage()], 400);
        }
    
        return response()->json(["msg" => "Registro actualizado correctamente"], 200);
    }
}