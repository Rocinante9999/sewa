<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sewa Instan - Rental Apapun, Kapanpun</title>
    <link rel="icon" href="{{ asset('logo.webp') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

    <div x-data="{ open: false }">
        <header class="absolute top-0 left-0 right-0 z-10 p-4 sm:p-6">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <a href="/" class="flex items-center space-x-2">
                        <img src="/logo.webp" alt="Sewa Instan" class="h-10 w-10" />
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Sewa Instan</span>
                </a>
                
                <nav class="hidden md:flex items-center space-x-2">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Daftar Jadi Seller</a>
                            @endif
                        @endauth
                    @endif
                </nav>

                <!-- Tombol Hamburger (Mobile) -->
                <div class="md:hidden">
                    <button @click="open = !open" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Menu Mobile -->
        <div x-show="open" @click.away="open = false" x-transition class="fixed top-0 left-0 right-0 z-20 bg-white dark:bg-gray-800 shadow-lg md:hidden">
            <div class="p-4 space-y-4">
                 @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block w-full text-left rounded-md px-4 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-left rounded-md px-4 py-2 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full text-left rounded-md px-4 py-2 text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">Daftar Jadi Seller</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>

        <main>
            <section class="relative min-h-screen flex items-center justify-center pt-24 pb-12 bg-white dark:bg-gray-800">
                <div class="max-w-4xl mx-auto text-center px-4 sm:px-6">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                        Sewa Apapun, Semudah <span class="text-indigo-600">Scan QR</span>
                    </h1>
                    <p class="mt-6 max-w-2xl mx-auto text-lg text-gray-600 dark:text-gray-300">
                        Platform rental modern yang menghubungkan pemilik barang dengan penyewa secara instan. Tanpa perlu registrasi, tanpa ribet.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('register') }}" class="inline-block w-full sm:w-auto px-8 py-3 bg-indigo-600 text-white font-bold rounded-lg text-center hover:bg-indigo-700 transition duration-150">
                            Mulai Jadi Seller
                        </a>
                    </div>
                </div>
            </section>

            <section class="py-16 lg:py-24">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Bagaimana Cara Kerjanya?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                        Hanya dalam 3 langkah mudah, barang Anda siap disewa.
                    </p>
                    <div class="mt-12 grid gap-8 md:grid-cols-3">
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 mx-auto">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            </div>
                            <h3 class="mt-5 text-lg font-medium text-gray-900 dark:text-white">Daftarkan Barang</h3>
                            <p class="mt-2 text-base text-gray-500 dark:text-gray-400">Sebagai seller, tambahkan detail barang Anda dan sistem akan otomatis membuatkan QR code unik.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 mx-auto">
                               <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <h3 class="mt-5 text-lg font-medium text-gray-900 dark:text-white">Penyewa Scan & Bayar</h3>
                            <p class="mt-2 text-base text-gray-500 dark:text-gray-400">Penyewa cukup scan QR code, mengisi data diri, dan melakukan pembayaran secara langsung di tempat.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
                            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 mx-auto">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="mt-5 text-lg font-medium text-gray-900 dark:text-white">Konfirmasi & Selesai</h3>
                            <p class="mt-2 text-base text-gray-500 dark:text-gray-400">Anda menerima notifikasi pesanan, melakukan konfirmasi, dan barang pun siap diserahkan kepada penyewa.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white dark:bg-gray-800 py-16 lg:py-24">
                <div class="max-w-3xl mx-auto text-center px-4 sm:px-6">
                    <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Ubah Barang Jadi Penghasilan
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                        Bergabunglah sekarang dan mulailah menyewakan barang Anda kepada ribuan calon penyewa di sekitar Anda.
                    </p>
                    <div class="mt-8">
                        <a href="{{ route('register') }}" class="inline-block w-full sm:w-auto px-8 py-3 bg-indigo-600 text-white font-bold rounded-lg text-center hover:bg-indigo-700 transition duration-150">
                            Daftar Gratis
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 dark:bg-black">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Sewa Instan') }}. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</body>
</html>

