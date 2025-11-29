<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => [
        'api/*', // Tetap izinkan rute API Anda
        'uploads/*', // <-- PERBAIKAN: Tambahkan path ini untuk mengizinkan akses gambar
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'], // Izinkan semua metode

    'allowed_origins' => [
        '*' // Izinkan semua domain (untuk development)
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Izinkan semua header

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];