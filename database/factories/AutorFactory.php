<?php

namespace Database\Factories;

use App\Models\Autor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Autor> */
class AutorFactory extends Factory
{
    /** Prepara datos de ejemplo para probar autores sin usar registros reales. */
    public function definition(): array
    {
        return ['nombre' => fake()->name(), 'foto' => null];
    }
}
