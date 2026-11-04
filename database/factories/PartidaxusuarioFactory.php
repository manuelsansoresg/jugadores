<?php

namespace Database\Factories;

use App\Models\Partida;
use App\Models\Partidaxusuario;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Partidaxusuario> */
class PartidaxusuarioFactory extends Factory
{
    /** Prepara datos de ejemplo para probar participantes sin usar registros reales. */
    public function definition(): array
    {
        return ['idpartida' => Partida::factory(), 'idusuario' => User::factory()];
    }
}
