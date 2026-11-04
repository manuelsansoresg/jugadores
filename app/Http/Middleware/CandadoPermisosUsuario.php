<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CandadoPermisosUsuario
{
    // Revisa el permiso antes de entrar a una página.
    public function handle(Request $request, Closure $next, $permiso)
    {
        if (! $request->user()->tienePermiso($permiso)) {
            abort(403);
        }

        return $next($request);
    }
}
