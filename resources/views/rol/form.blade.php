@extends('layouts.master')
@section('titulo', $registro->exists ? 'Editar rol' : 'Agregar rol')
@section('contenido')
<section class="tarjeta formulario">
    <h2>Datos del rol</h2>
    <p class="texto-suave">Completa los datos y guarda el registro.</p>

    <!-- Formulario para agregar o editar el registro. -->
    <form method="POST" action="{{ route('rol.save', $registro->id) }}">
        @csrf
        <div class="campo">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $registro->nombre) }}" required maxlength="100">
            @error('nombre')
                <small class="error">{{ $message }}</small>
            @enderror
        </div>
        <div class="acciones-formulario">
            <button class="boton" type="submit" name="accion" value="Guardar">Guardar registro</button>
            <button class="boton secundario" type="reset">Restablecer valores</button>
            <a class="boton secundario" href="{{ route('rol.index') }}">Cancelar</a>
            @if($registro->exists)
                <button class="boton peligro" type="submit" name="accion" value="Eliminar" formnovalidate data-confirmar="¿Deseas eliminar este registro?">Eliminar registro</button>
            @endif
        </div>
    </form>
</section>
@endsection
