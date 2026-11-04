@extends('layouts.master')
@section('titulo', 'Permisos')
@section('accion')
    <a class="boton" href="{{ route('permiso.form') }}">+ Agregar permiso</a>
@endsection
@section('contenido')
<section class="tarjeta listado">
    <div class="tabla-contenedor">
        <table>
            <caption>Listado de permisos</caption>
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Clave</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr>
                        <td>{{ $registro->nombre }}</td>
                        <td>{{ $registro->clave }}</td>
                        <td>
                            <div class="acciones-tabla">
                                <a class="boton secundario pequeno" href="{{ route('permiso.form', $registro->id) }}">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="vacio" colspan="3">No hay registros. Usa el botón Agregar permiso para comenzar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="paginacion">Total: {{ $registros->count() }} registros.</p>
</section>
@endsection
