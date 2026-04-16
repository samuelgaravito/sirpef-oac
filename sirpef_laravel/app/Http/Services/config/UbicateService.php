<?php 

namespace App\Http\Services\config;
use Illuminate\Http\{
    Request,
    JsonResponse
};

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Centro;
use App\Models\Ministerio;
use App\Models\Pais;


class UbicateService {

    static public function getPaises()
    {
        $paises = Pais::all(['id','pais']);
        
        return response()->json($paises);
    }


   static public function getDireccion()
{
    $estados = Estado::all(['id','estado']);
    $ministerio = Ministerio::first(); // Assuming you want to retrieve the first ministerio
    
    return response()->json([
        $estados,
        $ministerio,
    ]);
}



    static public function getMunicipio($estado)
    {


        $municipios = Municipio::where('estado_id', $estado)->get();
        return response()->json([
            $municipios,
        ]);
    }


    static public function getParroquia($municipio)
    {    
        $parroquias = Parroquia::where('municipio_id', $municipio)->get();
        return response()->json([
           $parroquias,
        ]);
    }


    static public function getCentro()
    {    
        $centros = Centro::all();
        return response()->json([
           $centros,
        ]);
    }
}