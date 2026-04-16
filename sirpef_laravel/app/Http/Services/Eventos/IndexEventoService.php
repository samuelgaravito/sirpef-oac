<?php
namespace App\Http\Services\Eventos;

use Illuminate\Support\Facades\Auth;
use App\Models\Evento;

class IndexEventoService
{
    static public function getAllEventos()
    {
        $user = Auth::user();

        if ($user->is_admin) {
            $eventos = Evento::orderBy('id', 'desc')->get();
        } else {
            $eventoAsignados = json_decode($user->configUser->evento_asignado, true);
            if (!is_array($eventoAsignados)) {
                $eventoAsignados = [$eventoAsignados];
            }
            $eventos = Evento::whereIn('id', $eventoAsignados)->orderBy('id', 'desc')->get();
        }

        return $eventos;
    }

    static public function setEventoActivo($eventoId)
    {
        $user = Auth::user();
        $configUser = $user->configUser;
        $configUser->evento_activo = $configUser->evento_activo != $eventoId ? $eventoId : null;
        $configUser->save();

        return response()->json(['mensaje' => 'Se ha cambiado el evento', 'event_id' => $eventoId]);

    }
}