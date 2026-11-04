<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Lista los usuarios con su rol.
    public function index()
    {
        $registros = User::with('rol')->get();

        return view('usuario.index', compact('registros'));
    }

    // Abre el formulario para agregar o editar.
    public function form($id = null)
    {
        if ($id == null) {
            $registro = new User;
        } else {
            $registro = User::findOrFail($id);
        }
        $roles = Rol::all();

        return view('usuario.form', compact('registro', 'roles'));
    }

    // Guarda o elimina el usuario según el botón presionado.
    public function save(Request $request, $id = null)
    {
        $request->validate(['accion' => 'required|in:Guardar,Eliminar']);

        if ($id == null) {
            $registro = new User;
        } else {
            $registro = User::findOrFail($id);
        }

        if ($request->accion == 'Eliminar') {
            if (! $registro->exists) {
                abort(404);
            }
            if ($registro->id == $request->user()->id) {
                return back()->withErrors(['registro' => 'No puedes eliminar tu propia cuenta.']);
            }

            $jugador = $registro->jugador;
            $registro->delete();
            /** El helper también acepta que el usuario no tenga perfil o foto. */
            eliminar_foto($jugador?->foto);
        } else {
            $reglaPassword = 'required|string|min:8|confirmed';
            if ($registro->exists) {
                $reglaPassword = 'nullable|string|min:8|confirmed';
            }

            $datos = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:255|unique:users,email,'.$registro->id,
                'password' => $reglaPassword,
                'idrol' => 'required|integer|exists:rol,id',
            ]);

            if ($registro->id == $request->user()->id && $datos['idrol'] != $registro->idrol) {
                return back()->withErrors(['idrol' => 'No puedes cambiar tu propio rol.'])->withInput();
            }

            $registro->name = $datos['name'];
            $registro->email = $datos['email'];
            $registro->idrol = $datos['idrol'];
            if (! empty($datos['password'])) {
                $registro->password = Hash::make($datos['password']);
            }
            $registro->save();
        }

        return redirect()->route('usuario.index')->with('mensaje', 'Cambio guardado.');
    }
}
