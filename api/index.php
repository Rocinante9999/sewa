<?php

/*
|--------------------------------------------------------------------------
| 1. Register The Auto Loader
|--------------------------------------------------------------------------
*/
require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| 2. VERCEL READ-ONLY FILESYSTEM FIX (FULL VERSION)
|--------------------------------------------------------------------------
| Kita siapkan folder /tmp sebelum Laravel menyala.
*/

// Tentukan lokasi folder sementara
$tmpPath = '/tmp/laravel-project';

// Daftar folder yang wajib ada dan writable
$dirs = [
    $tmpPath . '/storage/framework/views',
    $tmpPath . '/storage/framework/cache',
    $tmpPath . '/storage/framework/sessions',
    $tmpPath . '/storage/app',
    $tmpPath . '/storage/logs',
    $tmpPath . '/bootstrap/cache', // <--- Folder penting untuk error Anda saat ini
];

// Buat folder-folder tersebut jika belum ada
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// --- MAGIC STEP: OVERRIDE LOKASI CACHE BOOTSTRAP ---
// Memaksa Laravel menulis file cache sistem (packages.php, services.php) ke /tmp
// Inilah solusi untuk error "The bootstrap/cache directory must be present and writable"
$pkgPath = $tmpPath . '/bootstrap/cache';
putenv("APP_PACKAGES_CACHE={$pkgPath}/packages.php");
putenv("APP_SERVICES_CACHE={$pkgPath}/services.php");
putenv("APP_CONFIG_CACHE={$pkgPath}/config.php");
putenv("APP_ROUTES_CACHE={$pkgPath}/routes-v7.php");
putenv("APP_EVENTS_CACHE={$pkgPath}/events.php");

// Override lokasi view compiled (blade)
putenv("VIEW_COMPILED_PATH={$tmpPath}/storage/framework/views");

/*
|--------------------------------------------------------------------------
| 3. Turn On The Lights
|--------------------------------------------------------------------------
*/
$app = require_once __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| 4. Pindahkan Storage Path Aplikasi
|--------------------------------------------------------------------------
*/
$app->useStoragePath($tmpPath . '/storage');

/*
|--------------------------------------------------------------------------
| 5. Run The Application
|--------------------------------------------------------------------------
*/
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);