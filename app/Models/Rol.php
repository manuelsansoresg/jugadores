<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'rol';

    // No se guardan fechas automáticas.
    public $timestamps = false;

    // Campos que se pueden guardar.
    protected $fillable = ['nombre'];

    // Un rol puede tener varios permisos.
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rolxpermiso', 'idrol', 'idpermiso');
    }

    // Un rol puede estar asignado a varios usuarios.
    public function usuarios()
    {
        return $this->hasMany(User::class, 'idrol');
    }
}
