<?php
namespace App\Http\Services\record;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\Registro;
use App\Models\EventoPersona;

class RegistroMultipleService
{
    static public function registerMultipleVotes(Request $request)
    {
        $validatedData = $request->validate([
            'vote' => 'required',
            'descripcion' => 'nullable',
            'hora_voto' => 'nullable',
            'persona_ids' => 'required|array',
            'persona_ids.*' => 'integer',
        ]);

        $errors = [];
        $successful = [];
        $alreadyRegistered = [];
        $notFound = [];

        foreach ($validatedData['persona_ids'] as $persona_id) {
            if ($persona_id === null) {
                $errors[] = "Persona ID cannot be null";
                continue;
            }

            $persona = Persona::find($persona_id);
            if (!$persona) {
                $notFound[] = $persona_id;
                continue;
            }

            $eventoPersona = $persona->eventos()->where('evento_id', auth()->user()->configUser->evento_activo)->withPivot('id')->first();
            if (!$eventoPersona) {
                $errors[] = "La persona no está asociada al evento activo";
                continue;
            }

            $exists = Registro::where('evento_persona_id', $eventoPersona->pivot->id)->exists();

            if ($exists) {
                $alreadyRegistered[] = $persona_id;
                continue;
            }

            try {
                $registro = new Registro;
                $registro->voto = $validatedData['vote'];
                $registro->descripcion = $validatedData['descripcion'];
                $registro->hora_voto = $validatedData['hora_voto'] ?? now();
                $registro->evento_persona_id = $eventoPersona->pivot->id;
                $registro->save();

                $successful[] = $persona_id;
            } catch (\Throwable $th) {
                $errors[] = "Error al registrar la persona ID $persona_id: " . $th->getMessage();
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 400);
        }

        $registeredCount = count($successful);
        $alreadyRegisteredCount = count($alreadyRegistered);
        $notFoundCount = count($notFound);

        $message = "Se registraron $registeredCount personas con éxito.";

        // ... rest of the code remains the same ...

        return response()->json(["msg" => $message], 201);
    }
}