@extends('layouts.master')
@section('titulo', 'Permisos del rol: '.$rol->nombre)
@section('contenido')
<section class="tarjeta formulario">
    <h2>Asignación de permisos</h2>
    <p class="texto-suave">Marca los accesos que tendrá el rol {{ $rol->nombre }}.</p>

    <!-- Solo se envían los permisos que estén marcados. -->
    <form method="POST" action="{{ route('rol.asignar', $rol->id) }}">
        @csrf
        @foreach($permisos as $permiso)
            <label class="casilla">
                <input type="checkbox" name="idpermiso[]" value="{{ $permiso->id }}" @if(in_array($permiso->id, old('idpermiso', $asignados))) checked @endif>
                <span>{{ $permiso->nombre }}</span>
            </label>
        @endforeach

        <div class="acciones-formulario">
            <button class="boton" type="submit">Guardar permisos</button>
            <a class="boton secundario" href="{{ route('rol.index') }}">Volver a roles</a>
        </div>
    </form>
</section>
@endsection
