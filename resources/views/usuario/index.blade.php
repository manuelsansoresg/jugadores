@extends('layouts.master')
@section('titulo', 'Usuarios')
@section('accion')
    <a class="boton" href="{{ route('usuario.form') }}">+ Agregar usuario</a>
@endsection
@section('contenido')
<section class="tarjeta listado">
    <div class="tabla-contenedor">
        <table>
            <caption>Listado de usuarios</caption>
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo electrónico</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr>
                        <td>{{ $registro->name }}</td>
                        <td>{{ $registro->email }}</td>
                        <td>
                            @if($registro->rol != null)
                                {{ $registro->rol->nombre }}
                            @else
                                Sin rol
                            @endif
                        </td>
                        <td>
                            <div class="acciones-tabla">
                                <a class="boton secundario pequeno" href="{{ route('usuario.form', $registro->id) }}">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="vacio" colspan="4">No hay registros. Usa el botón Agregar usuario para comenzar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="paginacion">Total: {{ $registros->count() }} registros.</p>
</section>
@endsection
