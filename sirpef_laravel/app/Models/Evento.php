<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_evento';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estatus',
        'ministerio_id',
        'estatus_personas',
        'cortesia',
        'cortesia_entregada'
    ];

    protected $casts = [
        'estatus_personas' => 'json',
    ];

   

    public function personas()
    {
        return $this->belongsToMany(Persona::class, 'tb_evento_persona', 'evento_id', 'persona_id');
    }

    public function auditorias()
    {
        return $this->hasMany(Auditoria::class, 'evento_id');
    }

    public function eventoPersona()
{
    return $this->hasMany(EventoPersona::class, 'evento_id');
}


}