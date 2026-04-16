<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Parroquia extends Model
{
    protected $table = 'tbl_parroquias';
    protected $fillable = ['parroquias', 'coordenadas_x', 'coordenadas_y', 'municipio_id'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function personas()
    {
        return $this->hasMany(Persona::class, 'parroquia_id');
    }
}
