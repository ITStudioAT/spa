<?php

use Illuminate\Foundation\Application;
use Illuminate\Session\Middleware\StartSession;
use Itstudioat\Spa\Http\Middleware\WebAllowed;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->append(StartSession::class);
        $middleware->alias([
            'web-allowed' => WebAllowed::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
