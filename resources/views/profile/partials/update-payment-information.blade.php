@if(auth()->user()->role == 'seller')
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informasi Toko & Pembayaran') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Lengkapi profil toko dan metode pembayaran Anda. Wajib mengisi Nama Toko dan minimal satu metode pembayaran.") }}
        </p>
    </header>

    <form method="post" action="{{ route('seller.profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Kita akan mengambil data profil seller dari relasi user --}}
        @php
            $profile = $user->sellerProfile()->firstOrCreate(['user_id' => $user->id]);
        @endphp

        {{-- PERBAIKAN: Input Nama dan Deskripsi Toko dipindahkan ke sini --}}
        <div>
            <x-input-label for="store_name" :value="__('Nama Toko')" />
            <x-text-input id="store_name" name="store_name" type="text" class="mt-1 block w-full" :value="old('store_name', $profile->store_name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('store_name')" />
        </div>

        <div>
            <x-input-label for="store_description" :value="__('Deskripsi Toko')" />
            <textarea id="store_description" name="store_description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('store_description', $profile->store_description) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('store_description')" />
        </div>
        
        <hr class="my-6 border-gray-300 dark:border-gray-700">

        <h3 class="text-md font-medium text-gray-800 dark:text-gray-200">Detail Rekening Bank (Opsional)</h3>

        <!-- Nama Bank -->
        <div>
            <x-input-label for="payment_bank_name" :value="__('Nama Bank (Contoh: BCA)')" />
            <x-text-input id="payment_bank_name" name="payment_bank_name" type="text" class="mt-1 block w-full" :value="old('payment_bank_name', $profile->payment_bank_name)" />
            <x-input-error class="mt-2" :messages="$errors->get('payment_bank_name')" />
        </div>

        <!-- Nomor Rekening -->
        <div>
            <x-input-label for="payment_account_number" :value="__('Nomor Rekening')" />
            <x-text-input id="payment_account_number" name="payment_account_number" type="text" class="mt-1 block w-full" :value="old('payment_account_number', $profile->payment_account_number)" />
            {{-- Menampilkan error validasi kustom di sini --}}
            <x-input-error class="mt-2" :messages="$errors->get('payment_account_number')" />
        </div>

        <!-- Nama Pemilik Rekening -->
        <div>
            <x-input-label for="payment_account_name" :value="__('Nama Pemilik Rekening (a/n)')" />
            <x-text-input id="payment_account_name" name="payment_account_name" type="text" class="mt-1 block w-full" :value="old('payment_account_name', $profile->payment_account_name)" />
            <x-input-error class="mt-2" :messages="$errors->get('payment_account_name')" />
        </div>

        <hr class="my-6 border-gray-300 dark:border-gray-700">

        <h3 class="text-md font-medium text-gray-800 dark:text-gray-200">Detail E-Wallet (Opsional)</h3>
        
        <!-- Nomor OVO -->
        <div class="mt-4">
            <x-input-label for="ovo_number" :value="__('Nomor OVO')" />
            <x-text-input id="ovo_number" name="ovo_number" type="text" class="mt-1 block w-full" :value="old('ovo_number', $profile->ovo_number)" />
            <x-input-error class="mt-2" :messages="$errors->get('ovo_number')" />
        </div>

        <!-- Nomor GoPay -->
        <div>
            <x-input-label for="gopay_number" :value="__('Nomor GoPay')" />
            <x-text-input id="gopay_number" name="gopay_number" type="text" class="mt-1 block w-full" :value="old('gopay_number', $profile->gopay_number)" />
            <x-input-error class="mt-2" :messages="$errors->get('gopay_number')" />
        </div>

        <!-- Nomor DANA -->
        <div>
            <x-input-label for="dana_number" :value="__('Nomor DANA')" />
            <x-text-input id="dana_number" name="dana_number" type="text" class="mt-1 block w-full" :value="old('dana_number', $profile->dana_number)" />
            <x-input-error class="mt-2" :messages="$errors->get('dana_number')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan Informasi Pembayaran') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Tersimpan.') }}</p>
            @endif
        </div>
    </form>
</section>
@endif

