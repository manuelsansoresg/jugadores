<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    protected string $carpetaPublica;

    protected function setUp(): void
    {
        parent::setUp();

        // Las pruebas importan la estructura SQL en una base temporal, sin migraciones.
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Las pruebas deben usar SQLite en memoria.');
        }
        $sql = explode('-- Datos de ejemplo', file_get_contents(base_path('jugadores.sql')))[0];
        $sql = str_replace(['SET NAMES utf8mb4;', 'AUTO_INCREMENT', ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'], ['', 'AUTOINCREMENT', ''], $sql);
        DB::unprepared($sql);

        /** Adaptamos el SQL de MySQL para probarlo en SQLite sin tocar la base real. */
        $biblioteca = file_get_contents(base_path('biblioteca.sql'));
        $estructura = explode('-- Ajuste al tipo real', $biblioteca)[0];
        $participantes = explode("SET @crear_participantes = CONCAT('", $biblioteca)[1];
        $participantes = explode("');", $participantes)[0];
        $participantes = str_replace("', @tipo_usuario, '", 'INTEGER', $participantes);
        $estructura .= $participantes.';';
        $estructura = str_replace(['SET NAMES utf8mb4;', 'AUTO_INCREMENT', ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'], ['', 'AUTOINCREMENT', ''], $estructura);
        DB::unprepared($estructura);

        // Las fotos de las pruebas no se mezclan con las fotos reales.
        $this->carpetaPublica = sys_get_temp_dir().'/jugadores-public-'.uniqid();
        File::ensureDirectoryExists($this->carpetaPublica);
        $this->app->usePublicPath($this->carpetaPublica);
        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        if (isset($this->carpetaPublica)) {
            File::deleteDirectory($this->carpetaPublica);
        }

        parent::tearDown();
    }
}
