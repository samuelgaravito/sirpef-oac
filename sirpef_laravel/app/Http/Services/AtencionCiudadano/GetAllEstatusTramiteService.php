<?php


namespace App\Http\Services\AtencionCiudadano; 

use App\Models\EstatusTramite;
use Illuminate\Http\JsonResponse;

class GetAllEstatusTramiteService
{
    /**
     * Obtiene todos los estatus de trámite disponibles.
     *
     * @return JsonResponse
     */
    public function getAllEstatusTramite(): JsonResponse
    {
        try {
            // Simplemente obtiene todos los registros de la tabla tbl_estatus_tramite
            $estatus = EstatusTramite::all();

            if ($estatus->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No se encontraron estatus de trámite.',
                    'data'    => []
                ], 200);
            }

            return response()->json(
                $estatus
            , 200);

        } catch (\Exception $e) {
            // Captura cualquier excepción inesperada
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al obtener los estatus de trámite: ' . $e->getMessage(),
            ], 500);
        }
    }
}