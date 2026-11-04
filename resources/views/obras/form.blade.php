@extends('layouts.master')
@section('titulo', $registro->exists ? 'Editar obra' : 'Agregar obra')
@section('contenido')
{{-- El mismo formulario sirve para agregar y editar; old conserva lo escrito si hay errores. --}}
<section class="tarjeta formulario">
    <form method="POST" action="{{ route('obras.save', $registro->id) }}" enctype="multipart/form-data">
        {{-- Este código permite que Laravel compruebe el envío del formulario. --}}
        @csrf
        <div class="campo">
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" value="{{ old('nombre', $registro->nombre) }}" maxlength="100" required>
        </div>
        <div class="campo">
            {{-- Las opciones vienen de los catálogos de autores y categorías. --}}
            <label for="idautor">Autor</label>
            <select id="idautor" name="idautor" required>
                <option value="">Selecciona una opción</option>
                @foreach($autores as $autor)
                    <option value="{{ $autor->id }}" @selected(old('idautor', $registro->idautor) == $autor->id)>{{ $autor->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="campo">
            <label for="idcategoria">Categoría</label>
            <select id="idcategoria" name="idcategoria" required>
                <option value="">Selecciona una opción</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected(old('idcategoria', $registro->idcategoria) == $categoria->id)>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="campo">
            <label for="foto">Foto</label>
            {{-- Al editar mostramos la foto actual; subir otra es opcional. --}}
            @if($registro->foto)
                <img class="foto-catalogo" src="{{ asset($registro->foto) }}" alt="Foto actual de {{ $registro->nombre }}">
            @endif
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
            <small>Opcional. JPG, PNG, GIF o WebP de hasta 2 MB. Al editar, déjala vacía para conservar la foto actual.</small>
        </div>
        {{-- El botón elegido indica al controlador si debe guardar o eliminar. --}}
        <div class="acciones-formulario">
            <button class="boton" type="submit" name="accion" value="Guardar">Guardar registro</button>
            <button class="boton secundario" type="reset">Restablecer valores</button>
            <a class="boton secundario" href="{{ route('obras.index') }}">Cancelar</a>
            @if($registro->exists)
                <button class="boton peligro" type="submit" name="accion" value="Eliminar" formnovalidate data-confirmar="¿Deseas eliminar este registro?">Eliminar registro</button>
            @endif
        </div>
    </form>
</section>
@endsection
