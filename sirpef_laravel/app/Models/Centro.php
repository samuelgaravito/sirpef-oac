<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centro extends Model
{
    protected $table = 'tbl_centros';
    protected $fillable = ['nombre', 'coordenadas_x', 'coordenadas_y'];

    public function personas()
    {
        return $this->hasMany(Persona::class, 'centro_id');
    }
}
