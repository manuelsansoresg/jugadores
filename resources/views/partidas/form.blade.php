@extends('layouts.master')
@section('titulo', $registro->exists ? 'Editar partida' : 'Agregar partida')
@section('contenido')
{{-- El mismo formulario sirve para agregar y editar; old conserva lo escrito si hay errores. --}}
<section class="tarjeta formulario">
    <form method="POST" action="{{ route('partidas.save', $registro->id) }}" enctype="multipart/form-data">
        {{-- Este código permite que Laravel compruebe el envío del formulario. --}}
        @csrf
        <div class="campo">
            <label for="clave">Clave</label>
            <input id="clave" name="clave" value="{{ old('clave', $registro->clave) }}" maxlength="10" required>
        </div>
        <div class="campo">
            <label for="status">Estado</label>
            <input type="number" step="1" id="status" name="status" value="{{ old('status', $registro->status) }}" required>
        </div>
        @if($registro->exists)
            <p>Fecha de creación: {{ $registro->fecha_creacion->format('d/m/Y H:i') }}</p>
        @endif
        {{-- El botón elegido indica al controlador si debe guardar o eliminar. --}}
        <div class="acciones-formulario">
            <button class="boton" type="submit" name="accion" value="Guardar">Guardar registro</button>
            <button class="boton secundario" type="reset">Restablecer valores</button>
            <a class="boton secundario" href="{{ route('partidas.index') }}">Cancelar</a>
            @if($registro->exists)
                <button class="boton peligro" type="submit" name="accion" value="Eliminar" formnovalidate data-confirmar="¿Deseas eliminar este registro?">Eliminar registro</button>
            @endif
        </div>
    </form>
</section>
@endsection
