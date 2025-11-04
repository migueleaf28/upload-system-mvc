<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (PostTooLargeException $e, $request) {
            $maxSize = ini_get('post_max_size');
            
            if ($request->expectsJson() || $request->is('files/upload')) {
                return response()->json([
                    'success' => false,
                    'message' => "Error: El archivo es demasiado grande. Límite máximo: {$maxSize}"
                ], 400);
            }
            
            return back()->with('error', "Error: El archivo es demasiado grande. Límite máximo: {$maxSize}");
        });
    })->create();
