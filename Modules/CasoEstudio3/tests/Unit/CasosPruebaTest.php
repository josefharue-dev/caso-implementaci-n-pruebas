<?php

namespace Modules\CasoEstudio3\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CasoEstudio3\Models\Curso;
use Modules\CasoEstudio3\Models\Grupo;
use Modules\CasoEstudio3\Models\Docente;
use Modules\CasoEstudio3\Models\Estudiante;
use Modules\CasoEstudio3\Models\EstudianteMatriculado;

class CasosPruebaTest extends TestCase
{
    use RefreshDatabase;

    public function test_cp01_registrar_grupo_con_curso(): void
    {
        // Registrar el curso IF6100
        $curso = Curso::create([
            'sigla' => 'IF6100',
            'nombreCurso' => 'Análisis y Diseño de Sistemas',
            'creditos' => 4,
        ]);

        // Crear el grupo 01
        $grupo = Grupo::create([
            'numeroCiclo' => 2,
            'ano' => 2026,
            'numeroGrupo' => 1,
            'abierto' => true,
            'curso_id' => $curso->id,
        ]);

        // Recuperar el grupo desde la base de datos
        $grupoRecuperado = Grupo::find($grupo->id);

        // Comprobar los atributos
        $this->assertNotNull($grupoRecuperado);
        $this->assertSame(2, $grupoRecuperado->numeroCiclo);
        $this->assertSame(2026, $grupoRecuperado->ano);
        $this->assertSame(1, $grupoRecuperado->numeroGrupo);
        $this->assertIsBool($grupoRecuperado->abierto);
        $this->assertTrue($grupoRecuperado->abierto);

        // Comprobar el curso asociado
        $this->assertSame(
            'IF6100',
            $grupoRecuperado->curso->sigla
        );

        $this->assertSame(
            4,
            $grupoRecuperado->curso->creditos
        );

        // Comprobar que existe un grupo registrado
        $this->assertDatabaseCount('grupos', 1);
    }


public function test_cp02_curso_con_varios_grupos(): void
{
    // Registrar el curso IF6100
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    // Crear tres grupos para el mismo curso
    for ($i = 1; $i <= 3; $i++) {
        Grupo::create([
            'numeroCiclo' => 2,
            'ano' => 2026,
            'numeroGrupo' => $i,
            'abierto' => $i <= 2,
            'curso_id' => $curso->id,
        ]);
    }

    // Recuperar los grupos del curso
    $grupos = $curso->grupos;

    // Filtrar los grupos abiertos
    $abiertos = $grupos->where('abierto', true);

    // Verificar que existen tres grupos
    $this->assertCount(3, $grupos);

    // Verificar que dos están abiertos
    $this->assertCount(2, $abiertos);

    // Verificar que los abiertos son 1 y 2
    $this->assertEqualsCanonicalizing(
        [1, 2],
        $abiertos->pluck('numeroGrupo')->all()
    );

    // Verificar que todos pertenecen al mismo curso
    foreach ($grupos as $grupo) {
        $this->assertEquals(
            $curso->id,
            $grupo->curso_id
        );
    }

    // Verificar los registros en la base de datos
    $this->assertDatabaseCount('grupos', 3);
}


public function test_cp03_grupo_acepta_dos_docentes(): void
{
    // Registrar el curso IF6100
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    // Crear el grupo 01 sin docentes
    $grupo = Grupo::create([
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => false,
        'curso_id' => $curso->id,
    ]);

    // Registrar los dos docentes
    $docente1 = Docente::create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    $docente2 = Docente::create([
        'codEmpleado' => 1002,
        'nombreEmpleado' => 'Luis',
        'apellidosEmpleado' => 'Rojas Solano',
    ]);

    // Asignar ambos docentes al grupo
    $grupo->asignarDocente(1001);
    $grupo->asignarDocente(1002);

    // Recuperar el grupo desde la base de datos
    $grupo = Grupo::find($grupo->id);

    // Verificar que tiene exactamente dos docentes
    $this->assertCount(2, $grupo->docentes);

    // Verificar los códigos de los docentes
    $this->assertEqualsCanonicalizing(
        [1001, 1002],
        $grupo->docentes->pluck('codEmpleado')->all()
    );

    // Verificar la relación inversa
    $this->assertTrue(
        $docente1->grupos->contains('id', $grupo->id)
    );

    // Verificar los registros en la tabla intermedia
    $this->assertDatabaseCount('docente_grupo', 2);
}


public function test_cp04_rechazar_tercer_docente(): void
{
    // Registrar el curso IF6100
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    // Crear el grupo 01 cerrado
    $grupo = Grupo::create([
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => false,
        'curso_id' => $curso->id,
    ]);

    // Registrar los tres docentes
    Docente::create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    Docente::create([
        'codEmpleado' => 1002,
        'nombreEmpleado' => 'Luis',
        'apellidosEmpleado' => 'Rojas Solano',
    ]);

    Docente::create([
        'codEmpleado' => 1003,
        'nombreEmpleado' => 'Carla',
        'apellidosEmpleado' => 'Jiménez Araya',
    ]);

    // Asignar los dos primeros docentes
    $grupo->asignarDocente(1001);
    $grupo->asignarDocente(1002);

    // Intentar asignar un tercer docente
    try {
        $grupo->asignarDocente(1003);

        $this->fail(
            'Se esperaba una DomainException.'
        );
    } catch (\DomainException $e) {
        $this->assertSame(
            'Un grupo no puede tener más de dos docentes.',
            $e->getMessage()
        );
    }

    // Recuperar el grupo desde la base de datos
    $grupo = $grupo->fresh();

    // Verificar que conserva dos docentes
    $this->assertCount(2, $grupo->docentes);

    // Verificar que mantiene los docentes originales
    $this->assertEqualsCanonicalizing(
        [1001, 1002],
        $grupo->docentes->pluck('codEmpleado')->all()
    );

    // Verificar que el docente 1003 no fue asignado
    $this->assertDatabaseMissing('docente_grupo', [
        'grupo_id' => $grupo->id,
        'docente_codEmpleado' => 1003,
    ]);

    // Comprobar que solo hay dos asignaciones
    $this->assertDatabaseCount('docente_grupo', 2);
}


public function test_cp05_no_abrir_grupo_con_14_estudiantes(): void
{
    // Registrar el curso
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    // Crear el grupo cerrado
    $grupo = Grupo::create([
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => false,
        'curso_id' => $curso->id,
    ]);

    // Registrar un docente
    Docente::create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    // Asignar el docente al grupo
    $grupo->asignarDocente(1001);

    // Registrar y matricular 14 estudiantes
    for ($i = 1; $i <= 14; $i++) {
        $estudiante = Estudiante::create([
            'carne' => sprintf('C%05d', $i),
            'nombre' => 'Estudiante',
            'apellidos' => 'Prueba ' . $i,
            'codEmpleadoConsejero' => 1001,
        ]);

        EstudianteMatriculado::create([
            'grupo_id' => $grupo->id,
            'estudiante_carne' => $estudiante->carne,
            'nota' => null,
        ]);
    }

    // Verificar las cantidades registradas
    $this->assertSame(
        14,
        $grupo->estudiantesMatriculados()->count()
    );

    $this->assertSame(
        1,
        $grupo->docentes()->count()
    );

    // Verificar que no cumple las condiciones
    $this->assertFalse($grupo->puedeAbrirse());

    // Intentar abrir el grupo
    try {
        $grupo->abrir();

        $this->fail(
            'Se esperaba una DomainException.'
        );
    } catch (\DomainException $e) {
        $this->assertSame(
            'El grupo no cumple las condiciones para abrirse.',
            $e->getMessage()
        );
    }

    // Verificar que permanece cerrado
    $this->assertFalse($grupo->fresh()->abierto);

    // Confirmar el estado en la base de datos
    $this->assertDatabaseHas('grupos', [
        'id' => $grupo->id,
        'abierto' => false,
    ]);
}


public function test_cp06_abrir_grupo_con_15_estudiantes(): void
{
    // Registrar el curso IF6100
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombreCurso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    // Crear el grupo inicialmente cerrado
    $grupo = Grupo::create([
        'numeroCiclo' => 2,
        'ano' => 2026,
        'numeroGrupo' => 1,
        'abierto' => false,
        'curso_id' => $curso->id,
    ]);

    // Registrar un docente
    Docente::create([
        'codEmpleado' => 1001,
        'nombreEmpleado' => 'Ana',
        'apellidosEmpleado' => 'Mora Vargas',
    ]);

    // Asignar el docente al grupo
    $grupo->asignarDocente(1001);

    // Registrar y matricular 15 estudiantes
    for ($i = 1; $i <= 15; $i++) {
        $estudiante = Estudiante::create([
            'carne' => sprintf('C%05d', $i),
            'nombre' => 'Estudiante',
            'apellidos' => 'Prueba ' . $i,
            'codEmpleadoConsejero' => 1001,
        ]);

        EstudianteMatriculado::create([
            'grupo_id' => $grupo->id,
            'estudiante_carne' => $estudiante->carne,
            'nota' => null,
        ]);
    }

    // Verificar las cantidades registradas
    $this->assertSame(
        15,
        $grupo->estudiantesMatriculados()->count()
    );

    $this->assertSame(
        1,
        $grupo->docentes()->count()
    );

    // Verificar que inicialmente está cerrado
    $this->assertFalse($grupo->abierto);

    // Verificar que cumple las condiciones
    $this->assertTrue($grupo->puedeAbrirse());

    // Abrir el grupo
    $grupo->abrir();

    // Recuperar el estado actualizado
    $grupoActualizado = $grupo->fresh();

    // Verificar que ahora está abierto
    $this->assertTrue($grupoActualizado->abierto);

    // Confirmar el cambio en la base de datos
    $this->assertDatabaseHas('grupos', [
        'id' => $grupo->id,
        'abierto' => true,
    ]);
}

}

