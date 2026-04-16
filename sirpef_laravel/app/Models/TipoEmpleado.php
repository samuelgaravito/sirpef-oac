<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEmpleado extends Model
{
    use HasFactory;

    protected $table = 'tb_tipo_empleados';

    protected $fillable = [
        'tipo',
        'codigo',
    ];

    // Relación con el modelo Persona
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class, 'tipo_empleado_id');
    }
}