<?php

namespace Modules\CasoEstudio3\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CasoEstudio3\Models\Curso;

class CursoFactory extends Factory
{
    protected $model = Curso::class;

    public function definition(): array
    {
        return [
            'sigla' => 'IF6100',
            'nombreCurso' => 'Análisis y Diseño de Sistemas',
            'creditos' => 4,
        ];
    }
}



