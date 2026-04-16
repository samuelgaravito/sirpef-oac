<?php

namespace App\Http\Services\Statistic;

use Illuminate\Support\Facades\DB;
use App\Models\Registro; // Importa el modelo Registro
use App\Models\TipoCaso; // Importa el modelo TipoCaso

class EstadisticaInicioService
{

    static public function getRegistrosPorTipoCaso()
    {
        // Utiliza la relación definida en el modelo TipoCaso para cargar los registros
        // y luego contar la cantidad de registros por cada tipo.
        $registrosPorTipoCaso = TipoCaso::withCount('registros')
            ->get()
            ->map(function ($tipoCaso) {
                return [
                    'id' => $tipoCaso->id, // Puedes incluir el ID si lo necesitas
                    'tipo' => $tipoCaso->tipo,
                    'color'=> $tipoCaso->color,
                    'cantidad_registros' => $tipoCaso->registros_count,
                ];
            });

        return $registrosPorTipoCaso;
    }
}