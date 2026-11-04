<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'Escribe un correo electrónico válido.',
    'string' => 'El campo :attribute debe ser texto.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'unique' => 'El campo :attribute ya está registrado.',
    'exists' => 'El valor de :attribute no existe.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'mimes' => 'La foto debe ser JPG, PNG o WEBP.',
    'uploaded' => 'No se pudo subir la foto. Revisa que no supere 2 MB.',
    'max' => [
        'string' => 'El campo :attribute no debe superar :max caracteres.',
        'file' => 'La foto no debe superar 2 MB.',
    ],
    'min' => ['string' => 'El campo :attribute debe tener al menos :min caracteres.'],
    'in' => 'El valor de :attribute no es válido.',
    'array' => 'El campo :attribute debe ser una lista.',
    'distinct' => 'El campo :attribute contiene valores repetidos.',
    'regex' => 'El campo :attribute solo permite letras minúsculas y guion bajo.',
    'attributes' => [
        'name' => 'nombre', 'nombre' => 'nombre', 'email' => 'correo electrónico',
        'password' => 'contraseña', 'foto' => 'foto', 'alias' => 'nombre de perfil',
        'idrol' => 'rol', 'clave' => 'clave', 'idpermiso' => 'permisos', 'accion' => 'acción',
        'idpermiso.*' => 'permiso',
    ],
];
