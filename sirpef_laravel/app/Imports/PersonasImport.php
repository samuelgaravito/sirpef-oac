<?php

namespace App\Imports;
use Carbon\Carbon;
use App\Models\Persona;
use App\Models\Parroquia;
use App\Models\UnidadAdscrita;
use App\Models\Ente;
use App\Models\Centro;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use DateTime;

class PersonasImport implements ToModel
{


/* public function model(array $row)
{
    try {
        // Filtrar la fila para eliminar valores vacíos o nulos
        $row = array_filter($row);

        // Verificar si la fila está vacía
        if (empty($row)) {
            return "No se puede registrar una fila vacía";
        }

        // Eliminar símbolos y puntos de la cédula
        $cedula = preg_replace('/[^0-9]/', '', $row[2]);

        // Crear o encontrar la unidad adscrita
        $unidadAdscrita = UnidadAdscrita::firstOrCreate([
            'codigo' => $row[0],
            'nombre' => $row[1],
        ]);

        // Verificar y asignar estado
        $estado = Estado::firstOrCreate(['estado'=>$row[6]]);

        // Verificar y asignar municipio (asegurándose que coincida con el estado)
        $municipio = Municipio::firstOrCreate([
            'municipio' =>$row[7] ,
            'estado_id' => $estado->id
        ]);

        // Verificar y asignar parroquia (asegurándose que coincida con el municipio)
        $parroquia = Parroquia::firstOrCreate([
            'parroquias' => $row[8] ,
            'municipio_id' => $municipio->id
        ]);

        // Crear o encontrar el centro
        $centros = Centro::firstOrCreate(['nombre'=> $row[9]]);

        // Verificar si el ente existe
        if (!isset($row[15]) || empty($row[15])) {
            return "No se puede registrar una persona sin ente";
        }

        $entes = Ente::firstOrCreate(['id' => $row[15]]);

        // Verificar si existe una persona con la misma cedula, unidad adscrita y ente
        $personaDB = Persona::where('cedula', $cedula)
            ->where('uni_ads_id', $unidadAdscrita->id)
            ->where('entes_id', $entes->id)
            ->first();

        if ($personaDB) {
            return "Se han registrado las personas";
        }

        // Crear la persona
        $guardar = new Persona([
            'uni_ads_id' => $unidadAdscrita->id,
            'cedula' => $cedula,
            'nombre_completo' => isset($row[3]) ? $row[3] : '',
            'fecha_nacimiento' => Carbon::createFromFormat('d/m/Y', $row[4])->format('Y-m-d'),               

            'sexo' => isset($row[5]) ? $row[5] : '',
            'estado_id' => $estado->id,
            'municipio_id' => $municipio->id,
            'parroquia_id' => $parroquia->id,
            'centro_id' => $centros->id,
            'direccion' => isset($row[10]) ? $row[10] : '',
            'ubicacion_administrativa'=> isset($row[11]) ? $row[11] : '',
            'cargo'=> isset($row[12]) ? $row[12] : '',
            'telefono' => isset($row[13]) ? preg_replace('/[^0-9]/', '', $row[13]) : null,
            'correo_electronico' => isset($row[14]) ? strtolower($row[14]) : '',
            'entes_id' => $entes->id,
        ]);

        // Verificar si existe un usuario con la misma cédula
        $user = User::where('cedula', $cedula)->first();

        // Si existe, asignar el ID del usuario a la persona
        if ($user) {
            $guardar->user_id = $user->id;
        }

        $guardar->save();
    } catch (\Exception $e) {
        // Manejar el error y devolver un mensaje adecuado
        return  $e->getMessage();
    }
} */



public function model(array $row)
{
    try {
        // Filtrar la fila para eliminar valores vacíos o nulos
        $row = array_filter($row);

        // Verificar si la fila está vacía
        if (empty($row)) {
            return "No se puede registrar una fila vacía";
        }

        // Eliminar símbolos y puntos de la cédula
        $cedula = preg_replace('/[^0-9]/', '', $row[2]);

        // Crear o encontrar la unidad adscrita
        $unidadAdscrita = UnidadAdscrita::firstOrCreate([
            'codigo' => $row[0],
            'nombre' => $row[1],
        ]);

        // Verificar y asignar estado
        $estado = Estado::firstOrCreate(['estado'=>$row[6]]);

        // Verificar y asignar municipio (asegurándose que coincida con el estado)
        $municipio = Municipio::firstOrCreate([
            'municipio' =>$row[7],
            'estado_id' => $estado->id
        ]);

        // Verificar y asignar parroquia (asegurándose que coincida con el municipio)
        $parroquia = Parroquia::firstOrCreate([
            'parroquias' => $row[8],
            'municipio_id' => $municipio->id
        ]);

        // Crear o encontrar el centro
        $centros = Centro::firstOrCreate(['nombre'=> $row[9]]);

        // Verificar si el ente existe
        if (!isset($row[15]) || empty($row[15])) {
            return "No se puede registrar una persona sin ente";
        }

        $entes = Ente::firstOrCreate(['id' => $row[15]]);

        // Verificar si existe una persona con la misma cedula, unidad adscrita y ente
        $personaDB = Persona::where('cedula', $cedula)
            ->where('uni_ads_id', $unidadAdscrita->id)
            ->where('entes_id', $entes->id)
            ->first();

        if ($personaDB) {
            // Actualizar la persona existente
            $personaDB->update([
                'nombre_completo' => isset($row[3])? $row[3] : '',
                'fecha_nacimiento' => Carbon::createFromFormat('d/m/Y', $row[4])->format('Y-m-d'),
                'sexo' => isset($row[5])? $row[5] : '',
                'estado_id' => $estado->id,
                'municipio_id' => $municipio->id,
                'parroquia_id' => $parroquia->id,
                'centro_id' => $centros->id,
                'direccion' => isset($row[10])? $row[10] : '',
                'ubicacion_administrativa'=> isset($row[11])? $row[11] : '',
                'cargo'=> isset($row[12])? $row[12] : '',
'telefono' => isset($row[13]) && $row[13]!= ''? (int) preg_replace('/[^0-9]/', '', $row[13]) : null,                'correo_electronico' => isset($row[14])? strtolower($row[14]) : '',
            ]);
            return "Persona actualizada con éxito";
        } else {
            // Crear la persona
            $guardar = new Persona([
                'uni_ads_id' => $unidadAdscrita->id,
                'cedula' => $cedula,
                'nombre_completo' => isset($row[3])? $row[3] : '',
                'fecha_nacimiento' => Carbon::createFromFormat('d/m/Y', $row[4])->format('Y-m-d'),               

                'sexo' => isset($row[5])? $row[5] : '',
                'estado_id' => $estado->id,
                'municipio_id' => $municipio->id,
                'parroquia_id' => $parroquia->id,
                'centro_id' => $centros->id,
                'direccion' => isset($row[10])? $row[10] : '',
                'ubicacion_administrativa'=> isset($row[11])? $row[11] : '',
                'cargo'=> isset($row[12])? $row[12] : '',
'telefono' => isset($row[13]) && $row[13]!= ''? (int) preg_replace('/[^0-9]/', '', $row[13]) : null,                'correo_electronico' => isset($row[14])? strtolower($row[14]) : '',
                'entes_id' => $entes->id,
            ]);

            // Verificar si existe un usuario con la misma cédula
            $user = User::where('cedula', $cedula)->first();

            // Si existe, asignar el ID del usuario a la persona
            if ($user) {
                $guardar->user_id = $user->id;
            }

            $guardar->save();
            return "Persona registrada con éxito";
        }
    } catch (\Exception $e) {
        // Manejar el error y devolver un mensaje adecuado
        return  $e->getMessage();
    }
}


}
