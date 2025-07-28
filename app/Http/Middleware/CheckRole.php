<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si el usuario no está autenticado, redirigir a la página de login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Verificar si el rol del usuario está en la lista de roles permitidos
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Si el usuario no tiene el rol necesario, redirigir o abortar
        // Puedes redirigir a una página de "Acceso Denegado" o al dashboard
        return redirect('/dashboard')->with('error', 'No tienes permiso para acceder a esta sección.');
        // Alternativamente, puedes usar abort(403, 'Acceso Denegado');
    }
}