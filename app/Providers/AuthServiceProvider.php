<?php

namespace App\Providers;

use App\Models\Rental; // <-- 1. Import model Rental
use App\Policies\RentalPolicy; // <-- 2. Import RentalPolicy
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Rental::class => RentalPolicy::class, // <-- 3. Daftarkan policy di sini
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
