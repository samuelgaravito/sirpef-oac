<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importar BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Importar HasMany

class TipoCaso extends Model
{
    use HasFactory;

    protected $table = 'tbl_tipo_caso';

    protected $fillable = [
        'tipo',
        'color', // Asegúrate de que 'color' esté aquí si lo añadiste antes
        'tipo_caso_padre_id' // ¡Importante: Añadir este campo para asignación masiva!
    ];

    /**
     * Relación: Un tipo de caso puede tener muchos registros.
     */
    public function registros(): HasMany
    {
        return $this->hasMany(Registro::class, 'id_tipo_caso');
    }

    /**
     * Relación: Un tipo de caso puede tener un tipo de caso padre.
     */
    public function padre(): BelongsTo
    {
        return $this->belongsTo(TipoCaso::class, 'tipo_caso_padre_id');
    }

    /**
     * Relación: Un tipo de caso puede tener muchos tipos de caso hijos.
     */
    public function hijos(): HasMany
    {
        return $this->hasMany(TipoCaso::class, 'tipo_caso_padre_id');
    }

    /**
     * Determina si este tipo de caso es un tipo de caso raíz (no tiene padre).
     */
    public function isRoot(): bool
    {
        return is_null($this->tipo_caso_padre_id);
    }
}