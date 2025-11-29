<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Item Belum Tersedia</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8 flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>

            <h1 class="text-2xl font-bold text-gray-800 mt-4">
                Oops! Item Ini Belum Siap Disewa
            </h1>
            <p class="text-gray-600 mt-2">
                Mohon maaf, pemilik barang <strong class="font-semibold">{{ $item->name }}</strong> belum selesai mengatur informasi pembayaran.
            </p>
            <p class="text-gray-600 mt-1">
                Silakan coba lagi nanti atau hubungi pemilik barang.
            </p>
        </div>
    </div>
</body>
</html>
