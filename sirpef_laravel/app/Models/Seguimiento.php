<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Seguimiento extends Model
{
    use HasFactory;

    protected $table = 'tbl_seguimiento'; // Nombre explícito de la tabla

    protected $fillable = [
        'registro_id',
        'estatus_tramite_id',
        'observacion',
        'fecha_seguimiento',
    ];

    protected $casts = [
        'fecha_seguimiento' => 'datetime',
    ];

    /**
     * Relación: Un seguimiento pertenece a un Registro (caso).
     */
    public function registro(): BelongsTo
    {
        return $this->belongsTo(Registro::class, 'registro_id');
    }

    /**
     * Relación: Un seguimiento pertenece a un Estatus de Trámite.
     */
    public function estatusTramite(): BelongsTo
    {
        return $this->belongsTo(EstatusTramite::class, 'estatus_tramite_id');
    }
}