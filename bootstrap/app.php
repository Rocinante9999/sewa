<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php', 
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // --- PERBAIKAN: Hapus baris ini jika ada ---
        // $middleware->web(append: [
        //     \App\Http\Middleware\HandleInertiaRequests::class, 
        // ]);
        // --- BATAS PERBAIKAN ---

        // Middleware lain yang mungkin Anda miliki
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Middleware global lainnya (seperti VerifyCsrfToken) biasanya
        // sudah ditambahkan oleh Laravel secara default di sini.
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();