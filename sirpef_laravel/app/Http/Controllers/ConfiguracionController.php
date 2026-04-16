<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 
use ClickSend\Configuration;
use ClickSend\Api\SMSApi;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use GuzzleHttp\Client as ClientSms;
use ElephantIO\Client;
use App\Models\Persona;
use App\Models\Ente;
use App\Models\Genero;
use App\Models\Registro;

use Illuminate\Http\{
    Request,
    JsonResponse
};


# services
use App\Http\Services\config\UnidadAdscritaService;
use App\Http\Services\config\TipoService;
use App\Http\Services\config\AuditService;
use App\Http\Services\config\UbicateService;
use App\Http\Services\config\CreatePersonService;

class ConfiguracionController extends Controller
{

    /*public function indexEntes()
    {
        $entes = Ente::all();

        return response()->json(['entes' => $entes]);
    }
    
    public function createEntes()
    {
        return response()->json(['message' => 'Crear ente']);
    }
    
    public function storeEntes(Request $request)
    {
        $ente = new Ente();
        $ente->entes = $request->input('entes');
        $ente->save();
        return response()->json(['message' => 'Ente creado con éxito', 'ente' => $ente]);
    }
    
    public function showEntes($id)
    {
        $ente = Ente::find($id);
        return response()->json(['ente' => $ente]);
    }
    
    public function editEntes($id)
    {
        $ente = Ente::find($id);
        return response()->json(['ente' => $ente]);
    }
    
    public function updateEntes(Request $request, $id)
    {
        $ente = Ente::find($id);
        $ente->entes = $request->input('entes');
        $ente->save();
        return response()->json(['message' => 'Ente actualizado con éxito', 'ente' => $ente]);
    }
    
    public function destroyEntes($id)
    {
        $ente = Ente::find($id);
        $ente->delete();
        return response()->json(['message' => 'Ente eliminado con éxito']);
    }*/


   public function indexMinisterios()
    {
        return unidadAdscritaService::indexMinisterios();  
    }


    public function createMinisterio()
    {
        return unidadAdscritaService::createMinisterio();  
    }


    public function storeMinisterio(Request $request)
    {
        return unidadAdscritaService::storeMinisterio($request);  

    }


    public function showMinisterio($id)
    {
        return unidadAdscritaService::showMinisterio($id);  
    }


    public function editMinisterio($id)
    {
        return unidadAdscritaService::editMinisterio($id);  

    }


    public function updateMinisterio(Request $request, $id)
    {
        return unidadAdscritaService::updateMinisterio($request, $id);  
    }


    public function destroyMinisterio($id)
    {
        return unidadAdscritaService::destroyMinisterio($id);  
    }






    public function indexTipos()
    {
        return TipoService::indexTipos();  
    }


    public function createTipo()
    {
        return TipoService::createTipo();  
    }


    public function storeTipo(Request $request)
    {
        return TipoService::createTipo($request);  
    }


    public function showTipo($id)
    {
        return TipoService::showTipo($id);  
    }


    public function editTipo($id)
    {
        return TipoService::editTipo($id);  
    }


    public function updateTipo(Request $request, $id)
    {
        return TipoService::updateTipo($request,$id);  
    }


    public function destroyTipo($id)
    {
        return TipoService::destroyTipo($id);  
    }











    public function indexAuditorias()
    {
        return AuditService::indexAuditorias();  
    }


    public function createAuditoria()
    {
        return AuditService::createAuditoria();  
    }


    public function storeAuditoria(Request $request)
    {
        return AuditService::storeAuditoria();  

    }


    public function showAuditoria($id)
    {
        return AuditService::showAuditoria($id); 
    }


    public function editAuditoria($id)
    {
        return AuditService::editAuditoria($id); 
    }


 /*    public function updateAuditoria(Request $request, $id)
    {
        return AuditService::updateAuditoria($request, $id); 
    }
 */

    public function destroyAuditoria($id)
    {
        return AuditService::destroyAuditoria($id); 

    }


    public function getPaises()
    {
        return UbicateService::getPaises(); 
    }


    public function getDireccion()
    {
        return UbicateService::getDireccion(); 
    }



    public function getMunicipio($estado)
    {
        return UbicateService::getMunicipio($estado); 
    }

    public function getParroquia($municipio)
    {    
        return UbicateService::getParroquia($municipio);
    }


    public function getCentro()
    {    
        return UbicateService::getCentro();
    }


    public function getUnidadAdscrita()
    {
        return UnidadAdscritaService::getUnidadAdscrita();
    }

    // Crear el nuevo registro

    public function create(Request $request)
    {
        return CreatePersonService::create($request);
    }

}
