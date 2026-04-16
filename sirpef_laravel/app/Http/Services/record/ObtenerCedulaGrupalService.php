<?php

namespace App\Http\Services\record;

use App\Http\Controllers\ConstController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;

class ObtenerCedulaGrupalService
{
    static public function obtenerPorCedulaGrupal($cedula)
    {
        $user = auth()->user();
        $ConstController = new ConstController();
        $personasFiltradas = $ConstController->obtenerPersonas();

        $persona = $personasFiltradas->firstWhere('cedula', $cedula);

        if (!$persona) {
            return response()->json([
                'msg' => 'El número de cédula que está ingresando no se encuentra en su base de datos, verifica el número de cédula o No pertenece al evento actual.'
            ], 404);
        }

        // Check if the person already has a record
        if ($persona->registrosEventoActivo()->exists()) {
            return response()->json(['msg' => 'Esta persona ya tiene una participación registrada'], 400);
        }

        // Prepare the response
        $respuesta = [
            'id' => $persona->id,
            'nombre_completo' => $persona->nombre_completo,
            'cedula' => $persona->cedula,
            'direccion' => $persona->direccion,
            'cargo' => $persona->cargo,
            'telefono' => $persona->telefono ? $persona->telefono : 'No especificado',
            'unidad_Adscrita' => 'No especificado'

        ];

        return response()->json($respuesta);
    }
}