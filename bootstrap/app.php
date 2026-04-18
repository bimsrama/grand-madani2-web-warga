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
    ->withMiddleware(function (Middleware $middleware) {
        // ── Register custom middleware aliases ──────────────────────────
        $middleware->alias([
            /**
             * Protects resident-only routes.
             * Checks session('resident_id') – pure server-side, no localStorage.
             */
            'resident.auth' => \App\Http\Middleware\ResidentAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
