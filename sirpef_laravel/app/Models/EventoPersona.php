<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventoPersona extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_evento_persona';

    protected $fillable = [
        'evento_id',
        'persona_id',
        'cantidad',
        'imagen1',
        'imagen2',
        'imagen3',
        'estatus',
        'persona_autorizada_id',
        'tipo'
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function registros()
    {
        return $this->hasMany(Registro::class, 'evento_persona_id');
    }

    public function personaAutorizada()
    {
        return $this->belongsTo(Persona::class, 'persona_autorizada_id');
    }
}