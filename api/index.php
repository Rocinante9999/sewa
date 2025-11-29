<?php

/*
|--------------------------------------------------------------------------
| 1. Register The Auto Loader (WAJIB PERTAMA)
|--------------------------------------------------------------------------
| Baris ini memuat semua class library Laravel. Tanpa ini, akan muncul error
| "Class Illuminate\Foundation\Application not found".
*/
require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| 2. Turn On The Lights
|--------------------------------------------------------------------------
| Memuat instance aplikasi Laravel.
*/
$app = require_once __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| 3. FIX VERCEL READ-ONLY FILESYSTEM
|--------------------------------------------------------------------------
| Memindahkan lokasi penyimpanan cache/log ke folder /tmp (temporary)
| karena Vercel memblokir penulisan ke folder project asli.
*/
$storage = '/tmp/storage';

if (!is_dir($storage)) {
    // Buat semua folder yang dibutuhkan Laravel secara manual
    mkdir($storage . '/framework/views', 0777, true);
    mkdir($storage . '/framework/cache', 0777, true);
    mkdir($storage . '/framework/sessions', 0777, true);
    mkdir($storage . '/app', 0777, true);
    mkdir($storage . '/logs', 0777, true);
}

// Perintahkan Laravel menggunakan path storage baru ini
$app->useStoragePath($storage);

/*
|--------------------------------------------------------------------------
| 4. Run The Application
|--------------------------------------------------------------------------
| Menjalankan request dan mengirimkan response kembali ke browser.
*/
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);