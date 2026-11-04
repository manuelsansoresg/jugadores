<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PartidaController extends Controller
{
    /** Muestra el listado de partidas. */
    public function index(): View
    {
        $registros = Partida::orderByDesc('id')->get();

        return view('partidas.index', compact('registros'));
    }

    /** Abre el formulario para agregar o editar una partida. */
    public function form(?int $id = null): View
    {
        $registro = $id === null ? new Partida : Partida::findOrFail($id);

        return view('partidas.form', compact('registro'));
    }

    /** Revisa los datos y guarda o elimina una partida, según el botón elegido. */
    public function save(Request $request, ?int $id = null): RedirectResponse
    {
        $request->validate(['accion' => 'required|in:Guardar,Eliminar']);
        $registro = $id === null ? new Partida : Partida::findOrFail($id);

        if ($request->input('accion') === 'Eliminar') {
            abort_unless($registro->exists, 404);
            $registro->delete();

            return redirect()->route('partidas.index')->with('mensaje', 'Registro eliminado.');
        }

        $datos = $request->validate([
            'clave' => 'required|string|max:10',
            'status' => 'required|integer',
        ]);
        /** Al editar se conserva la fecha original de la partida. */
        if (! $registro->exists) {
            $datos['fecha_creacion'] = now();
        }
        $registro->fill($datos)->save();

        return redirect()->route('partidas.index')->with('mensaje', 'Cambio guardado.');
    }
}
