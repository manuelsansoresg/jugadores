<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = ['name', 'email', 'password', 'idrol'];

    // La contraseña no se muestra al convertir el usuario en una lista de datos.
    protected $hidden = ['password', 'remember_token'];

    // Cada usuario tiene un rol.
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idrol');
    }

    // Cada usuario puede tener un perfil de jugador.
    public function jugador()
    {
        return $this->hasOne(Jugador::class, 'user_id');
    }

    // Busca el permiso dentro de los permisos del rol.
    public function tienePermiso($clave)
    {
        if ($this->rol == null) {
            return false;
        }

        foreach ($this->rol->permisos as $permiso) {
            if ($permiso->clave == $clave) {
                return true;
            }
        }

        return false;
    }
}
