<?php

namespace Modules\CasoEstudio3\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CasoEstudio3\Database\Factories\DocenteFactory;

class Docente extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    
protected $primaryKey = 'codEmpleado';

public $incrementing = false;

protected $keyType = 'int';

protected $fillable = [
    'codEmpleado',
    'nombreEmpleado',
    'apellidosEmpleado',
];

protected $casts = [
    'codEmpleado' => 'integer',
];


public function grupos()
{
    return $this->belongsToMany(
        Grupo::class,
        'docente_grupo',
        'docente_codEmpleado',
        'grupo_id',
        'codEmpleado',
        'id'
    );
}

public function estudiantes()
{
    return $this->hasMany(
        Estudiante::class,
        'codEmpleadoConsejero',
        'codEmpleado'
    );
}



    // protected static function newFactory(): DocenteFactory
    // {
    //     // return DocenteFactory::new();
    // }
}
