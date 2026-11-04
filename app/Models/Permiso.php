<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'permiso';

    // No se guardan fechas automáticas.
    public $timestamps = false;

    // Campos que se pueden guardar.
    protected $fillable = ['nombre', 'clave'];

    // Un permiso puede pertenecer a varios roles.
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rolxpermiso', 'idpermiso', 'idrol');
    }
}
