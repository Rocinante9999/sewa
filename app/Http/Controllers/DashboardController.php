<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rental;
use App\Models\Item;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard Blade.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'seller') {
            $totalRevenue = Rental::whereHas('item', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('payment_status', 'approved')->sum('total_price');

            $pendingOrdersCount = Rental::whereHas('item', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('payment_status', 'waiting_confirmation')->count();

            $rentedItemsCount = Item::where('user_id', $user->id)->where('status', 'rented')->count();
            $totalItemsCount = Item::where('user_id', $user->id)->count();

            $recentRentals = Rental::whereHas('item', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('item')->latest()->take(5)->get();
            
            // Kembalikan ke view Blade
            return view('dashboard', compact(
                'totalRevenue',
                'pendingOrdersCount',
                'rentedItemsCount',
                'totalItemsCount',
                'recentRentals'
            ));
        }

        // Untuk peran lain
        return view('dashboard');
    }
}

