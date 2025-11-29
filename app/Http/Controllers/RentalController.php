<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Rental;
use App\Models\SellerProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class RentalController extends Controller
{
    /**
     * Menampilkan form sewa atau halaman error yang sesuai menggunakan Blade.
     */
    public function showRentalForm(Item $item)
    {
        // Kondisi 1: Item sedang disewa
        if ($item->status !== 'available') {
            $rental = Rental::where('item_id', $item->id)
                            ->where('payment_status', 'approved')
                            ->latest('created_at')
                            ->first();

            return response()->view('rental.rented', compact('item', 'rental'), 404);
        }

        $sellerProfile = $item->user->sellerProfile;

        if (!$sellerProfile || !$this->hasPaymentMethod($sellerProfile)) {
            return response()->view('rental.error', compact('item'), 503);
        }

        return view('rental.form', compact('item', 'sellerProfile'));
    }
    
    private function hasPaymentMethod(SellerProfile $profile): bool
    {
        $bankFilled = $profile->payment_bank_name && $profile->payment_account_number && $profile->payment_account_name;
        $ewalletFilled = $profile->ovo_number || $profile->gopay_number || $profile->dana_number;
        return $bankFilled || $ewalletFilled;
    }

    public function processRental(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'renter_name' => 'required|string|max:255',
            'renter_phone' => 'required|string|max:20',
            'renter_identity_card_path' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'payment_method' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        $identityCardPath = $request->file('renter_identity_card_path')->store('identity_cards', 'public');

        $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $durationInDays = $startDate->diffInDays($endDate) + 1;
        $totalPrice = $durationInDays * $item->price_per_day;

        $rental = Rental::create([
            'item_id' => $item->id,
            'renter_name' => $validated['renter_name'],
            'renter_phone' => $validated['renter_phone'],
            'renter_identity_card_path' => $identityCardPath,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $totalPrice,
            'payment_method' => $validated['payment_method'],
            'payment_proof_path' => $paymentProofPath, 
            'payment_status' => 'waiting_confirmation',
        ]);
        
        // Redirect ke halaman sukses Blade
        return redirect()->route('rental.success', $rental);
    }

    /**
     * Menampilkan halaman sukses setelah submit form menggunakan Blade.
     */
    public function showSuccess(Rental $rental)
    {
        return view('rental.success', compact('rental'));
    }
}

