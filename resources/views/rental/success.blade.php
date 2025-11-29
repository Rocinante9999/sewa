<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale-1">
    <title>Pesanan Berhasil Dibuat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8 flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h1 class="text-2xl font-bold text-gray-800 mt-4">Terima Kasih!</h1>
            <p class="text-gray-600 mt-2">Pesanan Anda untuk menyewa <strong>{{ $rental->item->name }}</strong> telah kami terima dan sedang menunggu konfirmasi dari pemilik barang.</p>
            <p class="text-gray-600 mt-1">Anda akan dihubungi lebih lanjut jika pesanan sudah disetujui.</p>
            <div class="mt-6">
                <a href="/" class="text-indigo-600 hover:text-indigo-800 font-semibold">Kembali ke Halaman Utama</a>
            </div>
        </div>
    </div>
</body>
</html>