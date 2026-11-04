<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    /** Crea o actualiza los permisos de Jugadores y de los catálogos de Biblioteca. */
    public function run(): void
    {
        $permisos = [
            'administrar_usuario' => 'Administrar usuarios',
            'administrar_permiso' => 'Administrar permisos',
            'administrar_rol' => 'Administrar roles',
            'administrar_autor' => 'Administrar autores',
            'administrar_categoria' => 'Administrar categorías',
            'administrar_obra' => 'Administrar obras',
            'administrar_partida' => 'Administrar partidas',
            'administrar_partidaxusuario' => 'Administrar participantes',
            'home_admin' => 'Ver panel de administración',
            'home_jugador' => 'Ver perfil de jugador',
        ];
        foreach ($permisos as $clave => $nombre) {
            Permiso::updateOrCreate(['clave' => $clave], ['nombre' => $nombre]);
        }
    }
}
