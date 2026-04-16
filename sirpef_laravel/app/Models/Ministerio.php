<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ministerio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_ministerio';

    protected $fillable = [
        'nombre','descripcion',
        'ministerio_padre_id',
    ];

    public function personas()
    {
        return $this->hasMany(Persona::class, 'ministerio_id');
    }

    public function ministerioPadre()
    {
        return $this->belongsTo(Ministerio::class, 'ministerio_padre_id');
    }

    public function ministeriosHijos()
    {
        return $this->hasMany(Ministerio::class, 'ministerio_padre_id');
    }

    
}