<?php

namespace Modules\CasoEstudio3\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CasoEstudio3\Database\Factories\GrupoFactory;

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
        'grupo_docente',
        'grupo_id',
        'docente_codEmpleado',
        'id',
        'codEmpleado'
    );
}



public function asignarDocente(Docente $docente): void
{
    $codEmpleado = $docente->getKey();

    // Evitar asignar dos veces al mismo docente
    if ($this->docentes()->whereKey($codEmpleado)->exists()) {
        return;
    }

    // Impedir más de dos docentes
    if ($this->docentes()->count() >= 2) {
        throw new \DomainException(
            'Un grupo no puede tener más de dos docentes.'
        );
    }

    // Asociar el docente al grupo
    $this->docentes()->attach($codEmpleado);
}



public function puedeAbrirse(): bool
{
    // Contar los estudiantes matriculados
    $cantidadEstudiantes = $this->estudiantesMatriculados()->count();

    // Contar los docentes asignados
    $cantidadDocentes = $this->docentes()->count();

    // Verificar las condiciones para abrir el grupo
    return $cantidadEstudiantes >= 15
        && $cantidadDocentes >= 1
        && $cantidadDocentes <= 2;
}

public function abrir(): void
{
    // Impedir la apertura si no se cumplen las condiciones
    if (!$this->puedeAbrirse()) {
        throw new \DomainException(
            'El grupo no cumple las condiciones para abrirse.'
        );
    }

    // Cambiar el estado del grupo a abierto
    $this->update([
        'abierto' => true,
    ]);
}

public function curso()
{
    return $this->belongsTo(
        Curso::class,
        'curso_id'
    );
}


public function estudiantes()
{
    return $this->belongsToMany(
        Estudiante::class,
        'estudiantes_matriculados',
        'grupo_id',
        'estudiante_id',
        'id',
        'carne'
    )
    ->using(EstudianteMatriculado::class)
    ->withPivot('nota')
    ->withTimestamps();
}


public function registrarNota(Estudiante $estudiante, float $nota): void
{
    // Verificar que la nota esté entre 0 y 10
    if ($nota < 0 || $nota > 10) {
        throw new \InvalidArgumentException(
            'La nota debe estar entre 0 y 10.'
        );
    }

    // Buscar la matrícula del estudiante en este grupo
    $matricula = $this->estudiantesMatriculados()
        ->where('estudiante_id', $estudiante->carne)
        ->firstOrFail();

    // Registrar la nota
    $matricula->update([
        'nota' => $nota,
    ]);
}




    protected static function newFactory(): GrupoFactory
    {
        return GrupoFactory::new();
    }
}



