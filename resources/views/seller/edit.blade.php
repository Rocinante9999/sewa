<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Toko & Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Informasi Toko Anda') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __("Update informasi profil toko dan detail rekening untuk menerima pembayaran.") }}
                        </p>
                    </header>

                    <form method="post" action="{{ route('seller.profile.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="store_name" :value="__('Nama Toko')" />
                            <x-text-input id="store_name" name="store_name" type="text" class="mt-1 block w-full" :value="old('store_name', $profile->store_name)" required autofocus autocomplete="store_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('store_name')" />
                        </div>

                        <div>
                            <x-input-label for="store_description" :value="__('Deskripsi Toko')" />
                            <textarea id="store_description" name="store_description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('store_description', $profile->store_description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('store_description')" />
                        </div>
                        
                        <hr class="my-6">

                        <h3 class="text-md font-medium text-gray-800">Detail Rekening Bank</h3>

                        <div>
                            <x-input-label for="payment_bank_name" :value="__('Nama Bank (Contoh: BCA)')" />
                            <x-text-input id="payment_bank_name" name="payment_bank_name" type="text" class="mt-1 block w-full" :value="old('payment_bank_name', $profile->payment_bank_name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('payment_bank_name')" />
                        </div>

                        <div>
                            <x-input-label for="payment_account_number" :value="__('Nomor Rekening')" />
                            <x-text-input id="payment_account_number" name="payment_account_number" type="text" class="mt-1 block w-full" :value="old('payment_account_number', $profile->payment_account_number)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('payment_account_number')" />
                        </div>

                        <div>
                            <x-input-label for="payment_account_name" :value="__('Nama Pemilik Rekening (a/n)')" />
                            <x-text-input id="payment_account_name" name="payment_account_name" type="text" class="mt-1 block w-full" :value="old('payment_account_name', $profile->payment_account_name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('payment_account_name')" />
                        </div>


                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>

                            @if (session('status') === 'profile-updated')
                                <p
                                    x-data="{ show: true }"
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-gray-600"
                                >{{ __('Tersimpan.') }}</p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>