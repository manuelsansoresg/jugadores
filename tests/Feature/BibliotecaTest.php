<?php

use App\Models\Autor;
use App\Models\Categoria;
use App\Models\Obra;
use App\Models\Partida;
use App\Models\Partidaxusuario;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/** Prepara una cuenta administradora para probar los catálogos. */
function administradorBiblioteca(): User
{
    test()->seed([PermisoSeeder::class, RolSeeder::class]);

    return User::factory()->create(['idrol' => Rol::where('nombre', 'Administrador')->firstOrFail()->id]);
}

/** Reutiliza los mismos casos para revisar los cinco catálogos. */
dataset('catalogos biblioteca', [
    ['autores', 'autor'], ['categorias', 'categoria'], ['obras', 'obra'],
    ['partidas', 'partida'], ['partidaxusuario', 'partidaxusuario'],
]);

test('los catálogos de biblioteca exigen sesión y permiso incluso para guardar', function (string $ruta) {
    $this->get('/'.$ruta)->assertRedirect(route('login'));
    $this->get('/'.$ruta.'/form')->assertRedirect(route('login'));
    $this->post('/'.$ruta.'/save', ['accion' => 'Guardar'])->assertRedirect(route('login'));
    $usuario = User::factory()->create();

    $this->actingAs($usuario)->get('/'.$ruta)->assertForbidden();
    $this->get('/'.$ruta.'/form')->assertForbidden();
    $this->post('/'.$ruta.'/save', ['accion' => 'Guardar'])->assertForbidden();
})->with('catalogos biblioteca');

test('el administrador ve cada catálogo en el template y el menú', function (string $ruta) {
    $admin = administradorBiblioteca();

    $this->actingAs($admin)->get('/'.$ruta)->assertOk()->assertSee('SUBAS ART')->assertSee('No hay registros.');
    $this->get('/'.$ruta.'/form')->assertOk()->assertSee('Guardar registro');
    $this->get('/inicio')->assertOk()->assertSee('href="'.route($ruta.'.index').'"', false);
    $this->get('/'.$ruta.'/form/99999')->assertNotFound();
    $this->post('/'.$ruta.'/save/99999', ['accion' => 'Eliminar'])->assertNotFound();
    $this->post('/'.$ruta.'/save', ['accion' => 'Eliminar'])->assertNotFound();
})->with('catalogos biblioteca');

test('el jugador no ve los enlaces de biblioteca', function () {
    $this->seed([PermisoSeeder::class, RolSeeder::class]);
    $jugador = User::factory()->create(['idrol' => Rol::where('nombre', 'Jugador')->firstOrFail()->id]);

    $respuesta = $this->actingAs($jugador)->get('/inicio')->assertOk();

    foreach (['autores', 'categorias', 'obras', 'partidas', 'partidaxusuario'] as $ruta) {
        $respuesta->assertDontSee('href="'.route($ruta.'.index').'"', false);
    }
});

test('los catálogos rechazan campos obligatorios vacíos sin guardar registros', function (string $ruta, string $tabla, array $campos) {
    $admin = administradorBiblioteca();

    $this->actingAs($admin)->post('/'.$ruta.'/save', ['accion' => 'Guardar'])->assertSessionHasErrors($campos);

    $this->assertDatabaseCount($tabla, 0);
})->with([
    ['autores', 'autor', ['nombre']], ['categorias', 'categoria', ['nombre']],
    ['obras', 'obra', ['nombre', 'idautor', 'idcategoria']],
    ['partidas', 'partida', ['clave', 'status']],
    ['partidaxusuario', 'partidaxusuario', ['idpartida', 'idusuario']],
]);

test('se pueden crear editar y eliminar autores y categorías sin foto', function (string $ruta, string $tabla) {
    $admin = administradorBiblioteca();

    $this->actingAs($admin)->post('/'.$ruta.'/save', ['accion' => 'Guardar', 'nombre' => 'Nuevo'])->assertRedirect(route($ruta.'.index'));
    $id = DB::table($tabla)->value('id');
    $this->assertDatabaseHas($tabla, ['id' => $id, 'nombre' => 'Nuevo']);
    $this->get('/'.$ruta.'/form/'.$id)->assertOk()->assertSee('Nuevo');
    $this->post('/'.$ruta.'/save/'.$id, ['accion' => 'Guardar', 'nombre' => 'Modificado'])->assertRedirect(route($ruta.'.index'));
    $this->assertDatabaseHas($tabla, ['id' => $id, 'nombre' => 'Modificado']);
    $this->post('/'.$ruta.'/save/'.$id, ['accion' => 'Eliminar'])->assertRedirect(route($ruta.'.index'));
    $this->assertDatabaseCount($tabla, 0);
})->with([['autores', 'autor'], ['categorias', 'categoria']]);

