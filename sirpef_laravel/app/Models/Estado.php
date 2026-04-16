<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'tbl_estados';
    protected $fillable = ['estado', 'coordenadas_x', 'coordenadas_y'];


    

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'estado_id');
    }
}
