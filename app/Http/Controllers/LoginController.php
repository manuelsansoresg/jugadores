<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // Muestra el formulario de inicio de sesión.
    public function index()
    {
        return view('login');
    }

    // Comprueba el correo y la contraseña.
    public function entrar(Request $request)
    {
        $datos = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($datos)) {
            return back()->withErrors(['email' => 'El correo o la contraseña no son correctos.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('inicio');
    }

    // Cierra la sesión.
    public function salir(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Muestra el formulario de registro.
    public function registro()
    {
        return view('registro');
    }

    // Guarda la cuenta con el rol elegido, su perfil y su foto.
    public function registrar(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users',
            'alias' => 'required|string|max:100|unique:jugador',
            'rol' => 'required|string|in:Jugador,Administrador',
            'password' => 'required|string|min:8|confirmed',
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $rol = Rol::where('nombre', $datos['rol'])->firstOrFail();
        $foto = null;

        // Si algo falla, no queda una cuenta sin su perfil de jugador.
        DB::beginTransaction();
        try {
            /** El helper guarda la imagen y devuelve la ruta para el perfil. */
            $foto = guardar_foto($request->file('foto'), 'fotos/jugador');

            $usuario = new User;
            $usuario->name = $datos['name'];
            $usuario->email = $datos['email'];
            $usuario->password = Hash::make($datos['password']);
            $usuario->idrol = $rol->id;
            $usuario->save();

            $jugador = new Jugador;
            $jugador->alias = $datos['alias'];
            $jugador->user_id = $usuario->id;
            $jugador->foto = $foto;
            $jugador->save();

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            eliminar_foto($foto);
            throw $error;
        }

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('inicio')->with('mensaje', 'Tu cuenta fue creada. ¡Bienvenido!');
    }
}