test('las obras guardan relaciones y protegen los catálogos asociados', function () {
    $admin = administradorBiblioteca();
    $autor = Autor::factory()->create();
    $categoria = Categoria::factory()->create();
    $datos = ['accion' => 'Guardar', 'nombre' => 'Paisaje', 'idautor' => $autor->id, 'idcategoria' => $categoria->id];

    $this->actingAs($admin)->post('/obras/save', $datos)->assertRedirect(route('obras.index'));
    $obra = Obra::firstOrFail();
    $this->assertDatabaseHas('obra', ['id' => $obra->id, 'nombre' => 'Paisaje', 'idautor' => $autor->id, 'idcategoria' => $categoria->id]);
    $this->get('/obras')->assertOk()->assertSee($autor->nombre)->assertSee($categoria->nombre);
    $this->get('/obras/form/'.$obra->id)->assertOk()->assertSee('Paisaje');
    $this->post('/autores/save/'.$autor->id, ['accion' => 'Eliminar'])->assertSessionHasErrors('registro');
    $this->post('/categorias/save/'.$categoria->id, ['accion' => 'Eliminar'])->assertSessionHasErrors('registro');
    $this->assertModelExists($autor);
    $this->assertModelExists($categoria);
    $otroAutor = Autor::factory()->create();
    $this->post('/obras/save/'.$obra->id, [...$datos, 'idautor' => $otroAutor->id])->assertRedirect(route('obras.index'));
    $this->assertDatabaseHas('obra', ['id' => $obra->id, 'idautor' => $otroAutor->id]);
    $this->post('/obras/save/'.$obra->id, ['accion' => 'Eliminar'])->assertRedirect(route('obras.index'));
    $this->assertModelMissing($obra);
});

test('las fotos se conservan reemplazan y eliminan con el registro', function (string $ruta, string $modelo) {
    $admin = administradorBiblioteca();
    $registro = $modelo::factory()->create();
    $datos = ['accion' => 'Guardar', 'nombre' => 'Foto', ...$registro->only(['idautor', 'idcategoria'])];

    $this->actingAs($admin)->post('/'.$ruta.'/save/'.$registro->id, [...$datos, 'foto' => UploadedFile::fake()->image('foto.jpg')])->assertSessionHasNoErrors();
    $foto = $registro->fresh()->foto;
    $this->assertFileExists(public_path($foto));
    $this->get('/'.$ruta)->assertOk()->assertSee(asset($foto));
    $this->post('/'.$ruta.'/save/'.$registro->id, $datos)->assertSessionHasNoErrors();
    expect($registro->fresh()->foto)->toBe($foto);
    $this->post('/'.$ruta.'/save/'.$registro->id, [...$datos, 'foto' => UploadedFile::fake()->image('otra.png')])->assertSessionHasNoErrors();
    $otraFoto = $registro->fresh()->foto;
    $this->assertFileDoesNotExist(public_path($foto));
    $this->assertFileExists(public_path($otraFoto));
    $this->post('/'.$ruta.'/save/'.$registro->id, ['accion' => 'Eliminar'])->assertRedirect(route($ruta.'.index'));
    $this->assertModelMissing($registro);
    $this->assertFileDoesNotExist(public_path($otraFoto));
})->with([['autores', Autor::class], ['obras', Obra::class]]);

test('los archivos inválidos y las referencias inexistentes no se guardan', function () {
    $admin = administradorBiblioteca();

    $this->actingAs($admin)->post('/autores/save', ['accion' => 'Guardar', 'nombre' => 'Autor', 'foto' => UploadedFile::fake()->create('archivo.txt', 10, 'text/plain')])->assertSessionHasErrors('foto');
    $this->post('/obras/save', ['accion' => 'Guardar', 'nombre' => 'Obra', 'idautor' => 9999, 'idcategoria' => 9999])->assertSessionHasErrors(['idautor', 'idcategoria']);
    $this->post('/partidaxusuario/save', ['accion' => 'Guardar', 'idpartida' => 9999, 'idusuario' => 9999])->assertSessionHasErrors(['idpartida', 'idusuario']);
    $this->assertDatabaseCount('autor', 0);
    $this->assertDatabaseCount('obra', 0);
    $this->assertDatabaseCount('partidaxusuario', 0);
});

test('las partidas conservan la fecha de creación y eliminan sus participantes al borrarse', function () {
    $this->freezeTime();
    $admin = administradorBiblioteca();

    $this->actingAs($admin)->post('/partidas/save', ['accion' => 'Guardar', 'clave' => 'ABC123', 'status' => 0])->assertRedirect(route('partidas.index'));
    $partida = Partida::firstOrFail();
    expect($partida->fecha_creacion->format('Y-m-d H:i:s'))->toBe(now()->format('Y-m-d H:i:s'));
    $this->get('/partidas')->assertOk()->assertSee('ABC123');
    $this->get('/partidas/form/'.$partida->id)->assertOk()->assertSee('ABC123');
    $this->travel(1)->hours();
    $this->post('/partidas/save/'.$partida->id, ['accion' => 'Guardar', 'clave' => 'XYZ123', 'status' => 1])->assertRedirect(route('partidas.index'));
    $this->assertDatabaseHas('partida', ['id' => $partida->id, 'clave' => 'XYZ123', 'status' => 1, 'fecha_creacion' => $partida->fecha_creacion->format('Y-m-d H:i:s')]);
    Partidaxusuario::factory()->create(['idpartida' => $partida->id]);
    $this->post('/partidas/save/'.$partida->id, ['accion' => 'Eliminar'])->assertRedirect(route('partidas.index'));
    $this->assertModelMissing($partida);
    $this->assertDatabaseCount('partidaxusuario', 0);
});

