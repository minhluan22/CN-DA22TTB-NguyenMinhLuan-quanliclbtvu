<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))

    // ---------------------
    // ROUTES
    // ---------------------
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up'
    )

    // ---------------------
    // MIDDLEWARE
    // ---------------------
    ->withMiddleware(function (Middleware $middleware) {

        // ALIAS
        $middleware->alias([
            'adminOnly' => \App\Http\Middleware\AdminOnly::class,
            'role'      => \App\Http\Middleware\CheckRole::class,
        ]);

        // API GROUP (Giữ nguyên mặc định của Laravel 11)
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // WEB GROUP (KHÔNG THÊM HandleCors)
        // Laravel tự xử lý đủ CORS ở layer global nếu cần.
    })

    // ---------------------
    // EXCEPTIONS
    // ---------------------
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();
