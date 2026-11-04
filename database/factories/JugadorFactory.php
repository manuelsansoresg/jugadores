<?php

namespace Database\Factories;

use App\Models\Jugador;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Jugador> */
class JugadorFactory extends Factory
{
    protected $model = Jugador::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['alias' => fake()->unique()->userName(), 'user_id' => User::factory()];
    }
}
