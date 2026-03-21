<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    /**
     * Redirect semua request ke halaman maintenance jika MAINTENANCE_MODE=true.
     * Admin (is_admin = 1) tetap bisa mengakses website seperti biasa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isMaintenanceOn = env('MAINTENANCE_MODE', false);

        if ($isMaintenanceOn) {
            // Jangan redirect jika sudah di route maintenance itu sendiri
            if ($request->routeIs('maintenance')) {
                return $next($request);
            }

            // Admin yang sudah login bisa bypass maintenance
            if ($request->user() && $request->user()->is_admin) {
                return $next($request);
            }

            // Semua pengguna lain diarahkan ke halaman maintenance
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
