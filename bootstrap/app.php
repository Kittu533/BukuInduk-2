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

    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'admin' => \App\Http\Middleware\CekAdmin::class,
            'guru' => \App\Http\Middleware\CekGuru::class,
            'wali' => \App\Http\Middleware\CekWaliKelas::class,
            'orangtua' => \App\Http\Middleware\cekOrangTua::class,
            'pembina'  => \App\Http\Middleware\CekPembina::class,
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();