<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Inicio') | Subas Art</title>
    <link rel="stylesheet" href="{{ asset('plantilla/fonts/fonts.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@auth sistema @else acceso @endauth">
    {{-- Todas las páginas usan esta plantilla. --}}
    @auth
        <aside class="menu" id="menu">
            <a class="marca" href="{{ route('inicio') }}"><span class="material-symbols-outlined marca-icono">token</span> SUBAS ART</a>
            <p class="etiqueta">MENÚ PRINCIPAL</p>
            <nav aria-label="Menú principal">
                <a class="{{ request()->routeIs('inicio') ? 'activo' : '' }}" href="{{ route('inicio') }}"><span class="material-symbols-outlined">home</span> Inicio</a>
                {{-- El helper muestra solo las opciones permitidas para el rol de la cuenta. --}}
                @if(tiene_permiso('administrar_usuario'))
                    <a class="{{ request()->routeIs('usuario.*') ? 'activo' : '' }}" href="{{ route('usuario.index') }}"><span class="material-symbols-outlined">group</span> Usuarios</a>
                @endif
                @if(tiene_permiso('administrar_permiso'))
                    <a class="{{ request()->routeIs('permiso.*') ? 'activo' : '' }}" href="{{ route('permiso.index') }}"><span class="material-symbols-outlined">verified_user</span> Permisos</a>
                @endif
                @if(tiene_permiso('administrar_rol'))
                    <a class="{{ request()->routeIs('rol.*') ? 'activo' : '' }}" href="{{ route('rol.index') }}"><span class="material-symbols-outlined">manage_accounts</span> Roles</a>
                @endif
                @if(tiene_permiso('administrar_obra'))
                    <a class="{{ request()->routeIs('obras.*') ? 'activo' : '' }}" href="{{ route('obras.index') }}"><span class="material-symbols-outlined">palette</span> Obras</a>
                @endif
                @if(tiene_permiso('administrar_autor'))
                    <a class="{{ request()->routeIs('autores.*') ? 'activo' : '' }}" href="{{ route('autores.index') }}"><span class="material-symbols-outlined">person_edit</span> Autores</a>
                @endif
                @if(tiene_permiso('administrar_categoria'))
                    <a class="{{ request()->routeIs('categorias.*') ? 'activo' : '' }}" href="{{ route('categorias.index') }}"><span class="material-symbols-outlined">category</span> Categorías</a>
                @endif
                @if(tiene_permiso('administrar_partidaxusuario'))
                    <a class="{{ request()->routeIs('partidaxusuario.*') ? 'activo' : '' }}" href="{{ route('partidaxusuario.index') }}"><span class="material-symbols-outlined">groups</span> Participantes</a>
                @endif
                @if(tiene_permiso('administrar_partida'))
                    <a class="{{ request()->routeIs('partidas.*') ? 'activo' : '' }}" href="{{ route('partidas.index') }}"><span class="material-symbols-outlined">sports_esports</span> Partidas</a>
                @endif
            </nav>
            <div class="menu-pie">Subas Art · Proyecto escolar</div>
        </aside>
        <header class="barra">
            <button class="boton secundario abrir-menu" id="abrir-menu" type="button" aria-controls="menu" aria-expanded="false" aria-label="Abrir menú"><span class="material-symbols-outlined">menu</span></button>
            <div class="cuenta"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="boton secundario" type="submit"><span class="material-symbols-outlined">logout</span> Cerrar sesión</button></form>
        </header>
    @endauth

    <main class="principal">
        @auth
            <div class="cabecera"><div><p class="etiqueta">SUBAS ART</p><h1>@yield('titulo', 'Inicio')</h1></div>@yield('accion')</div>
        @endauth
        <div class="contenido">
            @if(session('mensaje'))<div class="aviso" role="status">{{ session('mensaje') }}</div>@endif
            @if($errors->any())
                <div class="errores" role="alert"><strong>Revisa estos datos:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('contenido')
        </div>
    </main>
</body>
</html>
