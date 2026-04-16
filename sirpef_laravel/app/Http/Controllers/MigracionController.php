<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Services\Migracion\ImportCedulaService;
use App\Http\Services\Migracion\ImportFinalService;
use App\Http\Services\Migracion\SearchCedulaService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PersonasImport; 
use App\Models\Persona;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Centro;

class MigracionController extends Controller
{


    public function importFinal(Request $request)

    {
        return ImportFinalService::importFinal($request);    
    }


    public function importCedulas(Request $request)
    {
        return ImportCedulaService::importCedulas($request);    
    }
    
    private function obtenerPersonaPorCedula($cedula)
    {
        return SearchCedulaService::obtenerPersonaPorCedula($cedula);    
    }













}

