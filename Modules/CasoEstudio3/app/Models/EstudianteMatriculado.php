<?php

namespace Modules\CasoEstudio3\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CasoEstudio3\Database\Factories\EstudianteMatriculadoFactory;

class EstudianteMatriculado extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    
protected $table = 'estudiantes_matriculados';

protected $fillable = [
    'grupo_id',
    'estudiante_carne',
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
        'estudiante_carne',
        'carne'
    );
}

}
