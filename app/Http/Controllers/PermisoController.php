<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    // Lista todos los permisos.
    public function index()
    {
        $registros = Permiso::all();

        return view('permiso.index', compact('registros'));
    }

    // Abre el formulario para agregar o editar.
    public function form($id = null)
    {
        if ($id == null) {
            $registro = new Permiso;
        } else {
            $registro = Permiso::findOrFail($id);
        }

        return view('permiso.form', compact('registro'));
    }

    // Guarda o elimina el permiso.
    public function save(Request $request, $id = null)
    {
        $request->validate(['accion' => 'required|in:Guardar,Eliminar']);

        if ($id == null) {
            $registro = new Permiso;
        } else {
            $registro = Permiso::findOrFail($id);
        }

        if ($request->accion == 'Eliminar') {
            if (! $registro->exists) {
                abort(404);
            }
            if ($registro->roles()->exists()) {
                return back()->withErrors(['registro' => 'El permiso está asignado a un rol. Quítalo del rol primero.']);
            }
            $registro->delete();
        } else {
            // La clave se conserva porque se usa para proteger las rutas.
            if ($registro->exists && $request->clave != $registro->clave) {
                return back()->withErrors(['clave' => 'La clave de un permiso guardado no se puede cambiar.'])->withInput();
            }

            $datos = $request->validate([
                'nombre' => 'required|string|max:100',
                'clave' => 'required|string|max:100|regex:/^[a-z_]+$/|unique:permiso,clave,'.$registro->id,
            ]);

            $registro->nombre = $datos['nombre'];
            $registro->clave = $datos['clave'];
            $registro->save();
        }

        return redirect()->route('permiso.index')->with('mensaje', 'Cambio guardado.');
    }
}
