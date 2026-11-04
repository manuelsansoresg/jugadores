@extends('layouts.master')
@section('titulo', 'Roles')
@section('accion')
    <a class="boton" href="{{ route('rol.form') }}">+ Agregar rol</a>
@endsection
@section('contenido')
<section class="tarjeta listado">
    <div class="tabla-contenedor">
        <table>
            <caption>Listado de roles</caption>
            <thead>
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Permisos asignados</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr>
                        <td>{{ $registro->nombre }}</td>
                        <td>
                            @forelse($registro->permisos as $permiso)
                                <span>{{ $permiso->nombre }}</span><br>
                            @empty
                                Sin permisos
                            @endforelse
                        </td>
                        <td>
                            <div class="acciones-tabla">
                                <a class="boton secundario pequeno" href="{{ route('rol.form', $registro->id) }}">Editar</a>
                                <a class="boton secundario pequeno" href="{{ route('rol.permisos', $registro->id) }}">Permisos</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="vacio" colspan="3">No hay registros. Usa el botón Agregar rol para comenzar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="paginacion">Total: {{ $registros->count() }} registros.</p>
</section>
@endsection
