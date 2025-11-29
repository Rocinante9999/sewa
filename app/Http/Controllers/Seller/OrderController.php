<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// Hapus use Inertia\Inertia; // Tidak lagi digunakan

class OrderController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan halaman utama pesanan menggunakan Blade.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Ambil pesanan yang menunggu konfirmasi
        $pendingRentals = Rental::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('payment_status', 'waiting_confirmation')
          ->with('item:id,name') // Tetap eager load untuk view
          ->latest()
          ->get();

        // Ambil riwayat pesanan (semua selain yang menunggu konfirmasi)
        $historicalRentals = Rental::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('payment_status', '!=', 'waiting_confirmation')
          ->with('item:id,name') // Tetap eager load untuk view
          ->latest()
          ->paginate(10); // Kirim objek paginator ke view Blade

        // Kembalikan view Blade 'seller.orders.index'
        return view('seller.orders.index', compact('pendingRentals', 'historicalRentals'));
    }

    /**
     * Menampilkan detail satu pesanan untuk konfirmasi menggunakan Blade.
     */
    public function show(Rental $rental)
    {
        // Otorisasi tetap sama
        $this->authorize('view', $rental);

        // Eager load relasi item (opsional, tergantung kebutuhan view Blade)
        // $rental->load('item'); 

        // Kembalikan view Blade 'seller.orders.show'
        return view('seller.orders.show', compact('rental'));
    }

    /**
     * Menyetujui pesanan sewa.
     * (Logika tetap sama)
     */
    public function approve(Rental $rental)
    {
        $this->authorize('update', $rental);
        $rental->payment_status = 'approved';
        $rental->save();
        $rental->item->status = 'rented';
        $rental->item->save();
        return redirect()->route('seller.orders.index')->with('success', 'Pesanan berhasil disetujui.');
    }

    /**
     * Menolak pesanan sewa.
     * (Logika tetap sama)
     */
    public function reject(Rental $rental)
    {
        $this->authorize('update', $rental);
        $rental->payment_status = 'rejected';
        $rental->save();
        $rental->item->status = 'available';
        $rental->item->save();
        return redirect()->route('seller.orders.index')->with('success', 'Pesanan berhasil ditolak.');
    }
}

