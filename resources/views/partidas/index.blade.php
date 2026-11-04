@extends('layouts.master')
@section('titulo', 'Partidas')
@section('accion')
    <a class="boton" href="{{ route('partidas.form') }}">+ Agregar partida</a>
@endsection
@section('contenido')
{{-- Comparte la plantilla de Jugadores y muestra los registros del catálogo. --}}
<section class="tarjeta listado">
    <div class="tabla-contenedor">
        <table>
            <caption>Listado de partidas</caption>
            <thead><tr><th scope="col">Clave</th><th scope="col">Estado</th><th scope="col">Fecha de creación</th><th scope="col">Acciones</th></tr></thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr>
                        <td>{{ $registro->clave }}</td><td>{{ $registro->status }}</td><td>{{ $registro->fecha_creacion->format('d/m/Y H:i') }}</td>
                        <td><a class="boton secundario pequeno" href="{{ route('partidas.form', $registro->id) }}">Editar</a></td>
                    </tr>
                {{-- Si el catálogo está vacío, indicamos cómo agregar el primer registro. --}}
                @empty
                    <tr><td class="vacio" colspan="4">No hay registros. Usa el botón Agregar partida para comenzar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="paginacion">Total: {{ $registros->count() }} registros.</p>
</section>
@endsection
