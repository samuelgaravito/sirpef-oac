<?php

namespace App\Http\Services\AtencionCiudadano;

use App\Models\PuntoCuenta;
use Illuminate\Http\JsonResponse;

class GetPuntoCuentaByNumeroService {

    /**
     * Obtiene los detalles de un Punto de Cuenta específico por su Número.
     *
     * @param string $numero El número del Punto de Cuenta a buscar.
     * @return JsonResponse
     */
    public static function getByNumero(string $numero): JsonResponse
    {
        $puntoCuenta = PuntoCuenta::where('nro_punto_cuenta', $numero)->first();

        if (!$puntoCuenta) {
            return response()->json([
                'success' => false,
                'message' => 'Punto de Cuenta no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $puntoCuenta->id,
                'numero' => $puntoCuenta->nro_punto_cuenta,
                'fecha' => $puntoCuenta->fecha_punto_cuenta,
                'solicitante' => $puntoCuenta->solicitante,
                'cedula' => $puntoCuenta->cedula,
                'monto' => $puntoCuenta->monto_total,
                'proveedor' => $puntoCuenta->proveedor,
            ]
        ], 200);
    }
}
