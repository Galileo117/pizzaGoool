<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autoriza rutas web para uno o varios roles y envía al login a invitados.
 */
class VerificarRol
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Auth::user();

        if (! in_array($usuario->rol, $roles, true)) {
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}
