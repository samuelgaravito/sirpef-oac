<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ente extends Model
{
    protected $table = 'tbl_entes';
    protected $fillable = ['entes'];

    public function personas()
    {
        return $this->hasMany(Persona::class, 'entes_id');
    }


    public function unidadesAdscritas()

    {

        return $this->hasMany(UnidadAdscrita::class, 'ente_id');

    }
}
