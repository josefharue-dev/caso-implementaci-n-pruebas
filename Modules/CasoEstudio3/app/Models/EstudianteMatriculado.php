<?php

namespace Modules\CasoEstudio3\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CasoEstudio3\Database\Factories\EstudianteMatriculadoFactory;

class EstudianteMatriculado extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    
protected $table = 'estudiantes_matriculados';

protected $fillable = [
    'grupo_id',
    'estudiante_id',
    'nota',
];

protected $casts = [
    'nota' => 'float',
];


    // protected static function newFactory(): EstudianteMatriculadoFactory
    // {
    //     // return EstudianteMatriculadoFactory::new();
    // }

    
public function grupo()
{
    return $this->belongsTo(Grupo::class, 'grupo_id');
}

public function estudiante()
{
    return $this->belongsTo(
        Estudiante::class,
        'estudiante_id',
        'carne'
    );
}

public $incrementing = true;

}
