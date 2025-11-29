<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Carbon\Carbon;
// Hapus use Inertia\Inertia; // Tidak lagi digunakan

class RevenueController extends Controller
{
    /**
     * Menampilkan halaman laporan pendapatan menggunakan Blade.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $baseQuery = Rental::whereHas('item', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('payment_status', 'approved');

        // Kalkulasi pendapatan (logika tetap sama)
        $dailyRevenue = (float) (clone $baseQuery)->whereDate('created_at', Carbon::today())->sum('total_price');
        $weeklyRevenue = (float) (clone $baseQuery)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_price');
        $monthlyRevenue = (float) (clone $baseQuery)->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total_price');
        $yearlyRevenue = (float) (clone $baseQuery)->whereYear('created_at', Carbon::now()->year)->sum('total_price');

        // Mengambil daftar transaksi terakhir dengan paginasi
        $recentTransactions = (clone $baseQuery)->with('item:id,name') // Tetap eager load untuk view
                                                ->latest()
                                                ->paginate(15); // Kirim objek paginator ke view

        // Kembalikan view Blade 'seller.revenue.index'
        return view('seller.revenue.index', compact(
            'dailyRevenue',
            'weeklyRevenue',
            'monthlyRevenue',
            'yearlyRevenue',
            'recentTransactions' // Kirim objek paginator
        ));
    }
}

