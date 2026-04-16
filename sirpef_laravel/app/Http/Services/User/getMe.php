<?php
namespace App\Http\Services\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class getMe
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    static public function get()
    {
        $user = Auth::user();

        // Verificar si el usuario tiene una persona asociada
        if (!$user || !$user->persona) {
            return [
                'error' => 'Usuario o persona no encontrados.',
            ];
        }

        $persona = $user->persona;

        // Verificar si la persona tiene un ministerio asociado
        $unid = $persona->ministerio ? $persona->ministerio->nombre : 'Sin ministerio';

        return [
            'id' => $user->id,
            'name' => $persona->nombre_completo,
            'email' => $user->email,
            'unid' => $unid,
            'role_id' => $user->role_id,
            'cedula' => $persona->cedula,
            'isAdmin' => $user->isAdmin(),
        ];
    }
}