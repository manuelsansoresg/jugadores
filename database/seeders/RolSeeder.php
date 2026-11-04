<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\RolxPermiso;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    // Crea los roles iniciales y asigna sus permisos.
    public function run()
    {
        $admin = Rol::firstOrCreate(['nombre' => 'Administrador']);
        $permisos = Permiso::all();
        foreach ($permisos as $permiso) {
            RolxPermiso::firstOrCreate(['idrol' => $admin->id, 'idpermiso' => $permiso->id]);
        }

        $jugador = Rol::firstOrCreate(['nombre' => 'Jugador']);
        $permiso = Permiso::where('clave', 'home_jugador')->firstOrFail();
        RolxPermiso::firstOrCreate(['idrol' => $jugador->id, 'idpermiso' => $permiso->id]);
    }
}
