<?php

use Modules\CasoEstudio3\Models\Curso;
use Modules\CasoEstudio3\Models\Grupo;
use Modules\CasoEstudio3\Models\Docente;
use Modules\CasoEstudio3\Models\Estudiante;
use Modules\CasoEstudio3\Models\EstudianteMatriculado;
use Illuminate\Database\QueryException;

mutates(Grupo::class);

test('CP-07 un grupo sin docente no puede abrirse', function () {

    // Crear el curso y el grupo cerrado
    $curso = Curso::factory()->create();

    $grupo = Grupo::factory()->create([
        'curso_id' => $curso->id,
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    // Registrar al profesor consejero
    $consejero = Docente::factory()->create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    // Generar 20 estudiantes mediante factory
    $estudiantes = Estudiante::factory()
        ->count(20)
        ->create([
            'consejero_id' => $consejero->codEmpleado,
        ]);

    // Matricular a los estudiantes en el grupo
    $grupo->estudiantes()->attach(
        $estudiantes->pluck('carne')->all()
    );

    // Verificar las precondiciones
    expect($grupo->estudiantes()->count())->toBe(20);
    expect($grupo->docentes()->count())->toBe(0);

    // Comprobar que no puede abrirse
    expect($grupo->puedeAbrirse())->toBeFalse();

    // Comprobar que abrir() lanza la excepción
    expect(fn () => $grupo->abrir())
        ->toThrow(DomainException::class);

    // Verificar que continúa cerrado
    expect($grupo->fresh()->abierto)->toBeFalse();

    // Confirmar el estado en la base de datos
    $this->assertDatabaseHas('grupos', [
        'id' => $grupo->id,
        'abierto' => false,
    ]);
});


test('CP-08 estudiante asociado a su profesor consejero', function () {

    // Registrar el docente 1001
    $docente = Docente::factory()->create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    // Registrar el estudiante C12345
    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
        'nombre' => 'María',
        'apellidos' => 'Solís Quesada',
        'consejero_id' => 1001,
    ]);

    // Verificar el consejero del estudiante
    expect($estudiante->consejero->codEmpleado)->toBe(1001);

    // Verificar la relación inversa
    expect($docente->estudiantes)->toHaveCount(1);

    expect($docente->estudiantes->first()->carne)
        ->toBe('C12345');

    // Verificar la asociación en la base de datos
    $this->assertDatabaseHas('estudiantes', [
        'carne' => 'C12345',
        'consejero_id' => 1001,
    ]);
});


test('CP-09 cambiar consejero reemplaza al anterior', function () {

    // Registrar los docentes
    $docente1 = Docente::factory()->create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    $docente2 = Docente::factory()->create([
        'codEmpleado' => 1002,
        'nombreEmpleado' => 'Luis',
        'apellidosEmpleado' => 'Rojas Solano',
    ]);

    // Crear estudiante con consejero 1001
    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
        'nombre' => 'María',
        'apellidos' => 'Solís Quesada',
        'consejero_id' => 1001,
    ]);

    // Cambiar al consejero 1002
    $estudiante->update([
        'consejero_id' => 1002,
    ]);

    // Recuperar los datos actualizados
    $estudiante = $estudiante->fresh();
    $docente1 = $docente1->fresh();
    $docente2 = $docente2->fresh();

    // Verificar el nuevo consejero
    expect($estudiante->consejero->codEmpleado)->toBe(1002);

    // El docente anterior ya no tiene estudiantes
    expect($docente1->estudiantes)->toHaveCount(0);

    // El nuevo docente tiene exactamente uno
    expect($docente2->estudiantes)->toHaveCount(1);

    expect($docente2->estudiantes->first()->carne)
        ->toBe('C12345');

    // Confirmar que solo existe el nuevo consejero
    $this->assertDatabaseHas('estudiantes', [
        'carne' => 'C12345',
        'consejero_id' => 1002,
    ]);

    $this->assertDatabaseCount('estudiantes', 1);
});


test('CP-10 matricula con nota inicial null', function () {

    // Registrar el curso y el grupo
    $curso = Curso::factory()->create();

    $grupo = Grupo::factory()->create([
        'curso_id' => $curso->id,
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    // Registrar el docente consejero
    $docente = Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    // Crear el estudiante C12345
    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
        'nombre' => 'María',
        'apellidos' => 'Solís Quesada',
        'consejero_id' => 1001,
    ]);

    // Matricular al estudiante
    $grupo->estudiantes()->attach($estudiante->carne);

    // Recuperar el pivote de la matrícula
    $pivote = $grupo->estudiantes->first()->pivot;

    // Verificar el tipo de pivote
    expect($pivote)->toBeInstanceOf(
        EstudianteMatriculado::class
    );

    // La nota inicial debe ser null
    expect($pivote->nota)->toBeNull();

    // Verificar los datos en la base de datos
    $this->assertDatabaseHas('estudiantes_matriculados', [
        'grupo_id' => $grupo->id,
        'estudiante_id' => 'C12345',
        'nota' => null,
    ]);

    // Debe existir una sola matrícula
    $this->assertDatabaseCount('estudiantes_matriculados', 1);
});


