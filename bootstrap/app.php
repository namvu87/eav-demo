<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Thêm Inertia middleware vào web group
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        // Nếu cần thêm middleware cho API CORS
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Alias middleware (optional)
        $middleware->alias([
            'inertia' => \App\Http\Middleware\HandleInertiaRequests::class,
            'eav.access' => \App\Http\Middleware\CheckEavAccess::class,

        ]);

        $middleware->statefulApi();


    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
