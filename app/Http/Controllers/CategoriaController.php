<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /** Muestra el listado de categorías. */
    public function index(): View
    {
        $registros = Categoria::orderBy('nombre')->get();

        return view('categorias.index', compact('registros'));
    }

    /** Abre el formulario para agregar o editar una categoría. */
    public function form(?int $id = null): View
    {
        $registro = $id === null ? new Categoria : Categoria::findOrFail($id);

        return view('categorias.form', compact('registro'));
    }

    /** Revisa los datos y guarda o elimina una categoría, según el botón elegido. */
    public function save(Request $request, ?int $id = null): RedirectResponse
    {
        $request->validate(['accion' => 'required|in:Guardar,Eliminar']);
        $registro = $id === null ? new Categoria : Categoria::findOrFail($id);

        if ($request->input('accion') === 'Eliminar') {
            abort_unless($registro->exists, 404);
            /** Conservamos los registros que todavía están ligados a una obra. */
            if ($registro->obras()->exists()) {
                return back()->withErrors(['registro' => 'No puedes eliminar este registro porque tiene obras asociadas.']);
            }
            $registro->delete();

            return redirect()->route('categorias.index')->with('mensaje', 'Registro eliminado.');
        }

        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
        ]);
        $registro->fill($datos)->save();

        return redirect()->route('categorias.index')->with('mensaje', 'Cambio guardado.');
    }
}
