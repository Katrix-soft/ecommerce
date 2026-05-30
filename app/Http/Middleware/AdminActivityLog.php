<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminActivityLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Registramos solo peticiones de modificación de estado (POST, PUT, PATCH, DELETE)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $user = $request->user();
            
            // Enmascaramos campos sensibles para que no aparezcan en los logs de auditoría
            $payload = $request->except([
                'password', 
                'password_confirmation', 
                'current_password',
                'card_number', 
                'cvv', 
                'token', 
                'key', 
                'pass',
                'secret'
            ]);

            $logData = [
                'user_id'    => $user ? $user->id : 'Guest/System',
                'user_email' => $user ? $user->email : 'Guest/System',
                'ip'         => $request->ip(),
                'method'     => $request->method(),
                'url'        => $request->fullUrl(),
                'payload'    => $payload,
                'status'     => $response->getStatusCode(),
                'user_agent' => $request->userAgent(),
            ];

            try {
                // Creamos un canal dinámico para registrarlo en su propio archivo diario de auditoría
                Log::build([
                    'driver' => 'daily',
                    'path'   => storage_path('logs/admin-activity.log'),
                    'days'   => 30,
                    'level'  => 'info',
                ])->info('Audit Trail', $logData);
            } catch (\Exception $e) {
                // Fallback en caso de que falle la creación dinámica del canal
                Log::info('Admin Activity (Fallback): ' . json_encode($logData, JSON_UNESCAPED_UNICODE));
            }
        }

        return $response;
    }
}
