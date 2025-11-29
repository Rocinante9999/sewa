<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
// use Inertia\Inertia; // Tidak perlu render dari sini

class ProfileController extends Controller
{
    /**
     * Menampilkan form untuk edit profil seller.
     * (Metode ini tidak lagi dipanggil langsung untuk Inertia)
     */
    public function edit(Request $request)
    {
         // Logika ini mungkin tidak relevan lagi jika form selalu dimuat
         // oleh ProfileController utama.
        $profile = $request->user()->sellerProfile()->firstOrCreate(
            ['user_id' => $request->user()->id]
        );

        // Seharusnya tidak sampai sini jika menggunakan Inertia dari ProfileController utama
        return view('profile.edit', compact('profile')); // <-- Biarkan view() untuk fallback
    }

    /**
     * Mengupdate profil seller dengan validasi kustom.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'store_name' diambil otomatis dari nama user
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

        $validated = $validator->validate();

        try {
            DB::beginTransaction();

            // --- PERBAIKAN: PENYIMPANAN LEBIH HATI-HATI ---
            // 1. Cari atau buat instance baru
            $profile = SellerProfile::firstOrNew(
                ['user_id' => $request->user()->id]
            );

            // 2. Selalu update data toko
            $profile->store_name = $request->user()->name;
            // Gunakan input() langsung untuk nullable agar null terkirim jika kosong
            $profile->store_description = $request->input('store_description'); 

            // 3. Update data pembayaran hanya jika ada di data *validated*
            //    Ini memastikan hanya field yang valid dan dikirim yang diupdate
            $paymentFields = [
                'payment_bank_name', 'payment_account_number', 'payment_account_name',
                'ovo_number', 'gopay_number', 'dana_number'
            ];

            foreach ($paymentFields as $field) {
                // array_key_exists mengecek apakah kunci ada di $validated,
                // bahkan jika nilainya null (karena nullable validation).
                if (array_key_exists($field, $validated)) {
                    // Update field di model dengan nilai dari $validated
                    $profile->{$field} = $validated[$field];
                }
                // Jika kunci tidak ada di $validated, field tersebut tidak akan diubah
                // sehingga nilai lama di database tetap terjaga.
            }

            // 4. Simpan ke database
            $profile->save();
            // --- BATAS PERBAIKAN ---

            DB::commit();

            // Memastikan data user yang dikirim Inertia selanjutnya adalah yang terbaru.
            $request->user()->load('sellerProfile');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan profil seller: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }

        // Redirect Blade standar, kembali ke halaman profil
        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }
}

