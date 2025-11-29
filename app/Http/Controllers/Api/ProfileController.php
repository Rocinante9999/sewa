<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; 

class ProfileController extends Controller
{
    /**
     * Menampilkan profil seller yang sedang login.
     * Rute: GET /api/profile
     */
    public function show(Request $request)
    {
        // Ambil user dan muat relasi sellerProfile
        // firstOrCreate() memastikan profil ada jika user baru
        $profile = $request->user()->sellerProfile()->firstOrCreate(
            ['user_id' => $request->user()->id],
            ['store_name' => $request->user()->name] // Atur nama toko saat pertama kali dibuat
        );

        return response()->json($profile);
    }

    /**
     * Rute: POST /api/user/profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                 // Pastikan email unik, KECUALI untuk user ini sendiri
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // Update juga store_name di profil seller
        $user->sellerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            ['store_name' => $user->name]
        );

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            // Kirim data user terbaru (termasuk profil yang di-load ulang)
            'user' => $user->fresh('sellerProfile'), 
        ]);
    }

    /**
     * Mengupdate info pembayaran seller.
     * Rute: POST /api/profile/payment
     */
    public function updatePayment(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'store_description' => 'nullable|string',
            'payment_bank_name' => 'required_with:payment_account_number,payment_account_name|nullable|string|max:100',
            'payment_account_number' => 'required_with:payment_bank_name,payment_account_name|nullable|string|max:50',
            'payment_account_name' => 'required_with:payment_bank_name,payment_account_number|nullable|string|max:255',
            'ovo_number' => 'nullable|string|max:20',
            'gopay_number' => 'nullable|string|max:20',
            'dana_number' => 'nullable|string|max:20',
        ]);

        $validator->after(function ($validator) use ($request) {
            $bankFilled = !empty($request->input('payment_bank_name')) &&
                          !empty($request->input('payment_account_number')) &&
                          !empty($request->input('payment_account_name'));

            $ewalletFilled = !empty($request->input('ovo_number')) ||
                             !empty($request->input('gopay_number')) ||
                             !empty($request->input('dana_number'));

            if (!$bankFilled && !$ewalletFilled) {
                $validator->errors()->add(
                    'payment_account_number',
                    'Anda harus mengisi setidaknya satu metode pembayaran (Rekening Bank atau E-Wallet).'
                );
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $profile = SellerProfile::firstOrNew(
                ['user_id' => $user->id]
            );

            // Set data toko (nama diambil dari user)
            $profile->store_name = $user->name;
            $profile->store_description = $validated['store_description'] ?? null;

            // Update data pembayaran HANYA JIKA ADA di request
            $paymentFields = [
                'payment_bank_name', 'payment_account_number', 'payment_account_name',
                'ovo_number', 'gopay_number', 'dana_number'
            ];
            foreach ($paymentFields as $field) {
                if (array_key_exists($field, $validated)) {
                    $profile->{$field} = $validated[$field];
                }
            }
            $profile->save();
            DB::commit();

            return response()->json([
                'message' => 'Profil pembayaran diperbarui.',
                'profile' => $profile, // Kirim data profil terbaru
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan profil seller (API): ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }
}

