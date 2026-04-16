<?php

namespace App\Http\Services\Migracion;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\User;
use App\Models\Pais;
use App\Models\TipoEmpleado;
use Carbon\Carbon;
use Exception;

class ImportFinalService
{

    static public function importFinalJubilados (Request $request) {
        
       $validator = Validator::make($request->all(), [
           'fileExcel' => 'required|file|mimes:csv,txt|max:10240',
           'unid_id' => 'required|string'
       ]);

       if ($validator->fails()) {
           return response()->json(['error' => 'Archivo CSV inválido o no proporcionado.'], 422);
       }

       $file = $request->file('fileExcel');
       $rows = array_map(function($row) {
           $cleaned_row = preg_replace('/\s*;\s*/', ';', $row);
           $cleaned_row = str_replace('"', '', $cleaned_row);
           return str_getcsv($cleaned_row, ';');
       }, file($file->getPathname()));

       if (count($rows) <= 1) {
           return response()->json(['error' => 'El archivo CSV está vacío.'], 400);
       }

       $personas = [];

       foreach (array_slice($rows, 1) as $index => $row) {
           $cedula = !empty($row[0]) ? $row[0] : ''; 
           $nombre_completo = isset($rows[1])  ?  $row[1] : ''; 
           $pais = $row[2] ? $row[2] : '';
           $telefono = !empty($row[7]) ? $row[7] : null;
           $telefono_2 = !empty($row[8]) ? $row[8] : null;
           $causa_pension = !empty($row[4]) ? $row[4] : null;
           $tipo = isset($row[3]) ? $row[3] : '';
           $estatus = isset($row[4]) ? $row[4] : '';

           $data = [
               'cedula' => str_replace('.', '', $cedula),
               'nombre_completo' => $nombre_completo,
               'pais' => $pais,/* $pais_db ? $pais_db->id : null */
               'tipo_empleado_id' => $tipo, /*$tipo_db ? $tipo_db->id : null*/
               'causante_pension' => $row[4],
               'estatus' => $estatus == 'ACTIVO' ? 'activo' : 'egresado',
               'sexo' => $row[5],
               'direccion' => $row[6],
               'cargo' => null,
               'telefono' => $row[7],
               'telefono_2' => $row[8],
               'fecha_nacimiento' => $row[9] ? $row[9] : null,
               'correo_electronico' => null,
               'parroquia_id' => $row[10],
               'ministerio_id' => $row[11],
               'centro_id' => null,
           ];

           array_push($personas, $data);

           $personaExistente = Persona::where('cedula', str_replace('.', '', $cedula))->first();

           if(!$personaExistente) {
               $persona = Persona::create($data);
           } else {
            $personaExistente->cedula = str_replace('.', '', $cedula);
           $personaExistente->nombre_completo = $nombre_completo;
           $personaExistente->pais_id = $pais; // Asignar el ID del país
           $personaExistente->tipo_empleado_id = $tipo; // Asignar el ID del tipo de empleado
           $personaExistente->estatus = $estatus == 'ACTIVO' ? 'activo' : 'egresado';
           $personaExistente->sexo = $row[5];
           $personaExistente->direccion = $row[6];
           $personaExistente->cargo = null;
           $personaExistente->fecha_nacimiento = $row[9];
           $personaExistente->correo_electronico = null;
           $personaExistente->parroquia_id = $row[10];
           $personaExistente->ministerio_id = $row[11];
           $personaExistente->centro_id = null;
           $personaExistente->telefono = $telefono;
            $personaExistente->telefono_2 = $telefono_2;
            $personaExistente->causa_pension = $causa_pension;
            $personaExistente->save();
           }
   };

   return response()->json($personas);

   }

 /*ACTUALIZACION DE PARROQUIAS */
 static public function importFinalSOPA  (Request $request) {

    $validator = Validator::make($request->all(), [
        'fileExcel' => 'required|file|mimes:csv,txt|max:10240',
        'unid_id' => 'required|string'
    ]);
    if ($validator->fails()) {
        return response()->json(['error' => 'Archivo CSV inválido o no proporcionado.'], 422);
    }
    $file = $request->file('fileExcel');
    $rows = array_map(function($row) {
        $cleaned_row = preg_replace('/\s*;\s*/', ';', $row);
        $cleaned_row = str_replace('"', '', $cleaned_row);
        return str_getcsv($cleaned_row, ';');
    }, file($file->getPathname()));
    if (count($rows) <= 1) {
        return response()->json(['error' => 'El archivo CSV está vacío.'], 400);
    }
    $personas = [];
    foreach (array_slice($rows, 1) as $index => $row) {
        $cedula = !empty($row[0]) ? $row[0] : ''; 
        $estado = isset($rows[1])  ?  $row[1] : '';
        $parroquia_id = $row[2] ? $row[2] : '';

        $data = [
            'cedula' => str_replace('.', '', $cedula),
            'estado' => $estado,
            'parroquia_id' => $parroquia_id,
        ];
        array_push($personas, $data);
        $personaExistente = Persona::where('cedula', str_replace('.', '', $cedula))->first();

        if($personaExistente) {
            $personaExistente->parroquia_id = $parroquia_id ? $parroquia_id : null;
            $personaExistente->save();
        }
};
return response()->json($personas);
}


