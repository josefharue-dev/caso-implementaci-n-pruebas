<?php

namespace Modules\CasoEstudio3\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CasoEstudio3\Database\Factories\EstudianteFactory;

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
    'codEmpleadoConsejero'
];


public function consejero()
{
    return $this->belongsTo(
        Docente::class,
        'codEmpleadoConsejero',
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



    // protected static function newFactory(): EstudianteFactory
    // {
    //     // return EstudianteFactory::new();
    // }
}
