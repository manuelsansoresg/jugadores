<?php

namespace App\Http\Controllers;

use App\Models\Jugador;
use Illuminate\Http\Request;

class InicioController extends Controller
{
    // Todos entran a la misma página; la vista muestra el contenido permitido.
    public function index(Request $request)
    {
        $jugador = Jugador::where('user_id', $request->user()->id)->first();

        return view('inicio', compact('jugador'));
    }
}
