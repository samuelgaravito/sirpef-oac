<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstatusTramite extends Model
{
    use HasFactory;

    protected $table = 'tbl_estatus_tramite'; // Nombre explícito de la tabla

    protected $fillable = [
        'nombre_estatus',
        'descripcion',
    ];

    /**
     * Relación: Un estatus de trámite puede tener muchos seguimientos.
     */
    public function seguimientos(): HasMany
    {
        return $this->hasMany(Seguimiento::class, 'estatus_tramite_id');
    }
}