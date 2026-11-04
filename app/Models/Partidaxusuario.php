<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Une una cuenta de usuario con una partida. La tabla conserva el nombre usado en Biblioteca. */
class Partidaxusuario extends Model
{
    use HasFactory;

    protected $table = 'partidaxusuario';

    /** Estas tablas no usan las fechas automáticas de Laravel. */
    public $timestamps = false;

    /** Campos que se pueden guardar después de validar el formulario. */
    protected $fillable = ['idpartida', 'idusuario'];

    /** Permite conocer la partida de esta inscripción. */
    public function partida(): BelongsTo
    {
        return $this->belongsTo(Partida::class, 'idpartida');
    }

    /** Usa la misma cuenta con la que la persona inicia sesión en Jugadores. */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idusuario');
    }
}
