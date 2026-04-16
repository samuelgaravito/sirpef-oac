<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'tbl_municipio';
    protected $fillable = ['municipio', 'coordenadas_x', 'coordenadas_y', 'estado_id'];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function parroquias()
    {
        return $this->hasMany(Parroquia::class, 'municipio_id');
    }
}
