<?php

namespace App\Http\Services\AtencionCiudadano;

use App\Models\Memorandum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

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
                    'punto_cuenta_id' => 'required|exists:punto_cuentas,id',
                    'codigo'          => 'required|string',
                    'de'              => 'required|string',
                    'para'            => 'required|string',
                    'asunto'          => 'required|string',
                    'fecha'           => 'required|date',
                    'cuerpo'          => 'required|string',
                ]);

                $memorandum = Memorandum::create($validated);

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
