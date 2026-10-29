<?php

namespace Modules\CasoEstudio3\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CasoEstudio3\Database\Factories\CursoFactory;

class Curso extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sigla',
        'nombreCurso',
        'creditos',
    ];

    protected $casts = ['creditos' => 'integer',];

    
public function grupos()
{
    return $this->hasMany(
        Grupo::class,
        'curso_id'
    );
}


    // protected static function newFactory(): CursoFactory
    // {
    //     // return CursoFactory::new();
    // }
}
