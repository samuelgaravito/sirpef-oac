<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaMasiva extends Model
{
    use HasFactory;

    protected $fillable = [ 'nombre_archivo','ruta_archivo',];


    public function personas()

    {

        return $this->hasMany(Persona::class, 'carga_masiva_id');

    }
}
