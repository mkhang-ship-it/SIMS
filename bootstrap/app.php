<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\ImpersonationMiddleware;
use App\Http\Middleware\LogSystemActivity;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'CheckPermission' => CheckPermission::class,
            'maintenance' => MaintenanceMode::class,
            'impersonation' => ImpersonationMiddleware::class,
            'logsystem' => LogSystemActivity::class,
        ]);

        // Apply maintenance middleware globally to all routes
        $middleware->append(MaintenanceMode::class);

        // Apply impersonation middleware to web routes
        $middleware->web(append: [
            ImpersonationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
