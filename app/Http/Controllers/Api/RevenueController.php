<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RevenueController extends Controller
{
    /**
     * Menampilkan halaman laporan pendapatan sebagai JSON.
     * Rute: GET /api/revenue
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Pastikan user adalah seller
        if ($user->role !== 'seller') {
             return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // Query dasar untuk mengambil rental yang sudah disetujui milik user
        $baseQuery = Rental::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('payment_status', 'approved');

        // Kalkulasi pendapatan berdasarkan periode
        $dailyRevenue = (float) (clone $baseQuery)->whereDate('created_at', Carbon::today())->sum('total_price');
        $weeklyRevenue = (float) (clone $baseQuery)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_price');
        $monthlyRevenue = (float) (clone $baseQuery)->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total_price');
        $yearlyRevenue = (float) (clone $baseQuery)->whereYear('created_at', Carbon::now()->year)->sum('total_price');

        // Mengambil daftar transaksi terakhir untuk ditampilkan (dengan paginasi)
        $recentTransactions = (clone $baseQuery)
            ->with('item:id,name') // Eager load item name
            ->latest()
            ->paginate(15); // Paginasi akan diformat otomatis ke JSON

        // Kembalikan semua data dalam format JSON
        return response()->json([
            'statistics' => [
                'daily' => $dailyRevenue,
                'weekly' => $weeklyRevenue,
                'monthly' => $monthlyRevenue,
                'yearly' => $yearlyRevenue,
            ],
            'recent_transactions' => $recentTransactions, // Objek paginator
        ]);
    }
}
