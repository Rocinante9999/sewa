<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sewa {{ $item->name }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $item->name }}</h1>
                <p class="text-gray-600 mb-4">{{ $item->description }}</p>
                <div class="flex justify-between items-center mb-6">
                    <p class="text-xl font-semibold text-gray-900">
                        Rp {{ number_format($item->price_per_day, 0, ',', '.') }} <span class="text-base font-normal text-gray-500">/ hari</span>
                    </p>
                    <span class="px-3 py-1 text-sm font-semibold text-green-800 bg-green-200 rounded-full">
                        Tersedia
                    </span>
                </div>
                
                <hr class="my-6">
                
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Formulir Penyewaan</h2>
                
                <form id="rental-form" action="{{ route('rental.process') }}" method="POST" enctype="multipart/form-data" data-price-per-day="{{ $item->price_per_day }}">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    <div class="space-y-4">
                        <div>
                            <label for="renter_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="renter_name" id="renter_name" value="{{ old('renter_name') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <x-input-error class="mt-2" :messages="$errors->get('renter_name')" />
                        </div>
                        <div>
                            <label for="renter_phone" class="block text-sm font-medium text-gray-700">Nomor Telepon (WhatsApp)</label>
                            <input type="tel" name="renter_phone" id="renter_phone" value="{{ old('renter_phone') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <x-input-error class="mt-2" :messages="$errors->get('renter_phone')" />
                        </div>
                        <div>
                            <label for="renter_identity_card_path" class="block text-sm font-medium text-gray-700">Upload Foto KTP</label>
                            <input type="file" name="renter_identity_card_path" id="renter_identity_card_path" accept="image/png, image/jpeg, image/jpg" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <x-input-error class="mt-2" :messages="$errors->get('renter_identity_card_path')" />
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai Sewa</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai Sewa</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <x-input-error class="mt-2" :messages="$errors->get('end_date')" />
                            </div>
                        </div>

                        <div class="pt-4 text-right">
                            <p class="text-gray-600">Total Biaya</p>
                            <p id="total-price" class="text-2xl font-bold text-gray-900">Rp 0</p>
                        </div>

                        <hr class="my-6">

                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Pembayaran</h2>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="" selected disabled>-- Pilih Salah Satu --</option>
                                @if($sellerProfile->payment_account_number)
                                    <option value="bank_transfer" @selected(old('payment_method') == 'bank_transfer')>Transfer Bank - {{ $sellerProfile->payment_bank_name }}</option>
                                @endif
                                @if($sellerProfile->ovo_number)
                                    <option value="ovo" @selected(old('payment_method') == 'ovo')>OVO</option>
                                @endif
                                @if($sellerProfile->gopay_number)
                                    <option value="gopay" @selected(old('payment_method') == 'gopay')>GoPay</option>
                                @endif
                                @if($sellerProfile->dana_number)
                                    <option value="dana" @selected(old('payment_method') == 'dana')>DANA</option>
                                @endif
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
                        </div>

                        <div id="payment-details-container" class="hidden mt-4 space-y-4">
                            <div id="details-bank_transfer" class="payment-detail hidden">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <p class="text-sm font-medium text-gray-800">Silakan transfer ke rekening berikut:</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <p id="bank-info" class="text-gray-700 font-mono">{{ $sellerProfile->payment_bank_name }} - {{ $sellerProfile->payment_account_number }} (a/n {{ $sellerProfile->payment_account_name }})</p>
                                        <button type="button" class="copy-btn text-sm text-indigo-600 hover:text-indigo-800" data-copy="{{ $sellerProfile->payment_account_number }}">Salin No. Rek</button>
                                    </div>
                                </div>
                            </div>
                            <div id="details-ovo" class="payment-detail hidden">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <p class="text-sm font-medium text-gray-800">Silakan transfer ke nomor OVO berikut:</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <p class="text-gray-700 font-mono">{{ $sellerProfile->ovo_number }}</p>
                                        <button type="button" class="copy-btn text-sm text-indigo-600 hover:text-indigo-800" data-copy="{{ $sellerProfile->ovo_number }}">Salin Nomor</button>
                                    </div>
                                </div>
                            </div>
                            <div id="details-gopay" class="payment-detail hidden">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <p class="text-sm font-medium text-gray-800">Silakan transfer ke nomor GoPay berikut:</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <p class="text-gray-700 font-mono">{{ $sellerProfile->gopay_number }}</p>
                                        <button type="button" class="copy-btn text-sm text-indigo-600 hover:text-indigo-800" data-copy="{{ $sellerProfile->gopay_number }}">Salin Nomor</button>
                                    </div>
                                </div>
                            </div>
                            <div id="details-dana" class="payment-detail hidden">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <p class="text-sm font-medium text-gray-800">Silakan transfer ke nomor DANA berikut:</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <p class="text-gray-700 font-mono">{{ $sellerProfile->dana_number }}</p>
                                        <button type="button" class="copy-btn text-sm text-indigo-600 hover:text-indigo-800" data-copy="{{ $sellerProfile->dana_number }}">Salin Nomor</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="payment_proof" class="block text-sm font-medium text-gray-700">Upload Bukti Pembayaran</label>
                            <input type="file" name="payment_proof" id="payment_proof" accept="image/png, image/jpeg, image/jpg" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                             <x-input-error class="mt-2" :messages="$errors->get('payment_proof')" />
                        </div>
                        
                        <div class="pt-6">
                             <button id="submit-button" type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out disabled:bg-gray-400" disabled>
                                Sewa Sekarang
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateEl = document.getElementById('start_date');
            const endDateEl = document.getElementById('end_date');
            const totalPriceEl = document.getElementById('total-price');
            const form = document.getElementById('rental-form');
            const pricePerDay = parseFloat(form.dataset.pricePerDay);
            const submitButton = document.getElementById('submit-button');
            const paymentMethodEl = document.getElementById('payment_method');
            const paymentDetailsContainer = document.getElementById('payment-details-container');
            const allPaymentDetails = document.querySelectorAll('.payment-detail');
            const copyButtons = document.querySelectorAll('.copy-btn');

            function calculatePrice() {
                const startDate = new Date(startDateEl.value);
                const endDate = new Date(endDateEl.value);
                
                let isValid = true;
                let total = 0;

                if (startDateEl.value && endDateEl.value) {
                    if (endDate < startDate) {
                        totalPriceEl.textContent = 'Tanggal tidak valid';
                        isValid = false;
                    } else {
                        const diffTime = Math.abs(endDate - startDate);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        total = diffDays * pricePerDay;
                        totalPriceEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
                    }
                } else {
                    isValid = false;
                }
                
                submitButton.disabled = !isValid;
            }

            function togglePaymentDetails() {
                const selectedMethod = paymentMethodEl.value;
                if(selectedMethod){
                    paymentDetailsContainer.classList.remove('hidden');
                    allPaymentDetails.forEach(el => {
                        if (el.id === 'details-' + selectedMethod) {
                            el.classList.remove('hidden');
                        } else {
                            el.classList.add('hidden');
                        }
                    });
                } else {
                    paymentDetailsContainer.classList.add('hidden');
                }
            }

            function copyToClipboard(event) {
                const textToCopy = event.target.dataset.copy;
                navigator.clipboard.writeText(textToCopy).then(() => {
                    const originalText = event.target.textContent;
                    event.target.textContent = 'Tersalin!';
                    setTimeout(() => {
                        event.target.textContent = originalText;
                    }, 1500);
                });
            }

            startDateEl.addEventListener('change', calculatePrice);
            endDateEl.addEventListener('change', calculatePrice);
            paymentMethodEl.addEventListener('change', togglePaymentDetails);
            copyButtons.forEach(btn => btn.addEventListener('click', copyToClipboard));
            
            calculatePrice(); 
            if(paymentMethodEl.value) {
                togglePaymentDetails();
            }
        });
    </script>
</body>
</html>

