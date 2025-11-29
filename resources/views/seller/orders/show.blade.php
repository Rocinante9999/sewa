<x-app-layout>
    <style>
        /* CSS Modal tetap sama */
        .image-modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.8); display: none;
            justify-content: center; align-items: center; z-index: 1000; cursor: pointer;
        }
        .image-modal-content { max-width: 90%; max-height: 90%; display: block; }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Konfirmasi Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Tambahkan dark mode styles --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Kolom Kiri: Detail Pesanan & Penyewa -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Detail Pesanan</h3>
                                <div class="mt-2 space-y-2 text-sm text-gray-600 dark:text-gray-400"> {{-- Tambah dark mode text --}}
                                    <p><span class="font-semibold text-gray-900 dark:text-gray-100">Nama Barang:</span> {{ $rental->item->name }}</p>
                                    <p><span class="font-semibold text-gray-900 dark:text-gray-100">Tanggal Sewa:</span> {{ \Carbon\Carbon::parse($rental->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($rental->end_date)->format('d M Y') }}</p>
                                    <p><span class="font-semibold text-gray-900 dark:text-gray-100">Total Tagihan:</span> Rp {{ number_format($rental->total_price) }}</p>
                                    <p><span class="font-semibold text-gray-900 dark:text-gray-100">Metode Bayar:</span> {{ $rental->payment_method }}</p>
                                </div>
                            </div>
                             <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Data Penyewa</h3>
                                <div class="mt-2 space-y-2 text-sm text-gray-600 dark:text-gray-400"> {{-- Tambah dark mode text --}}
                                    <p><span class="font-semibold text-gray-900 dark:text-gray-100">Nama:</span> {{ $rental->renter_name }}</p>
                                    <p><span class="font-semibold text-gray-900 dark:text-gray-100">No. Telepon:</span> {{ $rental->renter_phone }}</p>
                                </div>
                            </div>
                             <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Foto KTP</h3>
                                {{-- PERBAIKAN: Ganti asset() dengan Storage::url() --}}
                                <img src="{{ Storage::url($rental->renter_identity_card_path) }}" alt="Foto KTP" class="enlargeable-image mt-2 rounded-lg border w-full max-w-xs hover:opacity-80 transition cursor-pointer">
                            </div>
                        </div>

                        <!-- Kolom Kanan: Bukti Bayar & Aksi -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Bukti Pembayaran</h3>
                                {{-- PERBAIKAN: Ganti asset() dengan Storage::url() --}}
                                <img src="{{ Storage::url($rental->payment_proof_path) }}" alt="Bukti Pembayaran" class="enlargeable-image mt-2 rounded-lg border w-full max-w-xs hover:opacity-80 transition cursor-pointer">
                            </div>

                            @if($rental->payment_status == 'waiting_confirmation')
                            <div class="pt-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tindakan</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Periksa bukti pembayaran. Jika sesuai, setujui pesanan. Jika tidak, tolak.</p>
                                <div class="mt-4 flex items-center gap-4">
                                    <form action="{{ route('seller.orders.approve', $rental) }}" method="POST">
                                        @csrf
                                        {{-- Tombol sudah OK --}}
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                            Setujui
                                        </button>
                                    </form>
                                     <form action="{{ route('seller.orders.reject', $rental) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak pesanan ini?');">
                                        @csrf
                                         {{-- Tombol sudah OK --}}
                                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-1E0">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                     <div class="mt-8">
                         {{-- Tambah dark mode text --}}
                        <a href="{{ route('seller.orders.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">&larr; Kembali ke Daftar Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal (tidak berubah) --}}
    <div id="imageModal" class="image-modal-overlay">
        <img id="modalImage" src="" alt="Gambar Diperbesar" class="image-modal-content">
    </div>

    {{-- Script Modal (tidak berubah) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const images = document.querySelectorAll('.enlargeable-image');

            images.forEach(image => {
                image.addEventListener('click', function() {
                    modal.style.display = 'flex';
                    modalImage.src = this.src;
                });
            });

            modal.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        });
    </script>
</x-app-layout>

