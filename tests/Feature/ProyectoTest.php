<?php

use App\Models\Jugador;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Database\Seeders\PermisoSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

/** Crea un usuario con los permisos que necesita cada prueba. */
function usuarioConPermisos(array $claves): User
{
    $rol = Rol::factory()->create();
    foreach ($claves as $clave) {
        $permiso = Permiso::firstOrCreate(['clave' => $clave], ['nombre' => $clave]);
        $rol->permisos()->attach($permiso);
    }

    return User::factory()->create(['idrol' => $rol->id]);
}

test('las páginas públicas muestran el acceso y el registro', function (string $url, string $texto) {
    $this->get($url)->assertOk()->assertSee($texto);
})->with([
    ['/', 'Iniciar sesión'],
    ['/login', 'Iniciar sesión'],
    ['/jugador/autoregistro', 'Registro de usuario'],
]);

test('las rutas privadas redirigen al login sin sesión', function (string $metodo, string $url) {
    $this->{$metodo}($url)->assertRedirect(route('login'));
})->with([
    ['get', '/inicio'], ['get', '/administrador/home'], ['get', '/jugador/home'],
    ['get', '/usuario'], ['get', '/usuario/form'], ['post', '/usuario/save'],
    ['get', '/permiso'], ['get', '/permiso/form'], ['post', '/permiso/save'],
    ['get', '/rol'], ['get', '/rol/form'], ['post', '/rol/save'],
    ['get', '/rol/1/permisos'], ['post', '/rol/1/permisos'],
]);

test('un usuario sin permisos recibe acceso denegado al abrir rutas privadas', function (string $metodo, string $url) {
    $usuario = User::factory()->create(['idrol' => Rol::factory()->create()->id]);

    $this->actingAs($usuario)->{$metodo}($url)->assertForbidden()->assertSee('Acceso denegado');
})->with([
    ['get', '/usuario'], ['get', '/usuario/form'], ['post', '/usuario/save'],
    ['get', '/permiso'], ['get', '/permiso/form'], ['post', '/permiso/save'],
    ['get', '/rol'], ['get', '/rol/form'], ['post', '/rol/save'],
    ['get', '/rol/1/permisos'], ['post', '/rol/1/permisos'],
]);

test('el menú y la ruta solo muestran los permisos del rol', function (string $clave, string $url, string $nombre) {
    $usuario = usuarioConPermisos([$clave]);

    $this->actingAs($usuario)->get($url)->assertOk();
    $respuesta = $this->get('/inicio')->assertOk()->assertSee($usuario->email);
    $respuesta->assertSee('href="'.route($nombre).'"', false);
    $respuesta->assertDontSee('href="'.route('administrador.home').'"', false);
})->with([
    ['administrar_usuario', '/usuario', 'usuario.index'],
    ['administrar_permiso', '/permiso', 'permiso.index'],
    ['administrar_rol', '/rol', 'rol.index'],
]);

test('el jugador ve solo su inicio y no los catálogos de administración', function () {
    $usuario = usuarioConPermisos(['home_jugador']);

    $this->actingAs($usuario)->get('/inicio')->assertOk()
        ->assertSee($usuario->email)
        ->assertDontSee('href="'.route('usuario.index').'"', false)
        ->assertDontSee('href="'.route('permiso.index').'"', false)
        ->assertDontSee('href="'.route('rol.index').'"', false);
});

test('el login abre el único inicio y muestra el contenido del rol', function (string $permiso, string $contenido) {
    $usuario = usuarioConPermisos([$permiso]);

    $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])->assertRedirect(route('inicio'));
    $this->assertAuthenticatedAs($usuario);
    $this->get('/inicio')->assertOk()->assertSee($contenido)
        ->assertDontSee('Inicio administrador')->assertDontSee('Inicio jugador');
})->with([
    ['home_admin', 'Administra las cuentas y los accesos del sistema.'], ['home_jugador', 'MI PERFIL'],
]);

test('el login rechaza una contraseña incorrecta', function () {
    $usuario = User::factory()->create();

    $this->from('/login')->post('/login', ['email' => $usuario->email, 'password' => 'incorrecta'])
        ->assertRedirect('/login')->assertSessionHasErrors(['email' => 'El correo o la contraseña no son correctos.']);
    $this->assertGuest();
});

