@extends('layouts.master')
@section('titulo', $registro->exists ? 'Editar categoría' : 'Agregar categoría')
@section('contenido')
{{-- El mismo formulario sirve para agregar y editar; old conserva lo escrito si hay errores. --}}
<section class="tarjeta formulario">
    <form method="POST" action="{{ route('categorias.save', $registro->id) }}" enctype="multipart/form-data">
        {{-- Este código permite que Laravel compruebe el envío del formulario. --}}
        @csrf
        <div class="campo">
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" value="{{ old('nombre', $registro->nombre) }}" maxlength="100" required>
        </div>
        {{-- El botón elegido indica al controlador si debe guardar o eliminar. --}}
        <div class="acciones-formulario">
            <button class="boton" type="submit" name="accion" value="Guardar">Guardar registro</button>
            <button class="boton secundario" type="reset">Restablecer valores</button>
            <a class="boton secundario" href="{{ route('categorias.index') }}">Cancelar</a>
            @if($registro->exists)
                <button class="boton peligro" type="submit" name="accion" value="Eliminar" formnovalidate data-confirmar="¿Deseas eliminar este registro?">Eliminar registro</button>
            @endif
        </div>
    </form>
</section>
@endsection
