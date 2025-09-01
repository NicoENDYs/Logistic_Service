<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        // Comprobar si el usuario tiene sesión y si su rol coincide
        if (!Auth::check() || optional(Auth::user()->role)->name !== $role) {
            abort(403, 'No tienes permisos.');
        }
        return $next($request);
    }
}
