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
        // Append the session-token middleware to the default web group so
        // that the Reverb channel guard in routes/channels.php has something
        // to verify even when no login flow exists yet.
        $middleware->web(\App\Http\Middleware\SeedChatSession::class);

        // Register custom middleware aliases
        $middleware->alias([
            'chat.throttle' => \App\Http\Middleware\RateLimitChat::class,
        ]);

        // Apply security headers to every response, including HTTPS
        // enforcement once the site is served over TLS.
        $middleware->append(\App\Http\Middleware\SetSecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();