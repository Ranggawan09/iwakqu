<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Percayai semua reverse proxy (dibutuhkan di shared hosting / cPanel)
        // agar Laravel tahu request datang via HTTPS dan generate URL yang benar.
        $middleware->trustProxies(at: '*');

        // Jalankan pengecekan maintenance di semua request web
        $middleware->web(append: [
            \App\Http\Middleware\MaintenanceModeMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/mayar/callback',
        ]);

        $middleware->alias([
            'admin'       => \App\Http\Middleware\AdminMiddleware::class,
            'maintenance' => \App\Http\Middleware\MaintenanceModeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

