<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonaAutorizada extends Model
{
    use HasFactory;

    protected $table = 'tb_personas_autorizadas';

    protected $fillable = [
        'nombre_completo',
        'cedula',
        'telefono',
        'estatus',
    ];

    // Relación con EventoPersona
    public function eventoPersonas()
    {
        return $this->hasMany(EventoPersona::class, 'persona_autorizada_id');
    }
}