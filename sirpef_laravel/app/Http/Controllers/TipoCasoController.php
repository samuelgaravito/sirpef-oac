<?php

namespace App\Http\Controllers;

use App\Models\TipoCaso;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Asegúrate de que esta línea esté presente

class TipoCasoController extends Controller
{
    /**
     * Muestra una lista de los recursos.
     * Si no se especifica un ID de padre, muestra los tipos de caso raíz.
     * Si se especifica un ID de padre, muestra sus hijos.
     */
    public function index(Request $request)
    {
        // Puedes filtrar por el tipo_caso_padre_id para obtener la jerarquía
        // Por ejemplo: /api/tipos-caso?parent_id=1 para obtener hijos del tipo de caso con ID 1
        // O /api/tipos-caso?parent_id=null para obtener solo los tipos de caso raíz
        $parentId = $request->query('parent_id');

        if ($parentId === 'null') { // Para buscar específicamente los que no tienen padre
            $tiposCasos = TipoCaso::whereNull('tipo_caso_padre_id')->get();
        } elseif ($parentId) { // Para buscar hijos de un padre específico
            $tiposCasos = TipoCaso::where('tipo_caso_padre_id', $parentId)->get();
        } else { // Si no se especifica parent_id, puedes decidir si mostrar todos o solo los raíz por defecto
            $tiposCasos = TipoCaso::all(); // Por defecto, muestra todos los tipos de caso
            // O si prefieres, para mostrar solo los raíz por defecto:
            // $tiposCasos = TipoCaso::whereNull('tipo_caso_padre_id')->get();
        }

        // Si quieres devolver los hijos anidados para cada tipo de caso, puedes cargar la relación:
        // $tiposCasos = TipoCaso::with('hijos')->whereNull('tipo_caso_padre_id')->get(); // Para una vista jerárquica desde la raíz

        return response()->json($tiposCasos);
    }

    /**
     * Almacena un nuevo recurso en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string|max:100|unique:tbl_tipo_caso,tipo',
            'color' => 'nullable|string|max:7',
            // Agregamos la validación para el campo recursivo
            'tipo_caso_padre_id' => [
                'nullable', // Puede ser nulo si es un tipo de caso raíz
                'integer',  // Debe ser un número entero
                'exists:tbl_tipo_caso,id', // Debe existir en la tabla tbl_tipo_caso
                // Asegurarse de que el padre no sea el propio tipo de caso (previene bucles infinitos)
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->id && $value == $request->id) {
                        $fail('Un tipo de caso no puede ser su propio padre.');
                    }
                },
            ],
        ]);

        $tipoCaso = TipoCaso::create($request->all());
        
        return response()->json([
            'message' => 'Tipo de caso creado exitosamente',
            'data' => $tipoCaso
        ], Response::HTTP_CREATED);
    }

    /**
     * Muestra el recurso especificado.
     */
    public function show(TipoCaso $tipoCaso)
    {
        // Si deseas mostrar el padre y los hijos del tipo de caso específico:
        $tipoCaso->load('padre', 'hijos'); // Carga las relaciones
        return response()->json($tipoCaso);
    }

    /**
     * Actualiza el recurso especificado en la base de datos.
     */
    public function update(Request $request, TipoCaso $tipoCaso)
    {
        $request->validate([
            'tipo' => 'required|string|max:100|unique:tbl_tipo_caso,tipo,'.$tipoCaso->id,
            'color' => 'nullable|string|max:7',
            // Agregamos la validación para el campo recursivo
            'tipo_caso_padre_id' => [
                'nullable',
                'integer',
                'exists:tbl_tipo_caso,id',
                // Validación para evitar que un tipo de caso sea su propio padre o un ancestro/descendiente
                function ($attribute, $value, $fail) use ($tipoCaso) {
                    if ($value && $value == $tipoCaso->id) {
                        $fail('Un tipo de caso no puede ser su propio padre.');
                    }
                    // Opcional: Para evitar bucles, puedes añadir lógica para prevenir que un tipo de caso
                    // se asigne como padre a uno de sus propios descendientes. Esto requeriría una
                    // función de verificación más compleja (ej. recursiva) en el modelo o un servicio.
                    // Por simplicidad, no la incluyo aquí, pero es una consideración importante para jerarquías profundas.
                },
            ],
        ]);

        $tipoCaso->update($request->all());
        
        return response()->json([
            'message' => 'Tipo de caso actualizado exitosamente',
            'data' => $tipoCaso
        ]);
    }

    /**
     * Elimina el recurso especificado de la base de datos.
     */
    public function destroy(TipoCaso $tipoCaso)
    {
        // Verificar si hay registros asociados
        if($tipoCaso->registros()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar, existen registros asociados a este tipo de caso.'
            ], Response::HTTP_CONFLICT);
        }

        // Verificar si tiene tipos de caso hijos.
        // Dada la política onDelete('set null'), los hijos no impedirían la eliminación,
        // pero podrías quererlo como una validación de negocio.
        if ($tipoCaso->hijos()->exists()) {
            // Opción 1: Devolver un error si tiene hijos
            return response()->json([
                'message' => 'No se puede eliminar, existen tipos de caso hijos asociados a este tipo. Elimine los hijos primero o reasígnelos.'
            ], Response::HTTP_CONFLICT);
            // Opción 2: Permitir la eliminación (los hijos se reasignarán a null por 'onDelete set null')
            // $tipoCaso->delete();
            // return response()->json(['message' => 'Tipo de caso eliminado. Sus hijos ahora no tienen padre.'], Response::HTTP_OK);
        }

        $tipoCaso->delete();
        
        return response()->json([
            'message' => 'Tipo de caso eliminado exitosamente.'
        ], Response::HTTP_NO_CONTENT);
    }
}