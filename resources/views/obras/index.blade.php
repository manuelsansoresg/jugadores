@extends('layouts.master')
@section('titulo', 'Obras')
@section('accion')
    <a class="boton" href="{{ route('obras.form') }}">+ Agregar obra</a>
@endsection
@section('contenido')
{{-- Comparte la plantilla de Jugadores y muestra los registros del catálogo. --}}
<section class="tarjeta listado">
    <div class="tabla-contenedor">
        <table>
            <caption>Listado de obras</caption>
            <thead><tr><th scope="col">Nombre</th><th scope="col">Autor</th><th scope="col">Categoría</th><th scope="col">Foto</th><th scope="col">Acciones</th></tr></thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr>
                        <td>{{ $registro->nombre }}</td><td>{{ $registro->autor->nombre }}</td><td>{{ $registro->categoria->nombre }}</td><td>@if($registro->foto)<img class="foto-catalogo" src="{{ asset($registro->foto) }}" alt="Foto de {{ $registro->nombre }}" loading="lazy">@else Sin foto @endif</td>
                        <td><a class="boton secundario pequeno" href="{{ route('obras.form', $registro->id) }}">Editar</a></td>
                    </tr>
                {{-- Si el catálogo está vacío, indicamos cómo agregar el primer registro. --}}
                @empty
                    <tr><td class="vacio" colspan="5">No hay registros. Usa el botón Agregar obra para comenzar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="paginacion">Total: {{ $registros->count() }} registros.</p>
</section>
@endsection
