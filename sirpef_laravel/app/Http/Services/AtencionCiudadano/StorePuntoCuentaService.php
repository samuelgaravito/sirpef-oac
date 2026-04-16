<?php

namespace App\Http\Services\AtencionCiudadano;

use Illuminate\Http\{
    Request,
    JsonResponse
};
use App\Models\PuntoCuenta;
use App\Models\Registro;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StorePuntoCuentaService {

    /**
     * Crea un nuevo punto de cuenta y lo asocia a un registro existente
     * * @param Request $request
     * @param int $registroId
     * @return JsonResponse
     */
    public static function crearPuntoCuenta(Request $request, int $registroId): JsonResponse
    {
        return DB::transaction(function () use ($request, $registroId) {
            try {
                // Validar los datos del request
                $validatedData = $request->validate([
                    'presentado_a' => 'required|string|max:255',
                    'presentado_por' => 'required|string|max:255',
                    'fecha' => 'required|date',
                    'numero_punto' => 'required|string|max:50',
                    'asunto' => 'required|string',
                    'exposicion_motivos' => 'required|string',
                    'propuesta' => 'required|string',
                    'decision' => 'nullable|string|in:APROBADO,NEGADO,VISTO,DIFERIDO,OTRO',
                    'otras_instrucciones' => 'nullable|string',
                    'anexos' => 'nullable|boolean',
                    'cargo_a' => 'nullable|string|max:255',
                    'cargo_por' => 'nullable|string|max:255',
                    'resolucion_1' => 'nullable|string',
                    'resolucion_2' => 'nullable|string'
                ]);

                // Verificar que el registro exista
                $registro = Registro::findOrFail($registroId);

                // **Añadir el campo 'estatus_pc' y establecerlo en true**
                $validatedData['estatus_pc'] = true;

                // Crear el punto de cuenta con los datos validados y el nuevo estatus
                $puntoCuenta = PuntoCuenta::create($validatedData);

                // Asociar el punto de cuenta al registro
                $registro->punto_cuenta_id = $puntoCuenta->id;
                $registro->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Punto de cuenta creado y asociado al registro exitosamente',
                    'data' => [
                        'punto_cuenta' => $puntoCuenta,
                        'registro_id' => $registro->id
                    ]
                ], 201);

            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], 404);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el punto de cuenta',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }
}
