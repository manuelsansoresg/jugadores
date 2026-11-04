@extends('layouts.master')
@section('titulo', 'Categorías')
@section('accion')
    <a class="boton" href="{{ route('categorias.form') }}">+ Agregar categoría</a>
@endsection
@section('contenido')
{{-- Comparte la plantilla de Jugadores y muestra los registros del catálogo. --}}
<section class="tarjeta listado">
    <div class="tabla-contenedor">
        <table>
            <caption>Listado de categorías</caption>
            <thead><tr><th scope="col">Nombre</th><th scope="col">Acciones</th></tr></thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr>
                        <td>{{ $registro->nombre }}</td>
                        <td><a class="boton secundario pequeno" href="{{ route('categorias.form', $registro->id) }}">Editar</a></td>
                    </tr>
                {{-- Si el catálogo está vacío, indicamos cómo agregar el primer registro. --}}
                @empty
                    <tr><td class="vacio" colspan="2">No hay registros. Usa el botón Agregar categoría para comenzar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="paginacion">Total: {{ $registros->count() }} registros.</p>
</section>
@endsection
