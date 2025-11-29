<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Item;

class DashboardController extends Controller
{
    /**
     * Mengembalikan data statistik dashboard untuk seller yang terotentikasi.
     * Rute ini harus diproteksi oleh middleware 'auth:sanctum'.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'seller') {
             return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $totalRevenue = (float) Rental::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('payment_status', 'approved')->sum('total_price');

        $pendingOrdersCount = Rental::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('payment_status', 'waiting_confirmation')->count();

        $rentedItemsCount = Item::where('user_id', $user->id)->where('status', 'rented')->count();
        $totalItemsCount = Item::where('user_id', $user->id)->count();

        $recentRentals = Rental::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('item:id,name') // Kirim relasi item (hanya IpppD dan nama)
          ->latest()
          ->take(5)
          ->get();
        
        return response()->json([
            'statistics' => [
                'total_revenue' => $totalRevenue,
                'pending_orders' => $pendingOrdersCount,
                'rented_items' => $rentedItemsCount,
                'total_items' => $totalItemsCount,
            ],
            'recent_rentals' => $recentRentals,
        ]);
    }
}
