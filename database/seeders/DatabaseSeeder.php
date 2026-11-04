<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /** Carga los roles, permisos y cuentas de ejemplo del proyecto escolar. */
    public function run(): void
    {
        $this->call([PermisoSeeder::class, RolSeeder::class]);
        $rol = Rol::where('nombre', 'Administrador')->firstOrFail();

        // Contraseña de ejemplo para las pruebas locales de la escuela.
        User::firstOrCreate(['email' => 'admin@subasart.test'], [
            'name' => 'Administrador',
            'password' => Hash::make('password'),
            'idrol' => $rol->id,
        ]);
        $this->call(JugadorSeeder::class);
    }
}
