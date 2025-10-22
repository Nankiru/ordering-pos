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
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'customer.auth' => \App\Http\Middleware\CustomerAuth::class,
            'delivery.auth' => \App\Http\Middleware\DeliveryAuth::class,
        ]);
        // Apply locale middleware to all web requests so pages reflect selected language
        $middleware->appendToGroup('web', [\App\Http\Middleware\SetLocale::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
