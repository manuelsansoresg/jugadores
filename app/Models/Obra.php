<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Relaciona cada obra con su autor, su categoría y su foto. La tabla conserva el nombre usado en Biblioteca. */
class Obra extends Model
{
    use HasFactory;

    protected $table = 'obra';

    /** Estas tablas no usan las fechas automáticas de Laravel. */
    public $timestamps = false;

    /** Campos que se pueden guardar después de validar el formulario. */
    protected $fillable = ['nombre', 'idautor', 'idcategoria', 'foto'];

    /** Permite conocer al autor de la obra. */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(Autor::class, 'idautor');
    }

    /** Permite conocer la categoría de la obra. */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'idcategoria');
    }
}
