@extends('layouts.master')
@section('titulo', 'Acceso denegado')
@section('contenido')
<section class="tarjeta formulario perfil"><span class="material-symbols-outlined perfil-icono error">shield_lock</span><p class="etiqueta">ERROR 403</p><h1>Acceso denegado</h1><p class="texto-suave">Tu rol no tiene permiso para entrar a esta página.</p><a class="boton" href="{{ route('inicio') }}">Volver al inicio</a></section>
@endsection
