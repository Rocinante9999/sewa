<?php

// 1. Load Bootstrap Laravel
$app = require __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| FIX VERCEL READ-ONLY FILESYSTEM
|--------------------------------------------------------------------------
|
*/
$storage = '/tmp/storage';

if (!is_dir($storage)) {
    // Buat struktur folder storage di /tmp secara manual
    mkdir($storage . '/framework/views', 0777, true);
    mkdir($storage . '/framework/cache', 0777, true);
    mkdir($storage . '/framework/sessions', 0777, true);
    mkdir($storage . '/logs', 0777, true);
    mkdir($storage . '/app', 0777, true);
}

// Perintahkan Laravel menggunakan path baru ini
$app->useStoragePath($storage);

/*
|--------------------------------------------------------------------------
| Jalankan Aplikasi
|--------------------------------------------------------------------------
*/
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);