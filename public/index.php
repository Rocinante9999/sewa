<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Cek Maintenance Mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Register Auto Loader
require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| VERCEL READ-ONLY FILESYSTEM FIX (STORAGE & BOOTSTRAP CACHE)
|--------------------------------------------------------------------------
| Kode ini akan memindahkan semua lokasi penulisan file Laravel ke /tmp
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
    $tmpPath . '/bootstrap/cache', // <--- Ini yang bikin error sebelumnya
];

// Buat folder-folder tersebut jika belum ada
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// --- MAGIC STEP: OVERRIDE LOKASI CACHE BOOTSTRAP ---
// Kita paksa Laravel membaca/menulis cache sistem di /tmp
// Ini memanfaatkan fitur Environment Variable bawaan Laravel
$pkgPath = $tmpPath . '/bootstrap/cache';
putenv("APP_PACKAGES_CACHE={$pkgPath}/packages.php");
putenv("APP_SERVICES_CACHE={$pkgPath}/services.php");
putenv("APP_CONFIG_CACHE={$pkgPath}/config.php");
putenv("APP_ROUTES_CACHE={$pkgPath}/routes-v7.php");
putenv("APP_EVENTS_CACHE={$pkgPath}/events.php");
putenv("VIEW_COMPILED_PATH={$tmpPath}/storage/framework/views");

// 3. Load Framework
$app = require_once __DIR__.'/../bootstrap/app.php';

// 4. Pindahkan Storage Path Aplikasi
$app->useStoragePath($tmpPath . '/storage');

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);