<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"> {{-- Updated text color for dark mode --}}
            {{ __('Tambah Item Sewaan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Updated background for dark mode --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"> 
                {{-- Updated text color for dark mode --}}
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('seller.items.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                {{-- Components likely handle dark mode automatically --}}
                                <x-input-label for="name" :value="__('Nama Item')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div>
                                <x-input-label for="description" :value="__('Deskripsi')" />
                                {{-- Added dark mode styles to textarea --}}
                                <textarea
                                    id="description"
                                    name="description"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                    required
                                >{{ old('description') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>
                            <div>
                                <x-input-label for="price_per_day" :value="__('Harga Sewa per Hari (Rp)')" />
                                <x-text-input id="price_per_day" name="price_per_day" type="number" step="1000" class="mt-1 block w-full" :value="old('price_per_day')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('price_per_day')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Simpan Item') }}</x-primary-button>
                                {{-- Updated link text color for dark mode --}}
                                <a href="{{ route('seller.items.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">{{ __('Batal') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

