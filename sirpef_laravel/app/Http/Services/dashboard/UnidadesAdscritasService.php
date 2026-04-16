<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use ClickSend\Configuration;
use ClickSend\Api\SMSApi;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use GuzzleHttp\Client;

use App\Models\Persona;
use App\Models\Ente;
use App\Models\Genero;
use App\Models\Centro;
use App\Models\Registro;
use App\Models\UnidadAdscrita;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;



class UnidadesAdscritasService
{

     /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function unidadesAdscritasDelUsuario()
    {
        // Obtén el ID del usuario logueado
        $userId = auth()->id();
        $user = auth()->user();
        $entes =Ente::all();
        // Encuentra las unidades adscritas a través de las personas asociadas con este usuario
        if ($user->isAdmin()) {
            // Si el usuario es administrador, devuelve todas las unidades adscritas
            $unidadesAdscritas = UnidadAdscrita::all();
            $ente_activo = 0;
        } else {
            $unidadesAdscritas = UnidadAdscrita::whereHas('persona', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get();
            $ente_activo = $user->persona()->first()->entes_id;
        }
        
        // Construye un array que contenga solamente los IDs y nombres de las unidades adscritas
        $unidades = $unidadesAdscritas->map(function ($unidad) use ($user) {
            return [
                'id' => $unidad->id,
                'nombre' => $unidad->nombre,
                'active' => $unidad->id == $user->unid_activa
            ];
        });
        
       if ($user->isAdmin()) {

        $unidades[] = [
            'id' => 0,
            'nombre' => 'Todas', // o "Sin unidad asignada"
            'active' => 0 == $user->unid_activa
        ];
       }


       $entesToSend = $entes->map(function ($ente) use ($ente_activo){
        return [
            'id' => $ente->id,
            'nombre' => $ente->entes,   
            'active' => $ente->id == $ente_activo
        ];
    });
    
        // Devuelve los IDs de las unidades adscritas en formato JSON
        return response()->json([
            'unidad_adscrita'=> $unidades,
            'entes' => $entesToSend,
        ]);
    }

}
