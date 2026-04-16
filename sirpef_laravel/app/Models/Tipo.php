<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    use HasFactory;
    protected $table = 'tbl_tipo';
    protected $fillable = [

        'tipo_oficina',

    ];


    public function unidadAdscritas()

    {

        return $this->hasMany(UnidadAdscrita::class,'tipo_id');

    }
}
