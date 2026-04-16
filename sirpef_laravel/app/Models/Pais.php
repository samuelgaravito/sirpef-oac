<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'tb_paises';

    protected $fillable = [
        'pais',
        'coordenadas_x',
        'coordenadas_y',
    ];

    // Relación con el modelo Persona
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class, 'pais_id');
    }
}