test('CP-11 registrar nota al finalizar el curso', function () {

    // Registrar el curso y el grupo
    $curso = Curso::factory()->create();

    $grupo = Grupo::factory()->create([
        'curso_id' => $curso->id,
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    // Registrar el docente consejero
    Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    // Crear el estudiante
    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
        'nombre' => 'María',
        'apellidos' => 'Solís Quesada',
        'consejero_id' => 1001,
    ]);

    // Matricular al estudiante
    $grupo->estudiantes()->attach($estudiante->carne);

    // Recuperar la matrícula inicial
    $matriculaInicial = EstudianteMatriculado::where([
        'grupo_id' => $grupo->id,
        'estudiante_id' => $estudiante->carne,
    ])->firstOrFail();

    // Verificar que inicialmente no tiene nota
    expect($matriculaInicial->nota)->toBeNull();

    // Registrar la calificación
    $grupo->registrarNota($estudiante, 8.5);

    // Recuperar la matrícula actualizada
    $matricula = EstudianteMatriculado::where([
        'grupo_id' => $grupo->id,
        'estudiante_id' => $estudiante->carne,
    ])->firstOrFail();

    // Verificar que la nota sea 8.5 y de tipo float
    expect($matricula->nota)->toBe(8.5);

    // Verificar que la matrícula sea la misma
    expect($matricula->id)->toBe($matriculaInicial->id);

    expect($matricula->grupo_id)->toBe(
        $matriculaInicial->grupo_id
    );

    expect($matricula->estudiante_id)->toBe(
        $matriculaInicial->estudiante_id
    );

    // Confirmar que existe una sola matrícula
    $this->assertDatabaseCount('estudiantes_matriculados', 1);
});


test('CP-12 rechazar notas fuera de rango', function (float $nota) {

    // Registrar el curso y el grupo
    $curso = Curso::factory()->create();

    $grupo = Grupo::factory()->create([
        'curso_id' => $curso->id,
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    // Registrar el docente consejero
    Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    // Crear el estudiante
    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
        'nombre' => 'María',
        'apellidos' => 'Solís Quesada',
        'consejero_id' => 1001,
    ]);

    // Matricular al estudiante sin nota
    $grupo->estudiantes()->attach($estudiante->carne);

    // Intentar registrar una nota inválida
    expect(fn () => $grupo->registrarNota($estudiante, $nota))
        ->toThrow(InvalidArgumentException::class);

    // Recuperar la matrícula
    $matricula = EstudianteMatriculado::where([
        'grupo_id' => $grupo->id,
        'estudiante_id' => $estudiante->carne,
    ])->firstOrFail();

    // Verificar que la nota sigue siendo null
    expect($matricula->nota)->toBeNull();

    // Confirmar que la matrícula no fue eliminada
    $this->assertDatabaseCount('estudiantes_matriculados', 1);

})->with([-1, 10.5]);


test('CP-13 impedir matricula duplicada', function () {

    // Registrar el curso y el grupo
    $curso = Curso::factory()->create();

    $grupo = Grupo::factory()->create([
        'curso_id' => $curso->id,
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    // Registrar el docente consejero
    Docente::factory()->create([
        'codEmpleado' => 1001,
    ]);

    // Crear el estudiante C12345
    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
        'nombre' => 'María',
        'apellidos' => 'Solís Quesada',
        'consejero_id' => 1001,
    ]);

    // Primera matrícula
    $grupo->estudiantes()->attach($estudiante->carne);

    // Verificar que existe una matrícula
    $this->assertDatabaseCount('estudiantes_matriculados', 1);

    // Intentar matricular nuevamente al mismo estudiante
    expect(fn () => $grupo->estudiantes()->attach($estudiante->carne))
        ->toThrow(QueryException::class);

    // Verificar que solamente existe una matrícula
    $this->assertDatabaseCount('estudiantes_matriculados', 1);

    // Confirmar que se conserva la matrícula original
    $this->assertDatabaseHas('estudiantes_matriculados', [
        'grupo_id' => $grupo->id,
        'estudiante_id' => 'C12345',
    ]);
});

test('C3 acepta y guarda las notas limite 0 y 10', function (float $nota) {
    $grupo = Grupo::factory()->create([
        'numeroGrupo' => 1,
        'abierto' => false,
    ]);

    $docente = Docente::factory()->create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    $estudiante = Estudiante::factory()->create([
        'carne' => 'C12345',
        'nombre' => 'María',
        'apellidos' => 'Solís Quesada',
        'consejero_id' => $docente->getKey(),
    ]);

    $grupo->estudiantes()->attach($estudiante->getKey());

    $grupo->registrarNota($estudiante, $nota);

    $matricula = EstudianteMatriculado::where([
        'grupo_id' => $grupo->getKey(),
        'estudiante_id' => $estudiante->getKey(),
    ])->firstOrFail();

    expect($matricula->nota)->toBe($nota);
})->with([0.0, 10.0]);






