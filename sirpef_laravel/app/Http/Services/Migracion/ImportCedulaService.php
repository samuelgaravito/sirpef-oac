<?php

namespace App\Http\Services\Migracion;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Persona;
use App\Http\Services\Const\ObtenerPersonasService;

class ImportCedulaService{
    
    static public function importCedulas(Request $request)
{
    // Validar que se haya subido un archivo y que sea del tipo correcto
    $request->validate([
        'fileExcel' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('fileExcel');
    $cedulas = [];

    try {
        // Abrir el archivo CSV
        if (($handle = fopen($file, 'r')) !== false) {
            // Saltar la primera línea si es un encabezado
            fgetcsv($handle, 1000, ';');
            // Leer cada línea del archivo
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                if (!empty($data[0])) {
                    $cedulas[] = $data[0]; // Agregar cédula a la lista
                }
            }
            fclose($handle);
        }

        $respuesta = [];
        
        // Obtener todas las personas de una sola vez usando whereIn
        $personas = ObtenerPersonasService::obtenerPersonas()->whereIn('cedula', $cedulas)->get()->keyBy('cedula');

        foreach ($cedulas as $cedula) {
            $persona = $personas->get($cedula); // Buscar persona por cédula

            // Mensaje dependiendo de si la persona fue encontrada o no
            $mensaje = $persona ? 'Tiene permiso para registrar la participación' : 'Esta cédula no existe o no tiene permiso';

            // Construir la respuesta
            $respuesta[] = [
                'id' => $persona ? $persona->id : null,
                'nombre_completo' => $persona ? $persona->nombre_completo : 'Sin Datos',
                'unidad_Adscrita' => null,
                'cargo' => $persona ? $persona->cargo : 'Sin Datos',
                'telefono' => $persona ? $persona->telefono : 'Sin Datos',
                'cedula' => $cedula,
                'mensaje' => $mensaje,
            ];
        }

        // Retornar la respuesta en formato JSON
        return response()->json(['correcto' => $respuesta]);

    } catch (\Throwable $th) {
        // Manejo de errores y retorno de mensaje de error
        return response()->json(['error' => $th->getMessage()], 500);
    }
}
}