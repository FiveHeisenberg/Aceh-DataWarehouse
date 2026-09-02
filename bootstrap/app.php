<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/penduduk/web.php',

            // 📌 NANTI TAMBAHKAN MODUL LAIN DI SINI (WEB)
            // __DIR__.'/../routes/sosial/web.php',
            // __DIR__.'/../routes/kesehatan/web.php',
            // __DIR__.'/../routes/pendidikan/web.php'
        ],
        api: [
            __DIR__.'/../routes/api.php',
            __DIR__.'/../routes/penduduk/api.php',

            // 📌 NANTI TAMBAHKAN MODUL LAIN DI SINI (API)
            // __DIR__.'/../routes/sosial/api.php',
            // __DIR__.'/../routes/kesehatan/api.php',
            // __DIR__.'/../routes/pendidikan/api.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
