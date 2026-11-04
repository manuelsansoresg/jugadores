<?php

use Illuminate\Http\UploadedFile;

/** Revisa si el rol del usuario actual tiene el permiso; sin sesión devuelve false. */
function tiene_permiso(string $clave): bool
{
    return auth()->user()?->tienePermiso($clave) ?? false;
}

/** Guarda una foto ya validada con un nombre único y devuelve su ruta dentro de public. */
function guardar_foto(UploadedFile $archivo, string $carpeta): string
{
    $nombre = $archivo->hashName();
    $archivo->move(public_path($carpeta), $nombre);

    return $carpeta.'/'.$nombre;
}

/** Borra la foto si existe; no hace nada cuando no hay foto. */
function eliminar_foto(?string $foto): void
{
    if ($foto !== null && is_file(public_path($foto))) {
        unlink(public_path($foto));
    }
}
