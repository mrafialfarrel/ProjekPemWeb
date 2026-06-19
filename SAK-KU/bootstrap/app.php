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
        // --- Tambahan untuk mengatasi Mixed Content (Proxy Render) ---
        $middleware->trustProxies(at: '*');
        // -------------------------------------------------------------

        $middleware->encryptCookies(except: [
            'theme_mode',
            'sakku-role',
        ]);
        
        $middleware->alias([
            'not.guest' => \App\Http\Middleware\CheckGuestAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();