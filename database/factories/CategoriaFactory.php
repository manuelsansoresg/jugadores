<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Categoria> */
class CategoriaFactory extends Factory
{
    /** Prepara datos de ejemplo para probar categorías sin usar registros reales. */
    public function definition(): array
    {
        return ['nombre' => fake()->word()];
    }
}
