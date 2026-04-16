<?php

namespace App\Http\Services\AtencionCiudadano;

use App\Models\Registro;
use App\Models\Seguimiento;
use App\Models\EstatusTramite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB; // Ya no es necesario importar DB si no se usan transacciones

class AtencionCiudadanoService
{
    public static function actualizarEstatus(int $registroId, int $estatusTramiteId, Request $request): JsonResponse
    {
        // Se elimina DB::beginTransaction();

        try {
            $registro = Registro::findOrFail($registroId);

            $observacionRegistroPrincipal = $request->input('observacion');
             $nuevoEstatus = $request->input('estatus_caso');
            // 1. Validar que el estatus_tramite_id proporcionado sea válido y exista en la tabla EstatusTramite.
            $estatusTramite = EstatusTramite::find($estatusTramiteId);

            if (!$estatusTramite) {
                // Se elimina DB::rollBack();
                return response()->json([
                    'error' => 'El ID del estatus de trámite proporcionado no es válido o no existe.',
                    'provided_estatus_id' => $estatusTramiteId
                ], 400); // Bad Request
            }

            // 2. Actualizar los campos del registro principal (tbl_registros)
            $registro->estatus_caso = $nuevoEstatus;
            $registro->voto = true;
            $registro->observacion = $observacionRegistroPrincipal;
            $registro->save();

            // 3. Crear un nuevo registro en la tabla de seguimiento (tbl_seguimiento)
            $seguimiento = Seguimiento::create([
                'registro_id'        => $registro->id,
                'estatus_tramite_id' => $estatusTramiteId,
                'observacion' => $observacionRegistroPrincipal
            ]);

            // Se elimina DB::commit();

            $seguimiento->load('estatusTramite');

            return response()->json([
                'message'            => 'Estatus de caso actualizado y seguimiento registrado correctamente.',
                'registro_actualizado' => $registro,
                'seguimiento_creado' => $seguimiento
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Se elimina DB::rollBack();
            return response()->json(['error' => 'El registro (caso) con el ID proporcionado no fue encontrado.'], 404);
        } catch (\Exception $e) {
            // Se elimina DB::rollBack();
            return response()->json(['error' => 'Error inesperado al procesar la actualización del estatus y el seguimiento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Marca un caso para revisión estableciendo el campo 'voto' en false.
     *
     * @param int $registroId El ID del registro a marcar para revisión.
     * @return JsonResponse
     */
    public static function enviarRevision(int $registroId): JsonResponse
    {
        try {
            $registro = Registro::findOrFail($registroId);

            $registro->voto = false; // Cambia el campo 'voto' a false
            $registro->save();

            return response()->json([
                'message' => 'Caso marcado para revisión correctamente (voto establecido en false).',
                'registro' => $registro
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Registro no encontrado.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al marcar el caso para revisión: ' . $e->getMessage()], 500);
        }
    }
}