<?php

namespace App\Http\Services\AtencionCiudadano;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\EventoPersona;
use App\Models\Registro;
use App\Http\Controllers\ConstController;

class ObtenerCedulaService
{
    public static function obtenerPorCedula($cedula)
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Filtrar personas basadas en el evento activo y ministerio_id
        $ConstController = new ConstController();
        $personasFiltradas = $ConstController->obtenerPersonas();

        // Buscar la persona por cédula dentro de las personas filtradas
        $persona = $personasFiltradas->firstWhere('cedula', $cedula);

        if (!$persona) {
            return response()->json([
                'msg' => 'El número de cédula que está ingresando no se encuentra en su base de datos, verifica el número de cédula o verifica si participa en el evento.'
            ], 404);
        }

        // Verificar si la persona está asociada al evento activo
        $eventoPersona = EventoPersona::where('persona_id', $persona->id)
            ->where('evento_id', $user->configUser->evento_activo)
            ->first();

        if (!$eventoPersona) {
            return response()->json([
                'msg' => 'La persona no está asociada al evento activo.'
            ], 404);
        }

        // Verificar si hay registros asociados a este evento_persona

        $registro = Registro::with([
            'tipoCaso',
            'puntoCuenta',
        ])->where('evento_persona_id', $eventoPersona->id)->first();

        // Preparar las respuestas
        $respuesta = [
            'id' => $persona->id,
            'nombre_completo' => $persona->nombre_completo,
            'cedula' => $persona->cedula,
            'organismo' => $persona->ministerio ? $persona->ministerio->nombre : 'No especificado',
            'telefono' => $persona->telefono ? $persona->telefono : 'No especificado',
        ];

        $respuesta2 = [
            'pdc' =>  $registro->punto_cuenta_id ? $registro->puntoCuenta->numero_punto : 'No especificado',
            'monto' => '0bs',
            'estatus' => $registro->punto_cuenta_id ? $registro->puntoCuenta->decision : 'No especificado',
            'Fecha' => $registro->punto_cuenta_id ? $registro->puntoCuenta->created_at : 'No especificado',
            'registro_id' => $registro->id
        ];

        return response()->json([$respuesta, $respuesta2]);
    }
}