test('el logout cierra la sesión y vuelve a proteger los catálogos', function () {
    $usuario = usuarioConPermisos(['administrar_usuario']);

    $this->actingAs($usuario)->post('/logout')->assertRedirect(route('login'));
    $this->assertGuest();
    $this->get('/usuario')->assertRedirect(route('login'));
});

test('el registro guarda la foto y asigna el rol elegido', function (string $rolElegido, string $contenido) {
    $this->seed([PermisoSeeder::class, RolSeeder::class]);
    $rolAdmin = Rol::where('nombre', 'Administrador')->firstOrFail();

    $this->post('/jugador/autoregistro/save', [
        'name' => 'Ana López', 'alias' => 'AnaJuega', 'email' => 'ana@example.test',
        'password' => 'secreto123', 'password_confirmation' => 'secreto123',
        'foto' => UploadedFile::fake()->image('perfil.jpg'), 'rol' => $rolElegido, 'idrol' => $rolAdmin->id,
    ])->assertRedirect(route('inicio'))->assertSessionHasNoErrors();

    $usuario = User::where('email', 'ana@example.test')->firstOrFail();
    $jugador = $usuario->jugador;
    $this->assertAuthenticatedAs($usuario);
    expect($usuario->rol->nombre)->toBe($rolElegido);
    expect(Hash::check('secreto123', $usuario->password))->toBeTrue();
    $this->assertDatabaseHas('jugador', ['user_id' => $usuario->id, 'alias' => 'AnaJuega']);
    $this->assertFileExists(public_path($jugador->foto));
    $this->get('/inicio')->assertOk()->assertSee($contenido);
})->with([
    ['Jugador', 'AnaJuega'],
    ['Administrador', 'Administra las cuentas y los accesos del sistema.'],
]);

test('el registro exige todos los datos y la foto', function () {
    $this->post('/jugador/autoregistro/save', [])->assertSessionHasErrors([
        'name' => 'El campo nombre es obligatorio.',
        'email' => 'El campo correo electrónico es obligatorio.',
        'alias' => 'El campo nombre de perfil es obligatorio.',
        'rol' => 'El campo rol es obligatorio.',
        'password' => 'El campo contraseña es obligatorio.',
        'foto' => 'El campo foto es obligatorio.',
    ]);
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('jugador', 0);
});

test('el registro rechaza archivos no válidos y datos incorrectos sin guardar cuentas', function (string $campo, mixed $valor) {
    $datos = [
        'name' => 'Ana', 'alias' => 'AnaJuega', 'email' => 'ana@example.test', 'rol' => 'Jugador',
        'password' => 'secreto123', 'password_confirmation' => 'secreto123',
        'foto' => UploadedFile::fake()->image('perfil.png'),
    ];
    $datos[$campo] = is_callable($valor) ? $valor() : $valor;

    $this->post('/jugador/autoregistro/save', $datos)->assertSessionHasErrors($campo === 'password_confirmation' ? 'password' : $campo);
    $this->assertDatabaseCount('users', 0);
    expect(glob(public_path('fotos/jugador/*')))->toBe([]);
})->with([
    'correo inválido' => ['email', 'no-es-correo'],
    'rol no permitido' => ['rol', 'Profesor'],
    'rol con formato incorrecto' => ['rol', ['Administrador']],
    'nombre largo' => ['name', str_repeat('a', 101)],
    'alias largo' => ['alias', str_repeat('a', 101)],
    'contraseña corta' => ['password', '123'],
    'confirmación diferente' => ['password_confirmation', 'diferente'],
    'archivo de texto' => ['foto', fn () => UploadedFile::fake()->create('texto.txt', 10, 'text/plain')],
    'foto demasiado grande' => ['foto', fn () => UploadedFile::fake()->image('foto.jpg')->size(2049)],
]);

