<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;

use App\Models\Evento;
use App\Models\User;

use Illuminate\Validation\Rule;
use App\Http\Services\Eventos\IndexEventoService;
use App\Http\Services\Eventos\StoreEventoService;
use App\Http\Services\Eventos\UpdateEventoService;
use App\Http\Services\Eventos\MigrateEventoService;
use App\Http\Services\Eventos\ChangeEstatusService;
use App\Http\Services\Eventos\resetEventService;


class EventoController extends Controller
{

    //funcion para obtener todos los eventos
    public function getAllEventos()
    {
        return IndexEventoService::getAllEventos(); 
    }

    public function setEventoActivo($eventoId)
    {
        return IndexEventoService::setEventoActivo($eventoId); 
    }


    //funcion para obtener todos los ministerios para el storeEvento 
    public function getMinisterios()
    {
        return StoreEventoService::getMinisterios(); 
    }


    //funcion para crear evento
    public function storeEvento(Request $request)
    {
        return StoreEventoService::storeEvento($request); 
    }


    //funcion para obtener todos los ministerios y evento para el updateEvento 
    public function editEvento(Request $request, $id)
    {
        return UpdateEventoService::editEvento($request, $id); 
    }


    //funcion para actualizar evento
    public function updateEvento(Request $request, $id)
    {
        return UpdateEventoService::updateEvento($request, $id); 
    }

       public function resetEvento(Request $request, $id)
    {
        return resetEventService::index($request, $id); 
    }
    
    //funcion para actualizar estatus de evento (activo o inactivo)
    public function changeEventoStatus(Request $request, $id)
    {
        return ChangeEstatusService::changeEventoStatus($request, $id); 
    }



    //funcion para hacer la carga masiva de personas que participan en el evento
    public function cargaMasivaEvento(Request $request, $evento_id)
    {
        return MigrateEventoService::cargaMasivaEvento($request, $evento_id); 
    }
  


    public function getEventosActivosYUltimosAsignados(Request $request)
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener la persona asignada al usuario
        $persona = $user->persona;

        if (!$persona) {
            return response()->json(['message' => 'Persona no encontrada para este usuario.'], 404);
        }

        // Obtener los eventos que están asignados a esta persona y que tienen estatus "activo"
        $eventosActivos = Evento::whereHas('personas', function ($query) use ($persona) {
            $query->where('persona_id', $persona->id);
        })
        ->where('estatus', true) // Filtrar solo eventos con estatus "true" (activos)
        ->get();

        // Obtener los últimos tres eventos asignados a esta persona, ordenados por la fecha de inicio
        $ultimosTresEventos = Evento::whereHas('personas', function ($query) use ($persona) {
            $query->where('persona_id', $persona->id);
        })
        ->orderBy('fecha_inicio', 'desc') // Ordenar por fecha de inicio, descendente
        ->take(3) // Limitar a los últimos 3 eventos
        ->get();

        // Retornar ambas respuestas en un solo JSON
        return response()->json([
            'eventos_activos' => $eventosActivos,
            'ultimos_tres_eventos' => $ultimosTresEventos
        ]);
    }










    


}
