<?php

namespace App\Providers;

use App\Models\Rental;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Menggunakan View Composer untuk mengirim data notifikasi ke layout navigasi
        View::composer('layouts.navigation', function ($view) {
            $pendingOrdersCount = 0;

            // Hanya jalankan query jika pengguna adalah seller yang sedang login
            if (Auth::check() && Auth::user()->role == 'seller') {
                $user = Auth::user();
                $pendingOrdersCount = Rental::whereHas('item', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->where('payment_status', 'waiting_confirmation')->count();
            }

            // Kirim variabel $pendingOrdersCount ke view 'layouts.navigation'
            $view->with('pendingOrdersCount', $pendingOrdersCount);
        });
    }
}
