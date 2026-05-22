<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // 1. Check if logged-in user is deactivated (is_active === false)
        if ($user && !$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => 'Tu cuenta ha sido desactivada. Contactá al administrador del sistema.',
            ]);
        }

        // 2. Resolve the primary tenant (admin) of the store
        $tenant = \App\Models\User::getTenant();

        if ($tenant) {
            // Check if route is related to auth or livewire updates to avoid blocking essential actions
            $isExcludedRoute = $request->is('login', 'logout', 'register', 'livewire/*', 'user/profile', 'user/profile-photo');

            if (!$isExcludedRoute) {
                $isTenantOwnerOrAdmin = $user && ($user->id === $tenant->id || $user->hasRole('superadmin') || $user->hasRole('admin'));

                // A. Check if store is suspended (e.g. non-payment)
                if ($tenant->store_status === 'suspended') {
                    // Block access to everyone except superadmins
                    if (!$user || !$user->hasRole('superadmin')) {
                        $msg = $tenant->suspended_message ?: 'Esta cuenta ha sido pausada temporalmente por falta de pago.';
                        return response()->view('errors.suspended', ['message' => $msg], 403);
                    }
                }

                // B. Check if store is in maintenance mode
                $isMaintenance = ($tenant->store_status === 'maintenance');
                if (!$isMaintenance && $tenant->maintenance_starts_at && $tenant->maintenance_ends_at) {
                    $now = now();
                    if ($now->greaterThanOrEqualTo($tenant->maintenance_starts_at) && $now->lessThanOrEqualTo($tenant->maintenance_ends_at)) {
                        $isMaintenance = true;
                    }
                }

                if ($isMaintenance) {
                    // Maintenance blocks storefront visitors, but lets logged-in admins & superadmins pass
                    if (!$isTenantOwnerOrAdmin) {
                        $msg = $tenant->maintenance_message ?: 'La tienda se encuentra en mantenimiento temporal.';
                        return response()->view('errors.maintenance', [
                            'message' => $msg,
                            'ends_at' => $tenant->maintenance_ends_at
                        ], 503);
                    }
                }
            }
        }

        return $next($request);
    }
}