test('el registro rechaza un correo o alias que ya existe', function () {
    $jugador = Jugador::factory()->create(['alias' => 'AnaJuega']);

    $this->post('/jugador/autoregistro/save', [
        'name' => 'Otra Ana', 'alias' => $jugador->alias, 'email' => $jugador->usuario->email, 'rol' => 'Jugador',
        'password' => 'secreto123', 'password_confirmation' => 'secreto123',
        'foto' => UploadedFile::fake()->image('perfil.jpg'),
    ])->assertSessionHasErrors(['alias', 'email']);
    $this->assertDatabaseCount('users', 1);
    expect(glob(public_path('fotos/jugador/*')))->toBe([]);
});

test('el inicio del jugador muestra la ruta pública de su propia foto', function () {
    $otro = Jugador::factory()->create(['foto' => 'fotos/jugador/otro.jpg']);
    $usuario = usuarioConPermisos(['home_jugador']);
    $jugador = Jugador::factory()->create(['user_id' => $usuario->id, 'foto' => 'fotos/jugador/perfil.jpg']);

    $this->actingAs($usuario)->get('/inicio?user_id='.$otro->user_id)
        ->assertOk()->assertSee(asset($jugador->foto))->assertDontSee(asset($otro->foto));
});

test('se puede agregar editar y eliminar un usuario', function () {
    $admin = usuarioConPermisos(['administrar_usuario']);
    $rol = Rol::factory()->create();
    $datos = ['accion' => 'Guardar', 'name' => 'Luis', 'email' => 'luis@example.test',
        'password' => 'secreto123', 'password_confirmation' => 'secreto123', 'idrol' => $rol->id];

    $this->actingAs($admin)->post('/usuario/save', $datos)->assertRedirect(route('usuario.index'))->assertSessionHasNoErrors();
    $usuario = User::where('email', $datos['email'])->firstOrFail();
    $this->assertDatabaseHas('users', ['name' => 'Luis', 'email' => $datos['email'], 'idrol' => $rol->id]);
    expect(Hash::check('secreto123', $usuario->password))->toBeTrue();
    $this->get('/usuario/form/'.$usuario->id)->assertOk()->assertSee('Luis');
    $this->post('/usuario/save/'.$usuario->id, [...$datos, 'name' => 'Luis Pérez', 'password' => '', 'password_confirmation' => ''])
        ->assertRedirect(route('usuario.index'))->assertSessionHasNoErrors();
    $this->assertDatabaseHas('users', ['id' => $usuario->id, 'name' => 'Luis Pérez']);
    expect(Hash::check('secreto123', $usuario->fresh()->password))->toBeTrue();
    $this->post('/usuario/save/'.$usuario->id, ['accion' => 'Eliminar'])->assertRedirect(route('usuario.index'));
    $this->assertModelMissing($usuario);
});

test('un administrador no puede eliminar su cuenta ni cambiar su propio rol', function () {
    $admin = usuarioConPermisos(['administrar_usuario']);
    $otroRol = Rol::factory()->create();

    $this->actingAs($admin)->post('/usuario/save/'.$admin->id, ['accion' => 'Eliminar'])->assertSessionHasErrors('registro');
    $this->assertModelExists($admin);
    $this->post('/usuario/save/'.$admin->id, ['accion' => 'Guardar', 'name' => $admin->name, 'email' => $admin->email, 'idrol' => $otroRol->id])
        ->assertSessionHasErrors('idrol');
    $this->assertDatabaseHas('users', ['id' => $admin->id, 'idrol' => $admin->idrol]);
});

test('el catálogo de usuarios rechaza datos incompletos y correos repetidos', function () {
    $admin = usuarioConPermisos(['administrar_usuario']);

    $this->actingAs($admin)->post('/usuario/save', ['accion' => 'Guardar'])->assertSessionHasErrors(['name', 'email', 'password', 'idrol']);
    $this->post('/usuario/save', ['accion' => 'Guardar', 'name' => 'Duplicado', 'email' => $admin->email, 'password' => 'secreto123', 'password_confirmation' => 'secreto123', 'idrol' => $admin->idrol])
        ->assertSessionHasErrors('email');
    $this->assertDatabaseCount('users', 1);
});

