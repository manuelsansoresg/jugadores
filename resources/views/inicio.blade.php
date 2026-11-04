@extends('layouts.master')
@section('titulo', 'Inicio')
@section('contenido')
{{-- El helper revisa los permisos del rol para mostrar el panel o el perfil. --}}
@if(tiene_permiso('home_admin'))
<section class="tarjeta bienvenida"><span class="material-symbols-outlined marca-icono">space_dashboard</span><div><h2>Bienvenido, {{ auth()->user()->name }}</h2><p class="texto-suave">Administra las cuentas y los accesos del sistema.</p></div></section>
<div class="tarjetas">
    @if(tiene_permiso('administrar_usuario'))
        <a class="tarjeta enlace-tarjeta" href="{{ route('usuario.index') }}"><span class="material-symbols-outlined">group</span><h2>Usuarios</h2><p>Agrega cuentas y asigna su rol.</p><span>Ver usuarios →</span></a>
    @endif
    @if(tiene_permiso('administrar_permiso'))
        <a class="tarjeta enlace-tarjeta" href="{{ route('permiso.index') }}"><span class="material-symbols-outlined">verified_user</span><h2>Permisos</h2><p>Consulta y administra los accesos.</p><span>Ver permisos →</span></a>
    @endif
    @if(tiene_permiso('administrar_rol'))
        <a class="tarjeta enlace-tarjeta" href="{{ route('rol.index') }}"><span class="material-symbols-outlined">manage_accounts</span><h2>Roles</h2><p>Organiza y asigna los permisos.</p><span>Ver roles →</span></a>
    @endif
    {{-- Estos accesos llevan a los catálogos que antes estaban en Biblioteca. --}}
    @if(tiene_permiso('administrar_obra'))
        <a class="tarjeta enlace-tarjeta" href="{{ route('obras.index') }}"><span class="material-symbols-outlined">palette</span><h2>Obras</h2><p>Administra las obras y sus imágenes.</p><span>Ver obras →</span></a>
    @endif
    @if(tiene_permiso('administrar_autor'))
        <a class="tarjeta enlace-tarjeta" href="{{ route('autores.index') }}"><span class="material-symbols-outlined">person_edit</span><h2>Autores</h2><p>Administra los autores de las obras.</p><span>Ver autores →</span></a>
    @endif
    @if(tiene_permiso('administrar_categoria'))
        <a class="tarjeta enlace-tarjeta" href="{{ route('categorias.index') }}"><span class="material-symbols-outlined">category</span><h2>Categorías</h2><p>Organiza las obras por categoría.</p><span>Ver categorías →</span></a>
    @endif
    @if(tiene_permiso('administrar_partidaxusuario'))
        <a class="tarjeta enlace-tarjeta" href="{{ route('partidaxusuario.index') }}"><span class="material-symbols-outlined">groups</span><h2>Participantes</h2><p>Asigna usuarios a las partidas.</p><span>Ver participantes →</span></a>
    @endif
    @if(tiene_permiso('administrar_partida'))
        <a class="tarjeta enlace-tarjeta" href="{{ route('partidas.index') }}"><span class="material-symbols-outlined">sports_esports</span><h2>Partidas</h2><p>Consulta y administra las partidas.</p><span>Ver partidas →</span></a>
    @endif
</div>
@elseif(tiene_permiso('home_jugador'))
<section class="tarjeta formulario perfil">
    <!-- La foto se muestra directamente desde la carpeta public. -->
    @if($jugador != null && $jugador->foto != null)
        <img class="foto-perfil" src="{{ asset($jugador->foto) }}" alt="Foto de {{ auth()->user()->name }}">
    @else
        <span class="material-symbols-outlined perfil-icono">account_circle</span>
    @endif

    <p class="etiqueta">MI PERFIL</p>
    @if($jugador != null)
        <h2>¡Hola, {{ $jugador->alias }}!</h2>
    @else
        <h2>¡Hola, {{ auth()->user()->name }}!</h2>
    @endif
    <p class="texto-suave">Bienvenido a tu inicio.</p>

    <dl>
        <dt>Nombre</dt>
        <dd>{{ auth()->user()->name }}</dd>
        <dt>Correo electrónico</dt>
        <dd>{{ auth()->user()->email }}</dd>
        <dt>Rol</dt>
        <dd>
            @if(auth()->user()->rol != null)
                {{ auth()->user()->rol->nombre }}
            @else
                Sin rol
            @endif
        </dd>
    </dl>
</section>
@else
<section class="tarjeta"><h2>Bienvenido, {{ auth()->user()->name }}</h2><p>Usa las opciones del menú para entrar a los módulos que tienes permitidos.</p><p class="texto-suave">Si no aparecen opciones, pide al administrador que revise los permisos de tu rol.</p></section>
@endif
@endsection
