@extends('layouts.master')
@section('titulo', $registro->exists ? 'Editar usuario' : 'Agregar usuario')
@section('contenido')
<section class="tarjeta formulario">
    <h2>Datos del usuario</h2>
    <p class="texto-suave">Completa los datos y guarda el registro.</p>

    <!-- Formulario para agregar o editar el registro. -->
    <form method="POST" action="{{ route('usuario.save', $registro->id) }}">
        @csrf
        <div class="campo">
            <label for="name">Nombre completo</label>
            <input type="text" id="name" name="name" value="{{ old('name', $registro->name) }}" required maxlength="100">
            @error('name')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email', $registro->email) }}" required maxlength="255">
            @error('email')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="idrol">Rol</label>
            <select id="idrol" name="idrol" required>
                <option value="">Selecciona un rol</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}" @if(old('idrol', $registro->idrol) == $rol->id) selected @endif>{{ $rol->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="campo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" minlength="8" autocomplete="new-password">
            <small>Al editar, déjala vacía para conservar la contraseña actual.</small>
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="campo">
            <label for="password_confirmation">Confirmar contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" autocomplete="new-password">
            @error('password_confirmation')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="acciones-formulario">
            <button class="boton" type="submit" name="accion" value="Guardar">Guardar registro</button>
            <button class="boton secundario" type="reset">Restablecer valores</button>
            <a class="boton secundario" href="{{ route('usuario.index') }}">Cancelar</a>
            @if($registro->exists)
                <button class="boton peligro" type="submit" name="accion" value="Eliminar" formnovalidate data-confirmar="¿Deseas eliminar este registro?">Eliminar registro</button>
            @endif
        </div>
    </form>
</section>
@endsection