test('los registros inexistentes devuelven una página no encontrada', function () {
    $admin = usuarioConPermisos(['administrar_usuario', 'administrar_rol', 'administrar_permiso']);

    $this->actingAs($admin)->get('/usuario/form/9999')->assertNotFound();
    $this->get('/rol/form/9999')->assertNotFound();
    $this->get('/permiso/form/9999')->assertNotFound();
    $this->post('/usuario/save/9999', ['accion' => 'Eliminar'])->assertNotFound();
});

test('el catálogo de roles permite agregar editar y eliminar un rol libre', function () {
    $admin = usuarioConPermisos(['administrar_rol']);

    $this->actingAs($admin)->get('/rol/form')->assertOk();
    $this->post('/rol/save', ['accion' => 'Guardar', 'nombre' => 'Invitado'])->assertRedirect(route('rol.index'));
    $rol = Rol::where('nombre', 'Invitado')->firstOrFail();
    $this->get('/rol/form/'.$rol->id)->assertOk();
    $this->post('/rol/save/'.$rol->id, ['accion' => 'Guardar', 'nombre' => 'Visitante'])->assertRedirect(route('rol.index'));
    $this->assertDatabaseHas('rol', ['id' => $rol->id, 'nombre' => 'Visitante']);
    $this->post('/rol/save/'.$rol->id, ['accion' => 'Eliminar'])->assertRedirect(route('rol.index'));
    $this->assertModelMissing($rol);
});

test('los permisos del rol se reemplazan y se pueden quitar todos', function () {
    $admin = usuarioConPermisos(['administrar_rol']);
    $rol = Rol::factory()->create();
    $permisos = Permiso::factory()->count(2)->create();
    $rol->permisos()->attach($permisos->first());

    $this->actingAs($admin)->get('/rol/'.$rol->id.'/permisos')->assertOk()->assertSee($rol->nombre);
    $this->post('/rol/'.$rol->id.'/permisos', ['idpermiso' => [$permisos->last()->id]])->assertRedirect(route('rol.index'));
    $this->assertDatabaseMissing('rolxpermiso', ['idrol' => $rol->id, 'idpermiso' => $permisos->first()->id]);
    $this->assertDatabaseHas('rolxpermiso', ['idrol' => $rol->id, 'idpermiso' => $permisos->last()->id]);
    $this->post('/rol/'.$rol->id.'/permisos', [])->assertRedirect(route('rol.index'));
    $this->assertDatabaseMissing('rolxpermiso', ['idrol' => $rol->id]);
});

test('la asignación rechaza permisos inexistentes sin borrar los anteriores', function () {
    $admin = usuarioConPermisos(['administrar_rol']);
    $rol = Rol::factory()->create();
    $permiso = Permiso::factory()->create();
    $rol->permisos()->attach($permiso);

    $this->actingAs($admin)->post('/rol/'.$rol->id.'/permisos', ['idpermiso' => [9999]])->assertSessionHasErrors('idpermiso.0');
    $this->assertDatabaseHas('rolxpermiso', ['idrol' => $rol->id, 'idpermiso' => $permiso->id]);
});

test('los roles base conservan su nombre', function () {
    $this->seed([PermisoSeeder::class, RolSeeder::class]);
    $admin = usuarioConPermisos(['administrar_rol']);
    $rol = Rol::where('nombre', 'Administrador')->firstOrFail();

    $this->actingAs($admin)->post('/rol/save/'.$rol->id, ['accion' => 'Eliminar'])->assertSessionHasErrors('registro');
    $this->post('/rol/save/'.$rol->id, ['accion' => 'Guardar', 'nombre' => 'Otro'])->assertSessionHasErrors('registro');
    $this->assertDatabaseHas('rol', ['id' => $rol->id, 'nombre' => 'Administrador']);
});

test('no se puede eliminar un rol que tiene usuarios', function () {
    $admin = usuarioConPermisos(['administrar_rol']);
    $usuario = User::factory()->create(['idrol' => Rol::factory()->create()->id]);

    $this->actingAs($admin)->post('/rol/save/'.$usuario->idrol, ['accion' => 'Eliminar'])->assertSessionHasErrors('registro');
    $this->assertModelExists($usuario->rol);
});

