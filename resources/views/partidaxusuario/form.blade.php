@extends('layouts.master')
@section('titulo', $registro->exists ? 'Editar participante' : 'Agregar participante')
@section('contenido')
{{-- El mismo formulario sirve para agregar y editar; old conserva lo escrito si hay errores. --}}
<section class="tarjeta formulario">
    <form method="POST" action="{{ route('partidaxusuario.save', $registro->id) }}" enctype="multipart/form-data">
        {{-- Este código permite que Laravel compruebe el envío del formulario. --}}
        @csrf
        <div class="campo">
            {{-- Se eligen una partida y una cuenta existente de Jugadores. --}}
            <label for="idpartida">Partida</label>
            <select id="idpartida" name="idpartida" required>
                <option value="">Selecciona una opción</option>
                @foreach($partidas as $partida)
                    <option value="{{ $partida->id }}" @selected(old('idpartida', $registro->idpartida) == $partida->id)>{{ $partida->clave }}</option>
                @endforeach
            </select>
        </div>
        <div class="campo">
            <label for="idusuario">Usuario</label>
            <select id="idusuario" name="idusuario" required>
                <option value="">Selecciona una opción</option>
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}" @selected(old('idusuario', $registro->idusuario) == $usuario->id)>{{ $usuario->name }} · {{ $usuario->email }}</option>
                @endforeach
            </select>
        </div>
        {{-- El botón elegido indica al controlador si debe guardar o eliminar. --}}
        <div class="acciones-formulario">
            <button class="boton" type="submit" name="accion" value="Guardar">Guardar registro</button>
            <button class="boton secundario" type="reset">Restablecer valores</button>
            <a class="boton secundario" href="{{ route('partidaxusuario.index') }}">Cancelar</a>
            @if($registro->exists)
                <button class="boton peligro" type="submit" name="accion" value="Eliminar" formnovalidate data-confirmar="¿Deseas eliminar este registro?">Eliminar registro</button>
            @endif
        </div>
    </form>
</section>
@endsection
