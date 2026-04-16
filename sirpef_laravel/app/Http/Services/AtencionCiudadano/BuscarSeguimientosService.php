<?php

namespace App\Http\Services\AtencionCiudadano;

use App\Models\Registro;    // Importa el modelo Registro (tbl_registros)
use App\Models\Seguimiento; // Importa el modelo Seguimiento (tbl_seguimiento)
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BuscarSeguimientosService
{
    /**
     * Busca todos los registros de seguimiento asociados a un ID de registro (caso).
     *
     * @param int $registroId El ID del registro (caso) cuyos seguimientos se desean buscar.
     * @return JsonResponse
     */
    static public function showSeguimientos(int $registroId): JsonResponse
    {
        try {
            // 1. Verificar si el Registro (caso) existe
            // Usamos findOrFail para que si el registro no existe, lance una ModelNotFoundException

            // 2. Buscar todos los seguimientos relacionados con este registro_id
            // Cargamos las relaciones estatusTramite y registro para mostrar información completa
            $seguimientos = Seguimiento::where('registro_id', $registroId)
                                        ->with('estatusTramite') // Cargar relaciones
                                        ->orderBy('id', 'desc') // Opcional: ordenar por fecha
                                        ->get();

            // 3. Retornar una respuesta JSON con los seguimientos encontrados
            if ($seguimientos->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => "No se encontraron seguimientos para el registro con ID: {$registroId}.",
                    'data'    => []
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => "Seguimientos encontrados para el registro con ID: {$registroId}.",
                'data'    => $seguimientos
            ], 200);

        } catch (ModelNotFoundException $e) {
            // Capturar si el Registro (caso) no fue encontrado
            return response()->json([
                'success' => false,
                'message' => "El registro (caso) con ID: {$registroId} no fue encontrado.",
                'error'   => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            // Capturar cualquier otra excepción inesperada
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al buscar los seguimientos: ' . $e->getMessage(),
            ], 500);
        }
    }
}