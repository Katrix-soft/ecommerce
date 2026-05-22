<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdmin
{
    /**
     * Middleware para proteger rutas de super admin.
     * Verifica que el usuario autenticado tenga el rol 'superadmin'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Acceso restringido a Super Administradores.');
        }

        return $next($request);
    }
}
