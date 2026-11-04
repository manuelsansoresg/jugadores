<?php

use App\Http\Controllers\AutorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ObraController;
use App\Http\Controllers\PartidaController;
use App\Http\Controllers\PartidaxusuarioController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index'])->middleware('guest')->name('portada');
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'entrar'])->middleware('throttle:5,1')->name('login.entrar');
    Route::get('/jugador/autoregistro', [LoginController::class, 'registro'])->name('registro');
    Route::post('/jugador/autoregistro/save', [LoginController::class, 'registrar'])->middleware('throttle:5,1')->name('registro.save');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'salir'])->name('logout');
    Route::get('/inicio', [InicioController::class, 'index'])->name('inicio');
    // Los enlaces anteriores también llevan al único inicio.
    Route::redirect('/administrador/home', '/inicio')->name('administrador.home');
    Route::redirect('/jugador/home', '/inicio')->name('jugador.home');

    Route::middleware('permiso:administrar_usuario')->group(function () {
        Route::get('/usuario', [UsuarioController::class, 'index'])->name('usuario.index');
        Route::get('/usuario/form/{id?}', [UsuarioController::class, 'form'])->whereNumber('id')->name('usuario.form');
        Route::post('/usuario/save/{id?}', [UsuarioController::class, 'save'])->whereNumber('id')->name('usuario.save');
    });

    Route::middleware('permiso:administrar_rol')->group(function () {
        Route::get('/rol', [RolController::class, 'index'])->name('rol.index');
        Route::get('/rol/form/{id?}', [RolController::class, 'form'])->whereNumber('id')->name('rol.form');
        Route::post('/rol/save/{id?}', [RolController::class, 'save'])->whereNumber('id')->name('rol.save');
        Route::get('/rol/{id}/permisos', [RolController::class, 'permisos'])->whereNumber('id')->name('rol.permisos');
        Route::post('/rol/{id}/permisos', [RolController::class, 'asignarPermisos'])->whereNumber('id')->name('rol.asignar');
    });

    Route::middleware('permiso:administrar_permiso')->group(function () {
        Route::get('/permiso', [PermisoController::class, 'index'])->name('permiso.index');
        Route::get('/permiso/form/{id?}', [PermisoController::class, 'form'])->whereNumber('id')->name('permiso.form');
        Route::post('/permiso/save/{id?}', [PermisoController::class, 'save'])->whereNumber('id')->name('permiso.save');
    });

    /** Para abrir o modificar autores, el rol debe tener este permiso. */
    Route::middleware('permiso:administrar_autor')->group(function () {
        Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
        Route::get('/autores/form/{id?}', [AutorController::class, 'form'])->whereNumber('id')->name('autores.form');
        Route::post('/autores/save/{id?}', [AutorController::class, 'save'])->whereNumber('id')->name('autores.save');
    });

    /** Para abrir o modificar categorías, el rol debe tener este permiso. */
    Route::middleware('permiso:administrar_categoria')->group(function () {
        Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
        Route::get('/categorias/form/{id?}', [CategoriaController::class, 'form'])->whereNumber('id')->name('categorias.form');
        Route::post('/categorias/save/{id?}', [CategoriaController::class, 'save'])->whereNumber('id')->name('categorias.save');
    });

    /** Para abrir o modificar obras, el rol debe tener este permiso. */
    Route::middleware('permiso:administrar_obra')->group(function () {
        Route::get('/obras', [ObraController::class, 'index'])->name('obras.index');
        Route::get('/obras/form/{id?}', [ObraController::class, 'form'])->whereNumber('id')->name('obras.form');
        Route::post('/obras/save/{id?}', [ObraController::class, 'save'])->whereNumber('id')->name('obras.save');
    });

    /** Para abrir o modificar partidas, el rol debe tener este permiso. */
    Route::middleware('permiso:administrar_partida')->group(function () {
        Route::get('/partidas', [PartidaController::class, 'index'])->name('partidas.index');
        Route::get('/partidas/form/{id?}', [PartidaController::class, 'form'])->whereNumber('id')->name('partidas.form');
        Route::post('/partidas/save/{id?}', [PartidaController::class, 'save'])->whereNumber('id')->name('partidas.save');
    });

    /** Para abrir o modificar participantes, el rol debe tener este permiso. */
    Route::middleware('permiso:administrar_partidaxusuario')->group(function () {
        Route::get('/partidaxusuario', [PartidaxusuarioController::class, 'index'])->name('partidaxusuario.index');
        Route::get('/partidaxusuario/form/{id?}', [PartidaxusuarioController::class, 'form'])->whereNumber('id')->name('partidaxusuario.form');
        Route::post('/partidaxusuario/save/{id?}', [PartidaxusuarioController::class, 'save'])->whereNumber('id')->name('partidaxusuario.save');
    });
});
