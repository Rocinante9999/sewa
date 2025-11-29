<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
// Hapus use Inertia\Inertia;
// Hapus use Inertia\Response;
use Illuminate\View\View; // <-- Import View

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view using Blade.
     */
    public function create(): View // <-- Ubah return type ke View
    {
        // Kembalikan view Blade 'auth.register'
        return view('auth.register'); 
    }

    /**
     * Handle an incoming registration request.
     * (Logika store() tetap sama)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Default role saat register (misal: 'seller')
            'role' => 'seller', 
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect Blade standar ke dashboard
        return redirect(route('dashboard', absolute: false));
    }
}

