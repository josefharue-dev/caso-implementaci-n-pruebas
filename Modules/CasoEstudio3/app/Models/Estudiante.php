<?php

namespace Modules\CasoEstudio3\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CasoEstudio3\Database\Factories\EstudianteFactory;

class Estudiante extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    
protected $primaryKey = 'carne';

public $incrementing = false;

protected $keyType = 'string';

protected $fillable = [
    'carne',
    'nombre',
    'apellidos',
    'consejero_id'
];


public function consejero()
{
    return $this->belongsTo(
        Docente::class,
        'consejero_id',
        'codEmpleado'
    );
}

public function matriculas()
{
    return $this->hasMany(
        EstudianteMatriculado::class,
        'estudiante_carne',
        'carne'
    );
}


public function grupos()
{
    return $this->belongsToMany(
        Grupo::class,
        'estudiantes_matriculados',
        'estudiante_id',
        'grupo_id',
        'carne',
        'id'
    )
    ->using(EstudianteMatriculado::class)
    ->withPivot('nota')
    ->withTimestamps();
}




    protected static function newFactory(): EstudianteFactory
    {
        return EstudianteFactory::new();
    }
}
