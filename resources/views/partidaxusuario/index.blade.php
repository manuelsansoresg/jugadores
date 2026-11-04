@extends('layouts.master')
@section('titulo', 'Participantes')
@section('accion')
    <a class="boton" href="{{ route('partidaxusuario.form') }}">+ Agregar participante</a>
@endsection
@section('contenido')
{{-- Comparte la plantilla de Jugadores y muestra los registros del catálogo. --}}
<section class="tarjeta listado">
    <div class="tabla-contenedor">
        <table>
            <caption>Listado de participantes</caption>
            <thead><tr><th scope="col">Partida</th><th scope="col">Usuario</th><th scope="col">Correo electrónico</th><th scope="col">Acciones</th></tr></thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr>
                        <td>{{ $registro->partida->clave }}</td><td>{{ $registro->usuario->name }}</td><td>{{ $registro->usuario->email }}</td>
                        <td><a class="boton secundario pequeno" href="{{ route('partidaxusuario.form', $registro->id) }}">Editar</a></td>
                    </tr>
                {{-- Si el catálogo está vacío, indicamos cómo agregar el primer registro. --}}
                @empty
                    <tr><td class="vacio" colspan="4">No hay registros. Usa el botón Agregar participante para comenzar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="paginacion">Total: {{ $registros->count() }} registros.</p>
</section>
@endsection
