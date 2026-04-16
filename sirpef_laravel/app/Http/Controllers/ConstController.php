<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Const\obtenerCedulaServiceFeDeVida;
use App\Http\Services\Const\ObtenerPersonasService;
use App\Http\Services\Const\GetUserInfoService;
use App\Http\Services\Const\checkpdfFeDeVida;
use Illuminate\Http\Request;
use App\Models\Persona;

class ConstController extends Controller
{
    
     public function obtenerPersonas()
    {
        $user = auth()->user();

      /*   $data = (object) [
            'is_admin' => $user->is_admin,
            "evento_activo" => $user->evento_activo,
            "oficina_asignada" => "[18]",
            "unid_activa" => "[18]"
        ]; */


        return ObtenerPersonasService::obtenerPersonas(/* $data */);  
    }


    public function getUserInfo()
    {
        return GetUserInfoService::getUserInfo();  
    }
    

        public static function findByCedula($cedula)
    {
        return ObtenerPersonasService::findByCedula($cedula);  
    }
    

    public function obtenerPorCedulaFeDeVida($cedula)
    {
        return obtenerCedulaServiceFeDeVida::obtenerPorCedulaFeDeVida($cedula);  
    }

    public function checkpdf($cedula)
    {
        return checkpdfFeDeVida::checkpdf($cedula);  
    }

}
