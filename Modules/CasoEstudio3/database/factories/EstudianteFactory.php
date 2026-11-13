<?php

namespace Modules\CasoEstudio3\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CasoEstudio3\Models\Estudiante;
use Modules\CasoEstudio3\Models\Docente;

class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition(): array
    {
        return [
            'carne' => 'C' . $this->faker->unique()->numerify('#####'),
            'nombre' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName() . ' ' . $this->faker->lastName(),
            'consejero_id' => Docente::factory(),
        ];
    }
}


