<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\Partidaxusuario;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartidaxusuarioController extends Controller
{
    /** Muestra el listado de participantes. */
    public function index(): View
    {
        $registros = Partidaxusuario::with(['partida', 'usuario'])->orderByDesc('id')->get();

        return view('partidaxusuario.index', compact('registros'));
    }

    /** Abre el formulario para agregar o editar un participante. */
    public function form(?int $id = null): View
    {
        $registro = $id === null ? new Partidaxusuario : Partidaxusuario::findOrFail($id);
        $partidas = Partida::orderBy('clave')->get();
        $usuarios = User::orderBy('name')->get();

        return view('partidaxusuario.form', compact('registro', 'partidas', 'usuarios'));
    }

    /** Revisa los datos y guarda o elimina un participante, según el botón elegido. */
    public function save(Request $request, ?int $id = null): RedirectResponse
    {
        $request->validate(['accion' => 'required|in:Guardar,Eliminar']);
        $registro = $id === null ? new Partidaxusuario : Partidaxusuario::findOrFail($id);

        if ($request->input('accion') === 'Eliminar') {
            abort_unless($registro->exists, 404);
            $registro->delete();

            return redirect()->route('partidaxusuario.index')->with('mensaje', 'Registro eliminado.');
        }

        /** Un usuario puede participar en varias partidas, pero solo una vez en cada una. */
        $datos = $request->validate([
            'idpartida' => 'required|integer|exists:partida,id',
            'idusuario' => ['required', 'integer', 'exists:users,id', Rule::unique('partidaxusuario')->where('idpartida', $request->integer('idpartida'))->ignore($registro->id)],
        ]);
        $registro->fill($datos)->save();

        return redirect()->route('partidaxusuario.index')->with('mensaje', 'Cambio guardado.');
    }
}