test('los participantes usan las cuentas actuales y rechazan duplicados', function () {
    $admin = administradorBiblioteca();
    $usuario = User::factory()->create();
    $otroUsuario = User::factory()->create();
    $partida = Partida::factory()->create();
    $datos = ['accion' => 'Guardar', 'idpartida' => $partida->id, 'idusuario' => $usuario->id];

    $this->actingAs($admin)->post('/partidaxusuario/save', $datos)->assertRedirect(route('partidaxusuario.index'));
    $participante = Partidaxusuario::firstOrFail();
    $this->assertDatabaseHas('partidaxusuario', ['idpartida' => $partida->id, 'idusuario' => $usuario->id]);
    $this->get('/partidaxusuario')->assertOk()->assertSee($usuario->email)->assertSee($partida->clave);
    $this->get('/partidaxusuario/form/'.$participante->id)->assertOk()->assertSee($usuario->email);
    $this->post('/partidaxusuario/save', $datos)->assertSessionHasErrors('idusuario');
    $this->assertDatabaseCount('partidaxusuario', 1);
    $this->post('/partidaxusuario/save/'.$participante->id, $datos)->assertSessionHasNoErrors();
    $this->post('/partidaxusuario/save/'.$participante->id, [...$datos, 'idusuario' => $otroUsuario->id])->assertSessionHasNoErrors();
    $this->assertDatabaseHas('partidaxusuario', ['id' => $participante->id, 'idusuario' => $otroUsuario->id]);
    $this->post('/partidaxusuario/save/'.$participante->id, ['accion' => 'Eliminar'])->assertRedirect(route('partidaxusuario.index'));
    $this->assertModelMissing($participante);
    $this->assertModelExists($usuario);
    $this->assertModelExists($partida);
});

test('el sql asigna permisos nuevos solo al administrador y puede repetirse', function () {
    $admin = Rol::factory()->create(['nombre' => 'Administrador']);
    $jugador = Rol::factory()->create(['nombre' => 'Jugador']);
    $sql = explode('-- Permisos de los nuevos catálogos.', file_get_contents(base_path('biblioteca.sql')))[1];

    DB::unprepared($sql);
    DB::unprepared($sql);

    expect($admin->permisos()->count())->toBe(5);
    expect($jugador->permisos()->count())->toBe(0);
    $this->assertDatabaseCount('permiso', 5);
    $this->assertDatabaseCount('rolxpermiso', 5);
});

test('los catálogos escapan el texto ingresado', function () {
    $admin = administradorBiblioteca();
    $texto = '<script>alert(1)</script>';
    Autor::factory()->create(['nombre' => $texto]);
    Categoria::factory()->create(['nombre' => $texto]);
    Obra::factory()->create(['nombre' => $texto]);

    foreach (['autores', 'categorias', 'obras'] as $ruta) {
        $this->actingAs($admin)->get('/'.$ruta)->assertOk()->assertSee($texto)->assertDontSee($texto, false);
    }
});

test('el sql recupera los catálogos y las rutas de sus imágenes sin duplicarlos', function () {
    $sql = explode('-- Datos recuperados de Biblioteca.', file_get_contents(base_path('biblioteca.sql')))[1];

    DB::unprepared($sql);
    DB::unprepared($sql);

    $this->assertDatabaseCount('autor', 1);
    $this->assertDatabaseCount('categoria', 2);
    $this->assertDatabaseCount('obra', 2);
    $this->assertDatabaseCount('partida', 1);
    $this->assertDatabaseHas('obra', ['nombre' => 'obra el principito', 'idautor' => 1, 'idcategoria' => 2]);
    foreach (Obra::all() as $obra) {
        $this->assertFileExists(base_path('public/'.$obra->foto));
    }
    $this->assertFileExists(base_path('public/'.Autor::firstOrFail()->foto));
});

test('cada permiso abre su catálogo sin conceder los demás', function (string $ruta, string $clave) {
    $rol = Rol::factory()->create();
    $this->seed(PermisoSeeder::class);
    $rol->permisos()->attach(Permiso::where('clave', 'administrar_'.$clave)->firstOrFail());
    $usuario = User::factory()->create(['idrol' => $rol->id]);

    $this->actingAs($usuario)->get('/'.$ruta)->assertOk();
    $respuesta = $this->get('/inicio')->assertOk()->assertSee('href="'.route($ruta.'.index').'"', false);

    foreach (array_diff(['autores', 'categorias', 'obras', 'partidas', 'partidaxusuario'], [$ruta]) as $otraRuta) {
        $this->get('/'.$otraRuta)->assertForbidden();
        $respuesta->assertDontSee('href="'.route($otraRuta.'.index').'"', false);
    }
})->with('catalogos biblioteca');
