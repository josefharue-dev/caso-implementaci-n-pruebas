<?php

namespace Modules\CasoEstudio3\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CasoEstudio3\Models\Grupo;
use Modules\CasoEstudio3\Models\Curso;

class GrupoFactory extends Factory
{
    protected $model = Grupo::class;

    public function definition(): array
    {
        return [
            'numeroCiclo' => 2,
            'ano' => 2026,
            'numeroGrupo' => $this->faker->numberBetween(1, 10),
            'abierto' => false,
            'curso_id' => Curso::factory(),
        ];
    }
}