test('el catálogo de permisos permite agregar editar y eliminar un permiso libre', function () {
    $admin = usuarioConPermisos(['administrar_permiso']);

    $this->actingAs($admin)->get('/permiso/form')->assertOk();
    $this->post('/permiso/save', ['accion' => 'Guardar', 'nombre' => 'Ver prueba', 'clave' => 'ver_prueba'])->assertRedirect(route('permiso.index'));
    $permiso = Permiso::where('clave', 'ver_prueba')->firstOrFail();
    $this->get('/permiso/form/'.$permiso->id)->assertOk();
    $this->post('/permiso/save/'.$permiso->id, ['accion' => 'Guardar', 'nombre' => 'Ver ejemplo', 'clave' => 'ver_prueba'])->assertRedirect(route('permiso.index'));
    $this->assertDatabaseHas('permiso', ['id' => $permiso->id, 'nombre' => 'Ver ejemplo']);
    $this->post('/permiso/save/'.$permiso->id, ['accion' => 'Eliminar'])->assertRedirect(route('permiso.index'));
    $this->assertModelMissing($permiso);
});

test('no se puede cambiar la clave ni eliminar un permiso asignado', function () {
    $admin = usuarioConPermisos(['administrar_permiso']);
    $permiso = $admin->rol->permisos->first();

    $this->actingAs($admin)->post('/permiso/save/'.$permiso->id, ['accion' => 'Guardar', 'nombre' => 'Otro', 'clave' => 'otra_clave'])->assertSessionHasErrors('clave');
    $this->post('/permiso/save/'.$permiso->id, ['accion' => 'Eliminar'])->assertSessionHasErrors('registro');
    $this->assertDatabaseHas('permiso', ['id' => $permiso->id, 'clave' => 'administrar_permiso']);
});

test('las vistas escapan los nombres escritos por los usuarios', function () {
    $admin = usuarioConPermisos(['administrar_usuario', 'administrar_permiso', 'administrar_rol']);
    $texto = '<script>alert(1)</script>';
    User::factory()->create(['name' => $texto]);
    Permiso::factory()->create(['nombre' => $texto]);
    Rol::factory()->create(['nombre' => $texto]);

    foreach (['/usuario', '/permiso', '/rol'] as $url) {
        $this->actingAs($admin)->get($url)->assertOk()->assertSee($texto)->assertDontSee($texto, false);
    }
});

test('al eliminar una cuenta se elimina su perfil y su foto pública', function () {
    $admin = usuarioConPermisos(['administrar_usuario']);
    $archivo = UploadedFile::fake()->image('perfil.jpg');
    $archivo->move(public_path('fotos/jugador'), 'perfil.jpg');
    $jugador = Jugador::factory()->create(['foto' => 'fotos/jugador/perfil.jpg']);
    $usuario = $jugador->usuario;

    $this->actingAs($admin)->post('/usuario/save/'.$usuario->id, ['accion' => 'Eliminar'])
        ->assertRedirect(route('usuario.index'));

    $this->assertModelMissing($usuario);
    $this->assertModelMissing($jugador);
    $this->assertFileDoesNotExist(public_path('fotos/jugador/perfil.jpg'));
});

test('el administrador tiene un solo inicio aunque tenga ambos permisos de inicio', function () {
    $usuario = usuarioConPermisos(['home_admin', 'home_jugador', 'administrar_usuario', 'administrar_permiso', 'administrar_rol']);

    $respuesta = $this->actingAs($usuario)->get('/inicio')->assertOk();
    $respuesta->assertSee('Administra las cuentas y los accesos del sistema.')
        ->assertSee('href="'.route('usuario.index').'"', false)
        ->assertSee('href="'.route('permiso.index').'"', false)
        ->assertSee('href="'.route('rol.index').'"', false)
        ->assertDontSee('MI PERFIL')
        ->assertDontSee('href="'.route('administrador.home').'"', false)
        ->assertDontSee('href="'.route('jugador.home').'"', false);
});

test('las direcciones de inicio anteriores redirigen al inicio común', function (string $url) {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)->get($url)->assertRedirect('/inicio');
})->with(['/administrador/home', '/jugador/home']);
