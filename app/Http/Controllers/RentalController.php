<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Rental;
use App\Models\SellerProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    /**
     * Fungsi upload ke Litterbox (Catbox temporary upload)
     */
    private function uploadToLitterbox($file)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://litterbox.catbox.moe/resources/internals/api.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                "reqtype"       => "fileupload",
                "time"          => "72h", // bisa 1h, 12h, 24h, 72h
                "fileToUpload"  => curl_file_create(
                    $file->getRealPath(),
                    $file->getMimeType(),
                    $file->getClientOriginalName()
                ),
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response; // hasil berupa URL string
    }

    /**
     * Menampilkan form sewa atau error
     */
    public function showRentalForm(Item $item)
    {
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
        $bankFilled = $profile->payment_bank_name &&
                      $profile->payment_account_number &&
                      $profile->payment_account_name;

        $ewalletFilled = $profile->ovo_number ||
                         $profile->gopay_number ||
                         $profile->dana_number;

        return $bankFilled || $ewalletFilled;
    }

    /**
     * Proses rental + upload gambar ke Litterbox
     */
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

        // ⬇️ Upload ke Litterbox
        $identityCardUrl = $this->uploadToLitterbox($request->file('renter_identity_card_path'));
        $paymentProofUrl = $this->uploadToLitterbox($request->file('payment_proof'));

        // Hitung total harga
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $durationInDays = $startDate->diffInDays($endDate) + 1;
        $totalPrice = $durationInDays * $item->price_per_day;

        // Simpan rental ke database
        $rental = Rental::create([
            'item_id' => $item->id,
            'renter_name' => $validated['renter_name'],
            'renter_phone' => $validated['renter_phone'],

            // ⬇️ SIMPAN URL, bukan path storage
            'renter_identity_card_path' => $identityCardUrl,
            'payment_proof_path' => $paymentProofUrl,

            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $totalPrice,
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'waiting_confirmation',
        ]);

        return redirect()->route('rental.success', $rental);
    }

    /**
     * Halaman sukses
     */
    public function showSuccess(Rental $rental)
    {
        return view('rental.success', compact('rental'));
    }
}
