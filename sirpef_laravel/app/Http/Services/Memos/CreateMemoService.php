<?php

namespace App\Http\Services\Memos;

use App\Models\Memorandum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CreateMemoService
{
    /**
     * Crea un nuevo memorandum.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public static function create(Request $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            try {
                $user = auth()->user();
                if (!$user) {
                    return response()->json(['message' => 'No autorizado', 'success' => false], 401);
                }

                $memo = Memorandum::create($request->all());

                return response()->json([
                    'message' => 'Memorándum creado con éxito',
                    'success' => true,
                    'data' => $memo
                ], 201);

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error al crear el memorándum',
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }
}
