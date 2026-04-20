<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memorandum extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_memorandums';

    protected $fillable = [
        'punto_cuenta_id',
        'codigo',
        'de',
        'para',
        'asunto',
        'fecha',
        'cuerpo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];
}
