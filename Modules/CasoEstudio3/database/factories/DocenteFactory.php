<?php

namespace Modules\CasoEstudio3\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CasoEstudio3\Models\Docente;

class DocenteFactory extends Factory
{
    protected $model = Docente::class;

    public function definition(): array
    {
        return [
            'codEmpleado' => $this->faker->unique()->numberBetween(2000, 99999),
            'nombreEmpleado' => $this->faker->firstName(),
            'apellidosEmpleado' => $this->faker->lastName() . ' ' . $this->faker->lastName(),
        ];
    }
}


