<?php

namespace Modules\CasoEstudio3\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modules\CasoEstudio3\Models\Grupo;
use Modules\CasoEstudio3\Models\Curso;
use Modules\CasoEstudio3\Models\Docente;
use Illuminate\Database\Eloquent\Collection;
use Modules\CasoEstudio3\Models\Estudiante;
use Modules\CasoEstudio3\Models\EstudianteMatriculado;

class GrupoTest extends TestCase
{
    public function test_crear_grupo(): void
    {
        $curso = new Curso([
            'sigla' => 'IF6100',
            'nombreCurso' => 'Analisis y Diseno de Sistemas',
            'creditos' => 4,
        ]);

        $curso->id = 1;

        $grupo = new Grupo([
            'numeroCiclo' => 2,
            'ano' => 2026,
            'numeroGrupo' => 1,
            'abierto' => true,
            'curso_id' => 1,
        ]);

        $grupo->setRelation('curso', $curso);

        $docente1 = new Docente([
            'codEmpleado' => 101,
            'nombreEmpleado' => 'Carlos',
            'apellidosEmpleado' => 'Rodriguez',
        ]);

        $docente2 = new Docente([
            'codEmpleado' => 102,
            'nombreEmpleado' => 'Maria',
            'apellidosEmpleado' => 'Gonzalez',
        ]);

        
$grupo->id = 1;

$matriculas = new Collection();

for ($i = 1; $i <= 15; $i++) {

    $carne = 'C' . str_pad($i, 5, '0', STR_PAD_LEFT);

    $estudiante = new Estudiante([
        'carne' => $carne,
        'nombre' => 'Estudiante ' . $i,
        'apellidos' => 'Prueba',
        'codEmpleadoConsejero' => 101,
    ]);

    $estudiante->setRelation('consejero', $docente1);

    $matricula = new EstudianteMatriculado([
        'grupo_id' => 1,
        'estudiante_carne' => $carne,
        'nota' => 70 + $i,
    ]);

    $matricula->setRelation('estudiante', $estudiante);
    $matricula->setRelation('grupo', $grupo);

    $matriculas->push($matricula);
}

    $grupo->setRelation('estudiantesMatriculados', $matriculas);


        $grupo->setRelation('docentes', new Collection([
            $docente1,
            $docente2,
        ]));
        

        $this->assertEquals(2, $grupo->numeroCiclo);
        $this->assertEquals(2026, $grupo->ano);
        $this->assertEquals(1, $grupo->numeroGrupo);
        $this->assertTrue($grupo->abierto);

        $this->assertEquals('IF6100', $grupo->curso->sigla);
        $this->assertEquals(4, $grupo->curso->creditos);
        $this->assertCount(2, $grupo->docentes);

        $this->assertEquals(
        'Carlos',
        $grupo->docentes[0]->nombreEmpleado
        );

        $this->assertEquals(
            'Maria',
            $grupo->docentes[1]->nombreEmpleado
        );

        
$this->assertCount(
    15,
    $grupo->estudiantesMatriculados
);

$this->assertEquals(
    'C00001',
    $grupo->estudiantesMatriculados[0]->estudiante->carne
);

$this->assertEquals(
    71,
    $grupo->estudiantesMatriculados[0]->nota
);

$this->assertEquals(
    101,
    $grupo->estudiantesMatriculados[0]->estudiante->codEmpleadoConsejero
);

$this->assertEquals(
    'Carlos',
    $grupo->estudiantesMatriculados[0]->estudiante->consejero->nombreEmpleado
);

    }
}


