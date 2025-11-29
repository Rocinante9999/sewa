<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 

class OrderController extends Controller
{
    use AuthorizesRequests; 

    public function index(Request $request)
    {
        $user = $request->user();

        $pendingRentals = Rental::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('payment_status', 'waiting_confirmation')
          ->with('item:id,name') // Eager load item name
          ->latest()
          ->get();

        $historicalRentals = Rental::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('payment_status', '!=', 'waiting_confirmation')
          ->with('item:id,name') // Eager load item name
          ->latest()
          ->paginate(15); 
          
        return response()->json([
            'pendingRentals' => $pendingRentals,
            'historicalRentals' => $historicalRentals,
        ]);
    }

    /**
     * Menampilkan detail satu pesanan.
     * Rute: GET /api/orders/{rental}
     */
    public function show(Rental $rental)
    {
        $this->authorize('view', $rental);

        $rental->load('item'); 

        return response()->json($rental);
    }

    /**
     * Menyetujui pesanan sewa.
     * Rute: POST /api/orders/{rental}/approve
     */
    public function approve(Rental $rental)
    {
        // Otorisasi: Pastikan seller hanya bisa mengupdate pesanan ini
        $this->authorize('update', $rental);

        // Cek apakah pesanan masih menunggu konfirmasi
        if ($rental->payment_status !== 'waiting_confirmation') {
            return response()->json(['message' => 'Pesanan ini tidak bisa disetujui.'], 409); // 409 Conflict
        }

        // Ubah status pesanan
        $rental->payment_status = 'approved';
        $rental->save();

        // Ubah status item menjadi 'rented'
        $rental->item->status = 'rented';
        $rental->item->save();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil disetujui.',
            'rental' => $rental->fresh('item'), // Kirim data terbaru
        ]);
    }

    /**
     * Menolak pesanan sewa.
     * Rute: POST /api/orders/{rental}/reject
     */
    public function reject(Rental $rental)
    {
        // Otorisasi: Pastikan seller hanya bisa mengupdate pesanan ini
        $this->authorize('update', $rental);

        // Cek apakah pesanan masih menunggu konfirmasi
        if ($rental->payment_status !== 'waiting_confirmation') {
            return response()->json(['message' => 'Pesanan ini tidak bisa ditolak.'], 409); 
        }
        
        // Ubah status pesanan
        $rental->payment_status = 'rejected';
        $rental->save();

        // Kembalikan status item menjadi 'available'
        $rental->item->status = 'available';
        $rental->item->save();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil ditolak.',
            'rental' => $rental->fresh('item'),
        ]);
    }
}

