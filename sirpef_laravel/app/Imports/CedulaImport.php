<?php

namespace App\Imports;

use App\Models\Persona;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CedulaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $results = [];

        foreach ($rows as $row) {
            if (isset($row['cedula'])) {
                // Elimina cualquier carácter no numérico
                $cedula = preg_replace('/\D/', '', $row['cedula']);

                // Asegúrate de que la longitud de la cédula no sea mayor que 8
                if (strlen($cedula) <= 8) {
                    $persona = Persona::where('cedula', $cedula)->first();

                    // Si encontramos la persona, agregamos a resultados
                    if ($persona) {
                        $results[] = [
                            'cedula' => $cedula,
                            'persona_id' => $persona->id
                        ];
                    }
                }
            }
        }

        // Convierte el array de resultados en JSON y guarda o devuelve
        return json_encode($results);
    }
}
