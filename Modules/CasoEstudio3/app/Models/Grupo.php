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
        'curso_id',
    ];

    protected $casts = [
        'numeroCiclo' => 'integer',
        'ano' => 'integer',
        'numeroGrupo' => 'integer',
        'abierto' => 'boolean',
    ];

    
public function estudiantesMatriculados()
{
    return $this->hasMany(
        EstudianteMatriculado::class,
        'grupo_id'
    );
}

public function docentes()
{
    return $this->belongsToMany(
        Docente::class,
        'docente_grupo',
        'grupo_id',
        'docente_codEmpleado',
        'id',
        'codEmpleado'
    );
}

public function curso()
{
    return $this->belongsTo(
        Curso::class,
        'curso_id'
    );
}


    // protected static function newFactory(): GrupoFactory
    // {
    //     return GrupoFactory::new();
    // }
}



