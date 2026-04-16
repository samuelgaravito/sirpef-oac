<?php

namespace App\Http\Services\Statistic;

use App\Http\Controllers\ConstController;
use Illuminate\Http\Request;

class IndexStatisticService
{
    static public function execute(Request $request)
    {
        $perPage = 15;  // Define la cantidad de resultados por página
        $ConstController = new ConstController();

        // Obtener el query builder desde el servicio ObtenerPersonasService
        $personasQuery = $ConstController->obtenerPersonas();

        // Aplicar filtro de búsqueda si se proporciona
        if ($request->has('search')) {
            $search = $request->input('search');
            $personasQuery = $personasQuery->where(function ($query) use ($search) {
                $query->where('nombre_completo', 'like', "%$search%")
                    ->orWhere('cedula', 'like', "%$search%");
            });
        }

        // Aplicar ordenamiento si se proporciona
        if ($request->has('sort')) {
            $sort = $request->input('sort');
            $direction = $request->input('direction');

            // Validar el campo de ordenamiento
            $allowedSortFields = ['nombre_completo', 'cedula', 'created_at']; // Agrega aquí los campos que deseas permitir
            if (!in_array($sort, $allowedSortFields)) {
                $sort = 'nombre_completo'; // Establecer un valor predeterminado
            }

            // Validar la dirección de ordenamiento
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'asc'; // Establecer un valor predeterminado
            }

            $personasQuery = $personasQuery->orderBy($sort, $direction);
        }

        // Aplicar paginación y ejecutar la consulta
        $personas = $personasQuery->paginate($perPage);

        // Añadir el campo participación basado en el valor del campo voto o null
        $personas->getCollection()->transform(function ($persona) {
            $registro = $persona->registrosEventoActivo()->first();
            $persona->participacion = $registro ? $registro->voto : null;
            return $persona;
        });

        // Estructurar la respuesta JSON
        return response()->json([
            "rows" => [
                "current_page" => $personas->currentPage(),
                "data" => $personas->items(),
                "first_page_url" => $personas->url(1),
                "from" => $personas->firstItem(),
                "last_page" => $personas->lastPage(),
                "last_page_url" => $personas->url($personas->lastPage()),
                "links" => $personas->linkCollection()->toArray(),
                "next_page_url" => $personas->nextPageUrl(),
                "path" => $personas->path(),
                "per_page" => $personas->perPage(),
                "prev_page_url" => $personas->previousPageUrl(),
                "to" => $personas->lastItem(),
                "total" => $personas->total(),
            ]
        ]);
    }
}