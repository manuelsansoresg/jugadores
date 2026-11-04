@extends('layouts.master')
@section('titulo', 'Iniciar sesión')
@section('contenido')
<div class="tarjeta-acceso">
    <div class="presentacion"><img src="{{ asset('plantilla/logo2.svg') }}" alt="Subas Art"><p>Bienvenido a Subas Art</p><span>Entra con tu cuenta para continuar.</span></div>
    <section class="formulario-acceso">
        <p class="etiqueta">SUBAS ART</p><h1>Iniciar sesión</h1><p class="texto-suave">Ingresa tu correo y tu contraseña.</p>
        <form method="POST" action="{{ route('login.entrar') }}">
            @csrf
        <div class="campo">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus>
            @error('email')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
            <button class="boton ancho" type="submit">Iniciar sesión <span class="material-symbols-outlined">arrow_forward</span></button>
        </form>
        <div class="separador">¿No tienes una cuenta?</div>
        <a class="boton secundario ancho" href="{{ route('registro') }}">Crear una cuenta</a>
        <p class="pie">Subas Art · 2026</p>
    </section>
</div>
@endsection
