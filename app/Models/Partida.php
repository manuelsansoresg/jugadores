<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Guarda la clave, el estado y la fecha de cada partida. La tabla conserva el nombre usado en Biblioteca. */
class Partida extends Model
{
    use HasFactory;

    protected $table = 'partida';

    /** Estas tablas no usan las fechas automáticas de Laravel. */
    public $timestamps = false;

    /** Campos que se pueden guardar después de validar el formulario. */
    protected $fillable = ['clave', 'status', 'fecha_creacion'];

    /** Permite consultar las inscripciones de esta partida. */
    public function participantes(): HasMany
    {
        return $this->hasMany(Partidaxusuario::class, 'idpartida');
    }

    /** Obtiene las cuentas que participan en esta partida. */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'partidaxusuario', 'idpartida', 'idusuario');
    }

    /** Permite mostrar la fecha y tratar el estado como un número. */
    protected function casts(): array
    {
        return ['fecha_creacion' => 'datetime', 'status' => 'integer'];
    }
}
