<?php

namespace Database\Factories;

use App\Models\Autor;
use App\Models\Categoria;
use App\Models\Obra;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Obra> */
class ObraFactory extends Factory
{
    /** Prepara datos de ejemplo para probar obras sin usar registros reales. */
    public function definition(): array
    {
        return ['nombre' => fake()->sentence(3), 'idautor' => Autor::factory(), 'idcategoria' => Categoria::factory(), 'foto' => null];
    }
}
