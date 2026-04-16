<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;


class UnidadAdscrita extends Model
{
    use HasFactory;

    protected $table = 'tbl_unidad_adscritas';
    protected $fillable = ['nombre', 'codigo', 'RIF', 'tipo_id','entes_id'];

    public function personas()
    {
        return $this->hasMany(Persona::class, 'uni_ads_id');
    }

    public function tipo()
    {
        return $this->belongsTo(Tipo::class, 'tipo_id');
    }

    public function ente()
    {
        return $this->belongsTo(Ente::class, 'entes_id');
    }
}