@extends('layouts.master')
@section('titulo', 'Registro de usuario')
@section('contenido')
<section class="tarjeta formulario registro">
    <p class="etiqueta">SUBAS ART</p>
    <h1>Registro de usuario</h1>
    <p class="texto-suave">Elige tu rol, completa tus datos y agrega una foto de perfil.</p>

    <!-- enctype permite enviar la foto junto con los datos. -->
    <form method="POST" action="{{ route('registro.save') }}" enctype="multipart/form-data">
        @csrf
        <div class="campo">
            <label for="rol">Rol</label>
            <select id="rol" name="rol" required>
                <option value="Jugador" @if(old('rol', 'Jugador') == 'Jugador') selected @endif>Jugador</option>
                <option value="Administrador" @if(old('rol', 'Jugador') == 'Administrador') selected @endif>Administrador</option>
            </select>
            @error('rol')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="name">Nombre completo</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name">
            @error('name')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="alias">Nombre de perfil</label>
            <input type="text" id="alias" name="alias" value="{{ old('alias') }}" required maxlength="100">
            @error('alias')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email">
            @error('email')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="foto">Foto de perfil</label>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" required>
            <small>JPG, PNG o WEBP. Máximo 2 MB.</small>
            @error('foto')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <small>Escribe al menos 8 caracteres.</small>
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">
            @error('password_confirmation')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="acciones-formulario">
            <button class="boton" type="submit">Crear mi cuenta</button>
            <a class="boton secundario" href="{{ route('login') }}">Volver al inicio de sesión</a>
        </div>
    </form>
</section>
@endsection