    static public function importFinal(Request $request)
    {
        // Validar que se haya subido un archivo CSV
        $validator = Validator::make($request->all(), [
            'fileExcel' => 'required|file|mimes:csv,txt|max:10240',
            'unid_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Archivo CSV inválido o no proporcionado.'], 422);
        }

        // Leer el archivo CSV usando el delimitador ';', eliminando espacios en blanco y dobles comillas
        $file = $request->file('fileExcel');
        $unid_id = $request->get('unid_id');

        $rows = array_map(function($row) {
            $cleaned_row = preg_replace('/\s*;\s*/', ';', $row);
            $cleaned_row = str_replace('"', '', $cleaned_row);
            return str_getcsv($cleaned_row, ';');
        }, file($file->getPathname()));

        // Validar que el archivo tenga datos
        if (count($rows) <= 1) {
            return response()->json(['error' => 'El archivo CSV está vacío.'], 400);
        }

        // Procesar cada fila del archivo CSV
        $errores = [];
        $procesadas = 0;

        try {
            foreach (array_slice($rows, 1) as $index => $row) {
                // Limpiar y verificar cédula y nombre completo
                $cedula = isset($row[0]) ? trim(preg_replace('/[^0-9]/', '', $row[0])) : ''; // Columna 0: cédula
                $nombre_completo = isset($row[1]) ? trim($row[1]) : ''; // Columna 1: nombre completo

                // Limpiar la dirección eliminando comillas dobles y espacios adicionales
                $direccion = isset($row[2]) ? trim(str_replace('"', '', $row[2])) : null; // Columna 2: dirección

                // Verificar que la fila contenga al menos la cédula y el nombre completo
                if (empty($cedula) || empty($nombre_completo)) {
                    $fila_completa = implode(';', $row);
                    $errores[] = "Fila " . ($index + 2) . ": Cédula y nombre completo son obligatorios. Valores encontrados: cédula: '{$cedula}', nombre completo: '{$nombre_completo}', fila completa: [{$fila_completa}]";
                    continue;
                }

                // Limpiar y asignar valores predeterminados si alguna columna está vacía
                $cargo = !empty($row[3]) ? $row[3] : null; // Columna 3: cargo
                $telefono = isset($row[4]) && !empty($row[4]) ? (int) preg_replace('/[^0-9]/', '', $row[4]) : null; // Columna 4: teléfono
                $sexo = $row[5] ?? ''; // Columna 5: sexo
                $fecha_nacimiento = isset($row[6]) && !empty($row[6]) ? trim($row[6]) : null;

                if ($fecha_nacimiento) {
                    try {
                        $fechaCarbon = Carbon::createFromFormat('Y-m-d', $fecha_nacimiento);
                        $data['fecha_nacimiento'] = $fechaCarbon->format('Y-m-d');
                    } catch (\Exception $e) {
                        $errores[] = "Fila " . ($index + 2) . ": La fecha de nacimiento no es válida. Valores encontrados: '{$fecha_nacimiento}'";
                        continue; // Saltar esta fila
                    }
                }

                $correo_electronico =  'no'; // Columna 7: correo electrónico

                if ($correo_electronico) {
                    $correo_electronico = filter_var($correo_electronico, FILTER_SANITIZE_EMAIL);
                    if (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
                        $correo_electronico = null; // Si no es válido, establecer como null
                    }
                }

                $estatus = isset($row[8]) && !empty($row[8]) ? 'activo' : 'activo'; // Columna 8: estatus, por defecto 'activo'
                $parroquia_id = !empty($row[9]) ? $row[9] : null; // Columna 9: parroquia_id
                $centro_id = !empty($row[10]) ? $row[10] : null; // Columna 10: centro_id
                $user_id = null; // Columna 11: user_id
                $ministerio_id = $unid_id; // Columna 12: ministerio_id

                // Verificar si la cédula ya existe en la tabla de personas
                $personaExistente = Persona::where('cedula', $cedula)->first();
                if ($personaExistente) {
                    // Si existe, proceder a verificar o crear el usuario
                    if (!empty($user_id)) {
                        $user_id = $personaExistente->user_id;
                        if (empty($user_id)) {
                            // Si no hay usuario, crear uno nuevo
                            $usuarioExistente = User::where('cedula', $cedula)->first();
                            if ($usuarioExistente) {
                                $user_id = $usuarioExistente->id; // Asignar el ID del usuario existente
                                $errores[] = "Fila " . ($index + 2) . ": El usuario ya existe con cédula $cedula.";
                                continue; // Saltar la creación de la persona y continuar con el ciclo
                            } else {
                                // Crear un nuevo usuario
                                if ($correo_electronico) {
                                    $usuarioExistente = User::where('email', $correo_electronico)->first();
                                    if ($usuarioExistente) {
                                        $user_id = $usuarioExistente->id; // Asignar el ID del usuario existente
                                        $errores[] = "Fila " . ($index + 2) . ": El correo electrónico '$correo_electronico' ya existe. Se usará el usuario existente.";
                                        continue; // Saltar la creación del nuevo usuario
                                    }
                                    try {
                                        $nuevoUsuario = User::create([
                                            'name' => $nombre_completo,
                                            'email' => $correo_electronico,
                                            'cedula' => $cedula,
                                            'password' => bcrypt('1234'), // Cambia esto según tus necesidades
                                        ]);
                                        $user_id = $nuevoUsuario->id; // Asignar el ID del nuevo usuario
                                    } catch (\Illuminate\Database\QueryException $e) {
                                        if ($e->getCode() == 23505) {
                                            // Si el error es debido a una violación de la restricción única de email, saltar esta fila
                                            $errores[] = "Fila " . ($index + 2) . ": El correo electrónico '$correo_electronico' ya existe.";
                                            continue; // Saltar esta fila
                                        } else {
                                            $errores[] = "Fila " . ($index + 2) . ": Error al crear el usuario: " . $e->getMessage();
                                            continue; // Saltar esta fila
                                        }
                                    }
                                } else {
                                    $errores[] = "Fila " . ($index + 2) . ": No se puede crear un usuario sin correo electrónico.";
                                    continue; // Saltar esta fila
                                }
                            }
                        }
                    }
                    // Actualizar el campo user_id en la tabla de personas
                    $personaExistente->update(['user_id' => $user_id]);
                    continue; // Saltar la creación de la persona y continuar con el ciclo
                } else {
                    // Si no existe, crear una nueva persona
                    $data = [
                        'cedula' => $cedula,
                        'nombre_completo' => $nombre_completo,
                        'sexo' => $sexo,
                        'estatus' => $estatus,
                        'user_id' => $user_id, // Asignar el ID del usuario
                        'tipo_empleado_id' => 7, // Asignar el ID del usuario
                    ];

                    if ($direccion) {
                        $data['direccion'] = $direccion;
                    }
                    if ($cargo) {
                        $data['cargo'] = $cargo;
                    }
                    if ($telefono) {
                        $data['telefono'] = $telefono;
                    }
                    if ($fecha_nacimiento) {
                        $data['fecha_nacimiento'] = $fecha_nacimiento;
                    }
                    if ($correo_electronico) {
                        $data['correo_electronico'] = $correo_electronico;
                    }
                    if ($parroquia_id) {
                        $data['parroquia_id'] = $parroquia_id;
                    }
                    if ($centro_id) {
                        $data['centro_id'] = $centro_id;
                    }
                    if ($ministerio_id) {
                        $data['ministerio_id'] = $ministerio_id;
                     }
                
                    // Crear la nueva persona
                    $persona = Persona::create($data);
                    // Si hay errores durante la inserción
                    if (!$persona) {
                        $fila_completa = implode(', ', $row);
                        $errores[] = "Fila " . ($index + 2) . ": Error al crear la persona con cédula $cedula. Fila completa: [{$fila_completa}]";
                    } else {
                        $procesadas++;
                    }
                }
            }

            return response()->json([
                'msg' => "Se procesaron $procesadas filas correctamente.",
                'errores' => $errores,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error en la carga masiva: ' . $e->getMessage()], 500);
        }
    }
}