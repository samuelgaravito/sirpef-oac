<?php
namespace App\Http\Services\Eventos;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\User;

class ChangeEstatusService
{
    static public function changeEventoStatus(Request $request, $id)
    {
        // Obtiene el evento a actualizar
        $evento = Evento::find($id);

        // Verifica si el evento existe
        if (!$evento) {
            Log::error("Evento no encontrado: ID {$id}");
            return response()->json(['error' => 'Evento no encontrado'], 404);
        }

        // Cambia el estatus del evento
        $evento->estatus = !$evento->estatus;

        // Guarda los cambios
        try {
            $evento->save();
            Log::info("Estatus del evento actualizado: ID {$evento->id}, Estatus: {$evento->estatus}");
        } catch (\Exception $e) {
            Log::error("Error al guardar el estatus del evento: " . $e->getMessage());
            Log::error("Trace: " . $e->getTraceAsString());

            return response()->json([
                'error' => 'Error al actualizar el evento',
                'detalle' => $e->getMessage()
            ], 500);
        }

        // Solo actualizar usuarios si el estatus del evento es false
        if (!$evento->estatus) {
            try {
                $users = User::where('is_admin', false)
                    ->whereHas('configUser', function ($query) use ($id) {
                        $query->where('evento_activo', $id);
                    })
                    ->get();

                if ($users->isEmpty()) {
                    Log::info("No se encontraron usuarios con el evento activo: ID del evento {$id}");
                    return response()->json(['mensaje' => 'No se encontraron usuarios con el evento activo']);
                }

                Log::info("Usuarios encontrados: " . count($users) . " para el evento ID {$id}");

                $usuariosActualizados = $users->map(function ($user) {
                    $user->configUser->evento_activo = null;
                    $user->configUser->save();
                    Log::info("Usuario actualizado: ID {$user->id}, Evento Activo cambiado a null");
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'evento_activo' => $user->configUser->evento_activo,
                    ];
                });

                return response()->json([
                    'mensaje' => 'Evento y usuarios actualizados con éxito',
                    'usuarios_actualizados' => $usuariosActualizados,
                ]);
            } catch (\Exception $e) {
                Log::error("Error al buscar o actualizar usuarios con evento activo: " . $e->getMessage());
                return response()->json([
                    'error' => 'Error al buscar o actualizar usuarios con evento activo',
                    'detalle' => $e->getMessage()
                ], 500);
            }
        }

        // Retorna un mensaje de éxito si solo se cambió el estatus del evento pero no se necesitaba actualizar usuarios
        return response()->json(['mensaje' => 'Estatus del evento actualizado con éxito']);
    }
}