<?php

namespace Modules\CasoEstudio3\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CasoEstudio3\Database\Factories\GrupoFactory;

class Grupo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'numeroCiclo',
        'ano',
        'numeroGrupo',
        'abierto',
    ];

    protected $casts = [
        'numeroCiclo' => 'integer',
        'ano' => 'integer',
        'numeroGrupo' => 'integer',
        'abierto' => 'boolean',
    ];

    // protected static function newFactory(): GrupoFactory
    // {
    //     return GrupoFactory::new();
    // }
}



