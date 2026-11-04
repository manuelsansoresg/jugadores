<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Guarda los autores y permite consultar sus obras. La tabla conserva el nombre usado en Biblioteca. */
class Autor extends Model
{
    use HasFactory;

    protected $table = 'autor';

    /** Estas tablas no usan las fechas automáticas de Laravel. */
    public $timestamps = false;

    /** Campos que se pueden guardar después de validar el formulario. */
    protected $fillable = ['nombre', 'foto'];

    /** Permite consultar las obras asociadas a este registro. */
    public function obras(): HasMany
    {
        return $this->hasMany(Obra::class, 'idautor');
    }
}
