<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolxPermiso extends Model
{
    // Nombre de la tabla.
    protected $table = 'rolxpermiso';

    // No se guardan fechas automáticas.
    public $timestamps = false;

    // Campos que se pueden guardar.
    protected $fillable = ['idrol', 'idpermiso'];
}
