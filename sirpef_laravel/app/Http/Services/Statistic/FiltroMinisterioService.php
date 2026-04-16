<?php

namespace App\Http\Services\Statistic;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Ministerio;
use App\Models\Evento;


class FiltroMinisterioService
{

//funcion para desplegar lista de unidades adscritas
// Función para desplegar lista de unidades adscritas
static public function unidadesAdscritasDelUsuario() {
    $user = auth()->user();
    $oficinasAsignadasConfig = array_map('trim', explode(',', $user->configUser->oficina_asignada));
    $Evento = Evento::find($user->configUser ->evento_activo);
    $oficinasAsignadasEvento =[];
    if ($Evento) {
        $oficinasAsignadasEvento = array_map('trim', explode(',', $Evento->ministerio_id));
    }

    //$oficinas = $user->configUser->evento_activo ? $oficinasAsignadasEvento : $oficinasAsignadasConfig;
    $oficinas = $oficinasAsignadasConfig;

    $unidades = Ministerio::whereIn('id', array_map('intval', $oficinas))
        ->orWhere(function ($query) use ($oficinas) {
            $query->whereNull('ministerio_padre_id')
                ->whereIn('id', function ($query) use ($oficinas) {
                    $query->select('ministerio_padre_id')
                        ->from('tb_ministerio')
                        ->whereIn('id', array_map('intval', $oficinas));
                });
        })
        ->get()
        ->map(function ($ministerio) use ($user) {
            // Asegurarse de que unid_activa sea un array
            $unidActiva = json_decode($user->configUser ->unid_activa, true) ?? [];

            return [
                'id' => $ministerio->id,
                'nombre' => $ministerio->nombre,
                'active' => in_array($ministerio->id, $unidActiva), // Cambiado a in_array
            ];
        });

    // Agregar la opción "Todos"
    $unidades[] = [
        'id' => 0,
        'nombre' => 'Todos',
        'active' => $user->configUser ->unid_activa == '[0]', // Verificar si unid_activa es cero
    ];

    return response()->json([
        'unidad_adscrita' => $unidades,
    ]);
}

//funcion para guardar y asignar una unidad adscrita a un usuario
static public function unidadesAdscritasGeneral(Request $request)
{
    $validated = $request->validate([
        'unidad_adscrita_id' => 'required|numeric'
    ]);

    $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'Usuario no está logueado'], 404);
    }

    $unidadAdscritaId = $validated['unidad_adscrita_id'];

    if ($unidadAdscritaId === [0]) {
        $user->configUser->unid_activa = [0];
    } else {
        $user->configUser->unid_activa = json_encode([$unidadAdscritaId]);
    }

    // Guardar la instancia de ConfigUser completa
    $user->configUser->save();

    $user->save();

    return response()->json(['message' => 'Unidad adscrita actualizada']);
}


static public function getunidadesAdscritasGeneral()
{
    $ministerio = Ministerio::all(['id','nombre']);
    return response()->json(
        $ministerio,
    );
}
}