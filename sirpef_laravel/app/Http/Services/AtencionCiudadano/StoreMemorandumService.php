<?php

namespace App\Http\Services\AtencionCiudadano;

use App\Models\Memorandum;
use App\Models\PuntoCuenta;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class StoreMemorandumService
{
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
                    'numero_punto_cuenta' => 'required|string|exists:tbl_punto_cuentas,numero',
                    'codigo'              => 'required|string',
                    'de'                  => 'required|string',
                    'para'                => 'required|string',
                    'asunto'              => 'required|string',
                    'fecha'               => 'required|date',
                    'cuerpo'              => 'required|string',
                ]);

                // Buscar el ID del punto de cuenta basándose en el número ingresado
                $puntoCuenta = PuntoCuenta::where('numero', $validated['numero_punto_cuenta'])->firstOrFail();

                $memorandum = Memorandum::create([
                    'punto_cuenta_id' => $puntoCuenta->id,
                    'codigo'          => $validated['codigo'],
                    'de'              => $validated['de'],
                    'para'            => $validated['para'],
                    'asunto'          => $validated['asunto'],
                    'fecha'           => $validated['fecha'],
                    'cuerpo'          => $validated['cuerpo'],
                ]);

                // Auditoría
                $auditoria = new Auditoria();
                $auditoria->user_id = $user->id;
                $auditoria->evento_id = $user->configUser->evento_activo ?? null;
                $auditoria->descripcion = "El usuario {$user->name} creó el memorándum {$memorandum->codigo} asociado al Punto de Cuenta N° {$puntoCuenta->numero}.";
                $auditoria->save();

                return response()->json([
                    'message' => 'Memorándum guardado exitosamente',
                    'success' => true,
                    'data'    => $memorandum
                ], 201);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error al guardar el memorándum: ' . $e->getMessage(),
                    'success' => false
                ], 500);
            }
        });
    }
}
