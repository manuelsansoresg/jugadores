<?php

namespace Database\Seeders;

use App\Models\Jugador;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JugadorSeeder extends Seeder
{
    /** Cuenta de ejemplo para revisar el inicio del jugador. */
    public function run(): void
    {
        $rol = Rol::where('nombre', 'Jugador')->firstOrFail();
        $usuario = User::firstOrCreate(['email' => 'jugador@subasart.test'], [
            'name' => 'Jugador de ejemplo',
            'password' => Hash::make('password'),
            'idrol' => $rol->id,
        ]);
        Jugador::firstOrCreate(['user_id' => $usuario->id], ['alias' => 'JugadorDemo']);
    }
}
