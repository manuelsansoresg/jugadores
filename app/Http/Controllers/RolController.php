<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\RolxPermiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    // Lista los roles y sus permisos.
    public function index()
    {
        $registros = Rol::with('permisos')->get();

        return view('rol.index', compact('registros'));
    }

    // Abre el formulario para agregar o editar.
    public function form($id = null)
    {
        if ($id == null) {
            $registro = new Rol;
        } else {
            $registro = Rol::findOrFail($id);
        }

        return view('rol.form', compact('registro'));
    }

    // Guarda o elimina un rol.
    public function save(Request $request, $id = null)
    {
        $request->validate(['accion' => 'required|in:Guardar,Eliminar']);

        if ($id == null) {
            $registro = new Rol;
        } else {
            $registro = Rol::findOrFail($id);
        }

        // Estos nombres se usan al crear las cuentas iniciales.
        if ($registro->exists && in_array($registro->nombre, ['Administrador', 'Jugador'])) {
            return back()->withErrors(['registro' => 'Los roles base conservan su nombre y no se pueden eliminar.']);
        }

        if ($request->accion == 'Eliminar') {
            if (! $registro->exists) {
                abort(404);
            }
            if ($registro->usuarios()->exists()) {
                return back()->withErrors(['registro' => 'Hay usuarios con este rol. Cambia su rol primero.']);
            }
            $registro->delete();
        } else {
            $datos = $request->validate([
                'nombre' => 'required|string|max:100|unique:rol,nombre,'.$registro->id,
            ]);

            $registro->nombre = $datos['nombre'];
            $registro->save();
        }

        return redirect()->route('rol.index')->with('mensaje', 'Cambio guardado.');
    }

    // Busca el rol por su ID y muestra todas las casillas de permisos.
    public function permisos($id)
    {
        $rol = Rol::findOrFail($id);
        $permisos = Permiso::all();
        $asignados = [];
        $registros = RolxPermiso::where('idrol', $id)->get();
        foreach ($registros as $registro) {
            $asignados[] = $registro->idpermiso;
        }

        return view('rol.permisos', compact('rol', 'permisos', 'asignados'));
    }

    // Borra los permisos anteriores y guarda los que fueron marcados.
    public function asignarPermisos(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);
        $datos = $request->validate([
            'idpermiso' => 'sometimes|array',
            'idpermiso.*' => 'required|integer|distinct|exists:permiso,id',
        ]);

        DB::beginTransaction();
        try {
            RolxPermiso::where('idrol', $rol->id)->delete();

            if (isset($datos['idpermiso'])) {
                foreach ($datos['idpermiso'] as $idpermiso) {
                    $registro = new RolxPermiso;
                    $registro->idrol = $rol->id;
                    $registro->idpermiso = $idpermiso;
                    $registro->save();
                }
            }

            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            throw $error;
        }

        return redirect()->route('rol.index')->with('mensaje', 'Permisos guardados.');
    }
}
