<?php

namespace Database\Factories;

use App\Models\Partida;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Partida> */
class PartidaFactory extends Factory
{
    /** Prepara datos de ejemplo para probar partidas sin usar registros reales. */
    public function definition(): array
    {
        return ['clave' => fake()->unique()->regexify('[A-Z0-9]{8}'), 'status' => 1, 'fecha_creacion' => now()];
    }
}
