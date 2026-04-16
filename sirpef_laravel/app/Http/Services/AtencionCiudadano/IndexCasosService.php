<?php

namespace App\Http\Services\AtencionCiudadano;

use Illuminate\Http\{
    Request,
    JsonResponse
};
use App\Models\Registro;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Importar la clase Log
use Illuminate\Support\Facades\Auth; // Importar la clase Auth
use Illuminate\Support\Facades\DB;   // Importar DB para la subconsulta

class IndexCasosService {

    /**
     * Obtiene los registros paginados con información de personas y tipos de caso.
     * Filtra por evento activo, estatus_caso, y estatus_tramite_id.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public static function index(Request $request): JsonResponse
    {
        try {
            // Validar parámetros
            $request->validate([
                'page' => 'sometimes|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:100',
                'tipo_caso_id' => 'sometimes|integer|exists:tbl_tipo_caso,id',
                'estatus_caso' => 'sometimes|string|in:En Tramite,Orientado,Resultado Directo,Remitido a Otro,Cerrado',
                'estatus_tramite_id' => 'sometimes|integer|exists:tbl_estatus_tramite,id'
            ]);

            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);

            // Obtener el usuario autenticado
            $user = Auth::user();

            // Consulta base con relaciones
            $query = Registro::with([
                'eventoPersona.persona',
                'tipoCaso',
            ])->orderBy('created_at', 'desc');

            // --- APLICAR FILTRO DE EVENTO ACTIVO ---
            self::filtrarPorEventoActivo($query, $user);

            // Filtro por tipo de caso
            if ($request->has('tipo_caso_id')) {
                $query->where('id_tipo_caso', $request->tipo_caso_id);
            }

            // Filtro por estatus de caso principal
            if ($request->has('estatus_caso')) {
                $query->where('estatus_caso', $request->estatus_caso);
            }

            // Filtro por estatus de trámite (sub-estado)
         if ($request->has('estatus_tramite_id') && $request->estatus_tramite_id != 0) { // <-- La clave está aquí
                $latestSeguimientoSubquery = DB::table('tbl_seguimiento')
                    ->select('registro_id', DB::raw('MAX(id) as last_id'))
                    ->groupBy('registro_id');

                $query->whereIn('id', function($subQuery) use ($request, $latestSeguimientoSubquery) {
                    $subQuery->select('s.registro_id')
                             ->from('tbl_seguimiento as s')
                             ->joinSub($latestSeguimientoSubquery, 'latest', function($join) {
                                 $join->on('s.id', '=', 'latest.last_id');
                             })
                             ->where('s.estatus_tramite_id', $request->estatus_tramite_id);
                });
            }

            // Filtro de búsqueda
            if ($request->has('search')) {
                $query->whereHas('eventoPersona.persona', function($q) use ($request) {
                    $q->where('nombre_completo', 'like', '%'.$request->search.'%')
                      ->orWhere('cedula', 'like', '%'.$request->search.'%');
                });
            }

            // Ejecutar consulta paginada
            $registros = $query->paginate($perPage, ['*'], 'page', $page);

            // Transformar los resultados
            $datosTransformados = $registros->getCollection()->map(function ($registro) {
                $persona = $registro->eventoPersona->persona ?? null;

                return [
                    'nombre_completo' => $persona->nombre_completo ?? 'No disponible',
                    'genero' => self::formatearGenero($persona->sexo ?? null),
                    'fecha_nacimiento' => $persona->fecha_nacimiento ?? 'No disponible',
                    'cedula' => $persona->cedula ?? 'No disponible',
                    'tipo_caso' => $registro->tipoCaso->tipo ?? 'Sin tipo',
                    'descripcion' => $registro->descripcion ?? 'Sin descripción',
                    'fecha_registro' => $registro->created_at->format('d/m/Y H:i:s'),
                    'hora_voto' => $registro->hora_voto ?? 'No registrada',
                    'estatus_caso' => $registro->estatus_caso ?? '-',
                    'havePunto' => $registro->punto_cuenta_id ? true : false,
                    'estatus' => $registro->voto,
                    'registro_id' => $registro->id
                ];
            });

            // Reemplazar la colección en la paginación
            $registros->setCollection($datosTransformados);

            return response()->json([
                'success' => true,
                'rows' => $registros->items(),
                'pagination' => [
                    'total' => $registros->total(),
                    'per_page' => $registros->perPage(),
                    'current_page' => $registros->currentPage(),
                    'last_page' => $registros->lastPage(),
                    'from' => $registros->firstItem(),
                    'to' => $registros->lastItem()
                ]
            ]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Error en IndexCasosService::index: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los registros',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filtra la consulta de registros por el evento activo del usuario y su oficina asignada.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $user El objeto de usuario autenticado.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    static private function filtrarPorEventoActivo($query, $user)
    {
        try {
            if (!empty($user->configUser->evento_activo)) {
                $eventoId = $user->configUser->evento_activo;

                $query->whereHas('eventoPersona', function ($epQuery) use ($eventoId, $user) {
                    $epQuery->where('evento_id', $eventoId);

                    // Aplicar el filtro de oficina asignada si existe
                    if (!empty($user->configUser->oficina_asignada)) {
                        $oficinaAsignada = json_decode($user->configUser->oficina_asignada, true);
                        // El filtro se aplica en la tabla 'tb_evento_persona'

                    }
                });
            }
        } catch (\Exception $e) {
            Log::error("Error en filtrarPorEventoActivo (IndexCasosService): " . $e->getMessage());
            // En este caso, no se debe detener la ejecución, solo registrar el error y continuar.
        }
        return $query;
    }

    /**
     * Formatea el género para una mejor presentación
     */
    private static function formatearGenero(?string $genero): string
    {
        return match ($genero) {
            'M' => 'Masculino',
            'F' => 'Femenino',
            default => 'No especificado'
        };
    }
}
