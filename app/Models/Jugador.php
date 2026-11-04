<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    use HasFactory;

    // Nombre de la tabla.
    protected $table = 'jugador';

    // No se guardan fechas automáticas.
    public $timestamps = false;

    // Campos que se pueden guardar.
    protected $fillable = ['alias', 'user_id', 'foto'];

    // Cada jugador tiene una cuenta de usuario.
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
