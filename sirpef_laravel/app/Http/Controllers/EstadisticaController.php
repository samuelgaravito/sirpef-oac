<?php

namespace App\Http\Controllers;

use App\Http\Services\Const\ObtenerPersonasService;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Statistic\EstadisticaParticipacionHoraService;
use App\Http\Services\Statistic\EstadisticaParticipacionService;
use App\Http\Services\Statistic\EstadisticaEdadSexoService;
use App\Http\Services\Statistic\EstadisticaSexoService;
use App\Http\Services\Statistic\EstadisticaEstadoService;
use App\Http\Services\Statistic\PorcentajeParticipacionService;
use App\Http\Services\Statistic\PorcentajeParticipacionEstadoService;
use App\Http\Services\Statistic\FiltroPersonaUnidadService;
use App\Http\Services\Statistic\FiltroPersonaEstadoService;
use App\Http\Services\Statistic\EstadisticaParticipacionFeDeVidaService;
use App\Http\Services\Statistic\EstadisticaInicioService;
use App\Http\Services\Statistic\TreeviewStatsService;
use ClickSend\Configuration;
use ClickSend\Api\SMSApi;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use GuzzleHttp\Client as ClientSms;
use ElephantIO\Client;
use Carbon\Carbon;
use App\Models\Persona;
use App\Models\Ente;
use App\Models\Genero;
use App\Models\Centro;
use App\Models\Registro;
use App\Models\UnidadAdscrita;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use App\Models\Tipo;
use App\Models\TipoCaso;
use Illuminate\Http\{
    Request,
    JsonResponse
};
use Illuminate\Support\Facades\DB;

class EstadisticaController extends Controller

{


        public function getRegistrosPorTipoCaso(): JsonResponse
    {
        $data = EstadisticaInicioService::getRegistrosPorTipoCaso();
        return response()->json($data);
    }



    public function getunidadesAdscritasGeneral()
    {
        return \App\Http\Services\Statistic\FiltroMinisterioService::getunidadesAdscritasGeneral();
    }



public function unidadesAdscritasDelUsuario()
{
    return \App\Http\Services\Statistic\FiltroMinisterioService::unidadesAdscritasDelUsuario();
}


public function unidadAdscritaUsuario(Request $request)
{
    return \App\Http\Services\Statistic\FiltroMinisterioService::unidadesAdscritasGeneral($request);
}


public function index(Request $request)//lista de personal asignado
{
    return \App\Http\Services\Statistic\IndexStatisticService::execute($request);
}


public function contarPersonasPorGenero() // calcular el total de personas M y F
{
    return \App\Http\Services\Statistic\ConteoGeneroService::contarPersonasPorGenero();
}


public function GetResumenData($fechaDesde = null, $fechaHasta = null,$tipo_caso_id =null)
{
    return EstadisticaParticipacionService::GetResumenData($fechaDesde, $fechaHasta ,$tipo_caso_id);
}

public function GetResumenDataFeDeVida($fechaDesde = null, $fechaHasta = null)
{
    return EstadisticaParticipacionFeDeVidaService::GetResumenDataFeDeVida($fechaDesde, $fechaHasta );
}


public function GetResumenDatahora($fechaDesde = null, $fechaHasta = null)
{
    return EstadisticaParticipacionHoraService::GetResumenDatahora($fechaDesde, $fechaHasta);
}


public function GetResumenDataBySexAge($fechaDesde = null, $fechaHasta = null)
{
    return EstadisticaEdadSexoService::GetResumenDataBySexAge($fechaDesde, $fechaHasta);
}


public function GetResumenDataBySex($fechaDesde = null, $fechaHasta = null)
{
    return EstadisticaSexoService::GetResumenDataBySex($fechaDesde, $fechaHasta);
}


public function conteoRegistrosPorEstado($fechaDesde = null, $fechaHasta = null)
{
    return EstadisticaEstadoService::conteoRegistrosPorEstado($fechaDesde, $fechaHasta);
}


public function calcularPorcentajeParticipacion($fechaDesde = null, $fechaHasta = null)
{
    return PorcentajeParticipacionService::calcularPorcentajeParticipacion($fechaDesde, $fechaHasta);
}


public function calcularPorcentajeParticipacionPorEstado($fechaDesde = null, $fechaHasta = null)
{
    return PorcentajeParticipacionEstadoService::calcularPorcentajeParticipacionPorEstado($fechaDesde, $fechaHasta);
}


public function filterPersonasXUnid($unid, $voto_status,$tipoCasoId=null,$fechaDesde = null, $fechaHasta = null) {
    return FiltroPersonaUnidadService::filterPersonasXUnid($unid, $voto_status,$tipoCasoId, $fechaDesde, $fechaHasta );
}


public function filterPersonasXEstado($estado_id, $voto_status, $fechaDesde = null, $fechaHasta = null) {
    return FiltroPersonaEstadoService::filterPersonasXEstado($estado_id, $voto_status, $fechaDesde, $fechaHasta);
}

public function getTreeviewStats(): JsonResponse
    {
        return TreeviewStatsService::getStats();
    }




}
