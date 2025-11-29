<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Item Sedang Disewa</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8 flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>

            <h1 class="text-2xl font-bold text-gray-800 mt-4">
                Item Sedang Disewa
            </h1>
            <p class="text-gray-600 mt-2">
                Mohon maaf, <strong class="font-semibold">{{ $item->name }}</strong> saat ini sedang tidak tersedia.
            </p>
            
            @if(isset($rental))
            <div class="mt-4 bg-gray-50 rounded-lg p-4 text-left border border-gray-200">
                <p class="text-sm text-gray-800">
                    <span class="font-semibold">Disewa oleh:</span> {{ $rental->renter_name }}
                </p>
                <p class="text-sm text-gray-800 mt-1">
                    <span class="font-semibold">Akan tersedia kembali setelah:</span> {{ \Carbon\Carbon::parse($rental->end_date)->format('d F Y') }}
                </p>
            </div>
            @endif

        </div>
    </div>
</body>
</html>
