<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Models\Categoria;
use App\Models\Obra;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ObraController extends Controller
{
    /** Muestra el listado de obras. */
    public function index(): View
    {
        $registros = Obra::with(['autor', 'categoria'])->orderBy('nombre')->get();

        return view('obras.index', compact('registros'));
    }

    /** Abre el formulario para agregar o editar una obra. */
    public function form(?int $id = null): View
    {
        $registro = $id === null ? new Obra : Obra::findOrFail($id);
        $autores = Autor::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('obras.form', compact('registro', 'autores', 'categorias'));
    }

    /** Revisa los datos y guarda o elimina una obra, según el botón elegido. */
    public function save(Request $request, ?int $id = null): RedirectResponse
    {
        $request->validate(['accion' => 'required|in:Guardar,Eliminar']);
        $registro = $id === null ? new Obra : Obra::findOrFail($id);

        if ($request->input('accion') === 'Eliminar') {
            abort_unless($registro->exists, 404);
            $fotoAnterior = $registro->foto;
            $registro->delete();
            eliminar_foto($fotoAnterior);

            return redirect()->route('obras.index')->with('mensaje', 'Registro eliminado.');
        }

        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'idautor' => 'required|integer|exists:autor,id',
            'idcategoria' => 'required|integer|exists:categoria,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);
        $fotoAnterior = $registro->foto;
        /** La foto actual se conserva si no se elige otra imagen. */
        unset($datos['foto']);
        if ($request->hasFile('foto')) {
            $datos['foto'] = guardar_foto($request->file('foto'), 'fotos/obras');
        }
        $registro->fill($datos)->save();
        /** La foto anterior se borra después de guardar el cambio. */
        if ($request->hasFile('foto')) {
            eliminar_foto($fotoAnterior);
        }

        return redirect()->route('obras.index')->with('mensaje', 'Cambio guardado.');
    }
}
