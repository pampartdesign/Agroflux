<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Web middleware stack additions
        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleFromSession::class,
        ]);

        // Route middleware aliases
        $middleware->alias([
            'super.admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'tenant.selected' => \App\Http\Middleware\EnsureTenantSelected::class,
            'tenant.active'   => \App\Http\Middleware\EnsureTenantSubscriptionActive::class,
            'tenant.member'   => \App\Http\Middleware\EnsureTenantMember::class,
            'tenant.role' => \App\Http\Middleware\EnsureTenantRole::class,
            'module.access'   => \App\Http\Middleware\EnsureModuleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
