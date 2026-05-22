<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModule
{
    /**
     * Middleware para verificar que el admin tiene el módulo habilitado.
     * Uso: middleware('check.module:families')
     *
     * Los superadmins siempre pasan.
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        // Los superadmins siempre tienen acceso
        if ($user->hasRole('superadmin')) {
            return $next($request);
        }

        // Verificar si el admin tiene el módulo habilitado
        if (!$user->hasModule($module)) {
            abort(403, 'No tenés acceso a este módulo. Contactá al Super Admin.');
        }

        return $next($request);
    }
}
