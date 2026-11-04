<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    /** Muestra el listado de autores. */
    public function index(): View
    {
        $registros = Autor::orderBy('nombre')->get();

        return view('autores.index', compact('registros'));
    }

    /** Abre el formulario para agregar o editar un autor. */
    public function form(?int $id = null): View
    {
        $registro = $id === null ? new Autor : Autor::findOrFail($id);

        return view('autores.form', compact('registro'));
    }

    /** Revisa los datos y guarda o elimina un autor, según el botón elegido. */
    public function save(Request $request, ?int $id = null): RedirectResponse
    {
        $request->validate(['accion' => 'required|in:Guardar,Eliminar']);
        $registro = $id === null ? new Autor : Autor::findOrFail($id);

        if ($request->input('accion') === 'Eliminar') {
            abort_unless($registro->exists, 404);
            /** Conservamos los registros que todavía están ligados a una obra. */
            if ($registro->obras()->exists()) {
                return back()->withErrors(['registro' => 'No puedes eliminar este registro porque tiene obras asociadas.']);
            }
            $fotoAnterior = $registro->foto;
            $registro->delete();
            eliminar_foto($fotoAnterior);

            return redirect()->route('autores.index')->with('mensaje', 'Registro eliminado.');
        }

        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);
        $fotoAnterior = $registro->foto;
        /** La foto actual se conserva si no se elige otra imagen. */
        unset($datos['foto']);
        if ($request->hasFile('foto')) {
            $datos['foto'] = guardar_foto($request->file('foto'), 'fotos/autores');
        }
        $registro->fill($datos)->save();
        /** La foto anterior se borra después de guardar el cambio. */
        if ($request->hasFile('foto')) {
            eliminar_foto($fotoAnterior);
        }

        return redirect()->route('autores.index')->with('mensaje', 'Cambio guardado.');
    }
}
