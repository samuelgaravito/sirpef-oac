<?php

namespace App\Http\Services\AtencionCiudadano;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Persona;
use App\Models\EventoPersona;
use App\Models\Evento;
use App\Models\Registro;
use App\Models\Recaudo;
use App\Models\TipoCaso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UpdateCasosService
{
    public static function updateCaso(Request $request, int $registroId): JsonResponse
    {
        return DB::transaction(function () use ($request, $registroId) {
            try {
                // 1. Buscar el registro y sus relaciones principales
                $registro = Registro::with('eventoPersona.persona', 'recaudos')->findOrFail($registroId);
                $persona = $registro->eventoPersona->persona;

                // 2. Preparar reglas de validación
                $validationRules = [
                    'nombre_completo' => 'required|string|max:255',
                    'sexo' => 'required|string|max:1',
                    'fecha_nacimiento' => 'required|date',
                    'cedula' => [
                        'sometimes',
                        'required',
                        'string',
                        Rule::unique('tbl_personas', 'cedula')->ignore($persona->id),
                    ],
                    'telefono' => 'required|string|max:20',
                    'descripcion' => 'required|string',
                    'tipo_caso_id' => 'nullable|integer',
                    'voto' => 'nullable|boolean',
                    'observacion' => 'nullable|string',
                    'referencia' => 'nullable|string',
                    'parroquia' => 'required',
                    'unidad_adscrita' => 'required',
                    'recaudos' => 'nullable|array',
                    'recaudos.*.nombre' => 'required|string|max:255', // Ahora es requerido
                    'recaudos.*.archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:204800',
                ];

                // Validación condicional para tablas relacionadas
                if ($request->has('tipo_caso_id') && Schema::hasTable('tbl_tipo_caso')) {
                    $validationRules['tipo_caso_id'] .= '|exists:tbl_tipo_caso,id';
                }
                if ($request->has('parroquia') && Schema::hasTable('tbl_parroquias')) {
                    $validationRules['parroquia'] = array_merge((array)$validationRules['parroquia'], ['exists:tbl_parroquias,id']);
                }
                if ($request->has('unidad_adscrita') && Schema::hasTable('tb_ministerio')) {
                    $validationRules['unidad_adscrita'] = array_merge((array)$validationRules['unidad_adscrita'], ['exists:tb_ministerio,id']);
                }

                $validatedData = $request->validate($validationRules);

                // 3. Actualizar la información de la Persona
                $persona->fill([
                    'nombre_completo' => $validatedData['nombre_completo'],
                    'sexo' => $validatedData['sexo'],
                    'fecha_nacimiento' => $validatedData['fecha_nacimiento'],
                    'cedula' => $validatedData['cedula'],
                    'telefono' => $validatedData['telefono'],
                    'parroquia_id' => $validatedData['parroquia'],
                    'ministerio_id' => $validatedData['unidad_adscrita'],
                ])->save();

                // 4. Actualizar la información del Registro
                $registro->fill([
                    'descripcion' => $validatedData['descripcion'],
                    'id_tipo_caso' => $validatedData['tipo_caso_id'] ?? null,
                    'voto' => $validatedData['voto'] ?? null,
                    'observacion' => $validatedData['observacion'] ?? null,
                    'referencia' => $validatedData['referencia'] ?? null,
                    'estatus_caso' => $validatedData['estatus_caso'] ?? null,
                ])->save();

                // 5. Procesar recaudos
                if (isset($validatedData['recaudos'])) {
                    // Obtener nombres de recaudos enviados
                    $nombresRecaudosEnviados = collect($validatedData['recaudos'])
                        ->pluck('nombre')
                        ->toArray();

                    // Eliminar recaudos existentes cuyos nombres no están en el array enviado
                    $recaudosAEliminar = $registro->recaudos()
                        ->whereNotIn('nombre', $nombresRecaudosEnviados)
                        ->get();

                    foreach ($recaudosAEliminar as $recaudo) {
                        try {
                            Storage::disk('public')->delete($recaudo->path);
                            $recaudo->delete();
                        } catch (\Exception $e) {
                            throw new \Exception("No se pudo eliminar el recaudo '{$recaudo->nombre}': " . $e->getMessage());
                        }
                    }

                    // Procesar cada recaudo enviado
                    foreach ($validatedData['recaudos'] as $recaudoData) {
                        // Verificar si ya existe un recaudo con este nombre
                        $recaudoExistente = $registro->recaudos()
                            ->where('nombre', $recaudoData['nombre'])
                            ->first();

                        // Si no existe y se envió un archivo, es un nuevo recaudo
                        if (!$recaudoExistente && isset($recaudoData['archivo'])) {
                            try {
                                $archivo = $recaudoData['archivo'];
                                $path = $archivo->store('recaudos', 'public');
                                
                                Recaudo::create([
                                    'nombre' => $recaudoData['nombre'],
                                    'path' => $path,
                                    'registro_id' => $registro->id,
                                    'mime_type' => $archivo->getMimeType(),
                                ]);
                            } catch (\Exception $e) {
                                throw new \Exception("Error al guardar el recaudo '{$recaudoData['nombre']}': " . $e->getMessage());
                            }
                        }
                        // Si existe pero se envió un nuevo archivo, reemplazarlo
                        elseif ($recaudoExistente && isset($recaudoData['archivo'])) {
                            try {
                                // Eliminar el archivo antiguo
                                Storage::disk('public')->delete($recaudoExistente->path);
                                
                                // Subir el nuevo archivo
                                $archivo = $recaudoData['archivo'];
                                $path = $archivo->store('recaudos', 'public');
                                
                                // Actualizar el recaudo existente
                                $recaudoExistente->update([
                                    'path' => $path,
                                    'mime_type' => $archivo->getMimeType(),
                                ]);
                            } catch (\Exception $e) {
                                throw new \Exception("Error al actualizar el recaudo '{$recaudoData['nombre']}': " . $e->getMessage());
                            }
                        }
                        // Si existe y no se envió archivo, se mantiene sin cambios
                    }
                } else {
                    // Si no se envía el array de recaudos, eliminamos todos los existentes
                    foreach ($registro->recaudos as $recaudo) {
                        try {
                            Storage::disk('public')->delete($recaudo->path);
                            $recaudo->delete();
                        } catch (\Exception $e) {
                            throw new \Exception("No se pudo eliminar el recaudo '{$recaudo->nombre}': " . $e->getMessage());
                        }
                    }
                }

                return response()->json([
                    'message' => 'Caso actualizado exitosamente',
                    'data' => $registro->fresh(['eventoPersona.persona', 'tipoCaso', 'recaudos'])
                ], 200);

            } catch (ValidationException $e) {
                Log::error('Error de validación al actualizar caso:', ['errors' => $e->errors()]);
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                Log::error("Registro (caso) no encontrado: {$registroId}");
                return response()->json([
                    'message' => 'Registro (caso) no encontrado',
                    'error' => $e->getMessage()
                ], 404);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::critical('Error CRÍTICO al actualizar caso:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return response()->json([
                    'message' => 'Error al actualizar el caso',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }
}