<?php

namespace App\Http\Services\Memos;

use App\Models\Memorandum;
use App\Models\PuntoCuenta;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CreateMemoService
{
    /**
     * Busca un Punto de Cuenta por su número y devuelve los datos de la persona asociada.
     *
     * @param string $numero
     * @return JsonResponse
     */
    public static function buscarPuntoCuenta(string $numero): JsonResponse
    {
        $puntoCuenta = PuntoCuenta::where('numero_punto', 'LIKE', trim($numero))
            ->with(['registros.eventoPersona.persona', 'registros.proveedores'])
            ->first();

        if (!$puntoCuenta) {
            return response()->json([
                'message' => 'Punto de Cuenta no encontrado',
                'success' => false
            ], 404);
        }

        // Intentamos obtener la persona y proveedores desde el primer registro asociado
        $registro = $puntoCuenta->registros->first();
        $persona = ($registro && $registro->eventoPersona) ? $registro->eventoPersona->persona : null;
        
        // Obtenemos el primer proveedor y el monto total si existen
        $proveedor = ($registro && $registro->proveedores->count() > 0) ? $registro->proveedores->first() : null;
        $montoTotal = ($registro) ? $registro->proveedores->sum('monto') : 0;

        // Buscamos si ya existe un memorándum para este punto de cuenta
        $memorandum = $puntoCuenta->memorandum;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $puntoCuenta->id,
                'numero_punto' => $puntoCuenta->numero_punto,
                'fecha' => $puntoCuenta->fecha->format('Y-m-d'),
                'solicitante' => $persona ? $persona->nombre_completo : 'No vinculado',
                'solicitante_id' => $persona ? $persona->id : null,
                'cedula' => $persona ? $persona->cedula : 'N/A',
                'asunto' => $puntoCuenta->asunto,
                'monto' => $montoTotal,
                'proveedor' => $proveedor ? $proveedor->nombre : 'N/A',
                'existing_memo' => $memorandum ? [
                    'id' => $memorandum->id,
                    'codigo' => $memorandum->codigo,
                    'de_nombre' => $memorandum->de,
                    'para_nombre' => $memorandum->para,
                    'asunto' => $memorandum->asunto,
                    'fecha' => $memorandum->fecha->format('Y-m-d'),
                    'motivo' => $memorandum->cuerpo,
                    'monto' => $memorandum->monto,
                    'proveedor' => $memorandum->proveedor,
                    'headerImg' => $memorandum->header_img,
                    'footerImg' => $memorandum->footer_img,
                    'firmaImg' => $memorandum->firma_img,
                ] : null,
            ]
        ]);
    }

    /**
     * Store a new memorandum record.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public static function store(Request $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            try {
                $user = auth()->user();
                if (!$user) {
                    return response()->json(['message' => 'No autorizado', 'success' => false], 401);
                }

                $validated = $request->validate([
                    'punto_cuenta_id' => 'required|exists:tbl_punto_cuenta,id',
                    'codigo' => ['required', 'string'],
                    'de' => 'required|string',
                    'para' => 'required|string',
                    'asunto' => 'required|string',
                    'fecha' => 'required|date',
                    'cuerpo' => 'required|string',
                    'headerImg' => 'nullable|string',
                    'footerImg' => 'nullable|string',
                    'firmaImg' => 'nullable|string',
                ]);

                $puntoCuenta = PuntoCuenta::findOrFail($validated['punto_cuenta_id']);

                // Verificar si ya existe un memorándum para este punto de cuenta
                $existe = Memorandum::where('punto_cuenta_id', $validated['punto_cuenta_id'])->first();
                if ($existe) {
                    return response()->json([
                        'message' => "Ya existe un memorándum registrado para el Punto de Cuenta N° {$puntoCuenta->numero_punto} (Código: {$existe->codigo}).",
                        'success' => false
                    ], 422);
                }

                $memorandum = Memorandum::create([
                    'punto_cuenta_id' => $validated['punto_cuenta_id'],
                    'codigo' => $validated['codigo'],
                    'de' => $validated['de'],
                    'para' => $validated['para'],
                    'asunto' => $validated['asunto'],
                    'fecha' => $validated['fecha'],
                    'cuerpo' => $validated['cuerpo'],
                    'monto' => $request->monto,
                    'proveedor' => $request->proveedor,
                    'header_img' => $validated['headerImg'],
                    'footer_img' => $validated['footerImg'],
                    'firma_img' => $validated['firmaImg'],
                ]);

                // Auditoría
                $auditoria = new Auditoria();
                $auditoria->user_id = $user->id;
                $auditoria->evento_id = $user->configUser->evento_activo ?? null;
                $auditoria->descripcion = "El usuario {$user->name} creó el memorándum {$memorandum->codigo} asociado al Punto de Cuenta N° {$puntoCuenta->numero_punto}.";
                $auditoria->save();

                return response()->json([
                    'message' => 'Memorándum guardado exitosamente',
                    'success' => true,
                    'data' => $memorandum
                ], 201);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error al guardar el memorándum: ' . $e->getMessage(),
                    'success' => false
                ], 500);
            }
        });
    }

    /**
     * Get all memorandums with related PuntoCuenta and Persona data.
     *
     * @return JsonResponse
     */
    public static function index(): JsonResponse
    {
        $memos = Memorandum::with(['puntoCuenta.registros.eventoPersona.persona'])
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedMemos = $memos->map(function ($memo) {
            $puntoCuenta = $memo->puntoCuenta;
            $registro = $puntoCuenta ? $puntoCuenta->registros->whereNotNull('evento_persona_id')->first() : null;
            $persona = ($registro && $registro->eventoPersona) ? $registro->eventoPersona->persona : null;

            return [
                'id' => $memo->id,
                'punto_cuenta_id' => $memo->punto_cuenta_id,
                'registro_id' => $registro ? $registro->id : null,
                'codigo' => $memo->codigo,
                'para_nombre' => $memo->para,
                'de_nombre' => $memo->de,
                'asunto' => $memo->asunto,
                'motivo' => $memo->cuerpo,
                'tabla' => [
                    'pto_cta' => $puntoCuenta ? $puntoCuenta->numero_punto : 'N/A',
                    'fecha' => $memo->fecha ? $memo->fecha->format('Y-m-d') : 'N/A',
                    'solicitante' => $persona ? $persona->nombre_completo : 'N/A',
                    'cedula' => $persona ? $persona->cedula : 'N/A',
                    'monto' => $memo->monto, 
                    'proveedor' => $memo->proveedor,
                    'total' => $memo->monto
                ],
                'headerImg' => $memo->header_img,
                'footerImg' => $memo->footer_img,
                'firmaImg' => $memo->firma_img,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedMemos
        ]);
    }

    /**
     * Update an existing memorandum.
     */
    public static function update(Request $request, $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            try {
                $user = auth()->user();
                $memorandum = Memorandum::findOrFail($id);

                $validated = $request->validate([
                    'codigo' => ['required', 'string'],
                    'de' => 'required|string',
                    'para' => 'required|string',
                    'asunto' => 'required|string',
                    'fecha' => 'required|date',
                    'cuerpo' => 'required|string',
                    'monto' => 'nullable|numeric',
                    'proveedor' => 'nullable|string',
                    'headerImg' => 'nullable|string',
                    'footerImg' => 'nullable|string',
                    'firmaImg' => 'nullable|string',
                ]);

                $memorandum->update([
                    'codigo' => $validated['codigo'],
                    'de' => $validated['de'],
                    'para' => $validated['para'],
                    'asunto' => $validated['asunto'],
                    'fecha' => $validated['fecha'],
                    'cuerpo' => $validated['cuerpo'],
                    'monto' => $validated['monto'],
                    'proveedor' => $validated['proveedor'],
                    'header_img' => $validated['headerImg'],
                    'footer_img' => $validated['footerImg'],
                    'firma_img' => $validated['firmaImg'],
                ]);

                // Auditoría
                $auditoria = new Auditoria();
                $auditoria->user_id = $user->id;
                $auditoria->descripcion = "El usuario {$user->name} actualizó el memorándum {$memorandum->codigo}.";
                $auditoria->save();

                return response()->json([
                    'message' => 'Memorándum actualizado exitosamente',
                    'success' => true,
                    'data' => $memorandum
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error al actualizar: ' . $e->getMessage(),
                    'success' => false
                ], 500);
            }
        });
    }

    /**
     * Delete a memorandum.
     */
    public static function destroy($id): JsonResponse
    {
        try {
            $user = auth()->user();
            $memorandum = Memorandum::findOrFail($id);
            $codigo = $memorandum->codigo;
            $memorandum->delete();

            // Auditoría
            $auditoria = new Auditoria();
            $auditoria->user_id = $user->id;
            $auditoria->descripcion = "El usuario {$user->name} eliminó el memorándum {$codigo}.";
            $auditoria->save();

            return response()->json([
                'message' => 'Memorándum eliminado exitosamente',
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }
}
