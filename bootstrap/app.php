<?php

use App\Http\Middleware\CheckIsAdmin;
use App\Http\Middleware\CheckIsMonthly;
use App\Http\Middleware\CheckIsSubscription;
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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web( [
            CheckIsSubscription::class,
        ]);
        $middleware->appendToGroup('CheckIsAdmin', [
            CheckIsAdmin::class,
        ]);
        $middleware->appendToGroup('CheckIsMonthly', [
            CheckIsMonthly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
