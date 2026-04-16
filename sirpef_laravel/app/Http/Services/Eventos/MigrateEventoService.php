<?php

namespace App\Http\Services\Eventos;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Evento;
use App\Models\Persona;
use App\Models\EventoPersona;

class MigrateEventoService
{
    static public function cargaMasivaEvento(Request $request, $evento_id)
    {
        // Validar que se haya subido un archivo CSV y que se reciba el campo tipo
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
            'tipo' => 'required|string', // Validar que el tipo sea obligatorio y sea una cadena
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'No se ha subido un archivo CSV válido o falta el campo tipo'], 422);
        }

        // Leer el archivo CSV
        $file = $request->file('file');
        $rows = array_map(function($row) {
            return str_getcsv($row, ';');
        }, file($file->getPathname()));

        // Validar que el evento exista
        $evento = Evento::find($evento_id);
        if (!$evento) {
            return response()->json(['error' => 'El evento no existe'], 404);
        }

        // Obtener los ministerios asociados al evento (asumiendo que ministerio_id es un array o JSON)
        $ministerios = json_decode($evento->ministerio_id, true);
        if (!is_array($ministerios)) {
            $ministerios = [$ministerios];
        }

        // Comenzar la transacción
        DB::beginTransaction();
        try {
            // Procesar cada fila del archivo CSV
            $errores = [];
            $procesados = 0;

            // Obtener el tipo del request
            $tipo = $request->input('tipo');

            foreach ($rows as $index => $row) {
                // Limpiar y validar que la cédula del empleado sea obligatoria y solo números
                $cedulaEmpleado = trim($row[0]);

                if (empty($cedulaEmpleado) || !preg_match('/^\d+$/', $cedulaEmpleado)) {
                    $errores[] = "Fila " . ($index + 1) . ": La cédula del empleado '$cedulaEmpleado' es inválida.";
                    continue;
                }

                // Buscar la persona del empleado por cédula
                $empleado = Persona::where('cedula', $cedulaEmpleado)->first();

                // Validar que la persona del empleado exista y pertenezca a uno de los ministerios asociados al evento
                if (!$empleado || !in_array($empleado->ministerio_id, $ministerios)) {
                    $errores[] = "Fila " . ($index + 1) . ": La persona del empleado con cédula '$cedulaEmpleado' no existe o no pertenece a uno de los ministerios asociados al evento.";
                    continue;
                }

                // Crear la relación entre el evento y la persona del empleado
                $tbEventoPersona = EventoPersona::firstOrCreate(
                    [
                        'evento_id' => $evento_id,
                        'persona_id' => $empleado->id,
                    ],
                    [
                        'cantidad' => $row[1] ?? 1, // Provide a value for cantidad here
                        'tipo' => $tipo, // Agregar el tipo recibido del request
                    ]
                );

                // Verificar si hay un autorizado
                if (!empty($row[2])) {
                    // Limpiar y validar que la cédula del autorizado sea solo números
                    $cedulaAutorizado = trim($row[2]);

                    if (!preg_match('/^\d+$/', $cedulaAutorizado)) {
                        $errores[] = "Fila " . ($index + 1) . ": La cédula del autorizado '$cedulaAutorizado' debe ser solo números.";
                        continue;
                    }

                    // Buscar o crear la persona del autorizado por cédula
                    $autorizado = Persona::where('cedula', $cedulaAutorizado)->first();

                    if (!$autorizado) {
                        // Si no existe, crear un nuevo registro con los datos del autorizado
                        $autorizado = new Persona();
                        $autorizado->cedula = $cedulaAutorizado;
                        $autorizado->nombre_completo = $row[3] ?? '';
                        $autorizado->autorizado_id = $empleado->id; // Asignar el ID del empleado como autorizado_id del autorizado
                        $autorizado->save();
                    } else {
                        // Si el autorizado ya existe, simplemente actualizar su autorizado_id si es necesario
                        if ($autorizado->autorizado_id !== $empleado->id) {
                            $autorizado->autorizado_id = $empleado->id;
                            $autorizado->save();
                        }
                    }

                    $tbEventoPersonaAutorizado = EventoPersona::firstOrCreate(
                        [
                            'evento_id' => $evento_id,
                            'persona_id' => $autorizado->id,
                        ],
                        [
                            'tipo' => $tipo, // Agregar el tipo recibido del request
                        ]
                    );
                }

                // Incrementar el contador de filas procesadas correctamente
                $procesados++;
            }

            $response = [
                'msg' => '',
                'errores' => '',
            ];

            // Verificar si hubo errores en la importación
            if (!empty($errores)) {
                // DB::rollback();
                $response = [
                    'msg' => "Se procesaron $procesados filas correctamente, pero ocurrieron algunos errores.",
                    'errores' => $errores
                ];
            } else {
                $response = [
                    'msg' => "Datos importados correctamente. Se procesaron $procesados filas.",
                ];
            }

            DB::commit();
            return response()->json($response, 200);

        } catch (\Exception $e) {
            // DB::rollback();
            return response()->json(['error' => 'Error al procesar la carga masiva: ' . $e->getMessage()], 500);
        }
    }
}