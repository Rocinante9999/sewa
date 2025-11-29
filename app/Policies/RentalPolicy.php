<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RentalPolicy
{
    /**
     * Determine whether the user can view the model.
     * Aturan: Pengguna bisa melihat detail pesanan jika ID pengguna sama dengan ID pemilik barang dari pesanan tersebut.
     */
    public function view(User $user, Rental $rental): bool
    {
        return $user->id === $rental->item->user_id;
    }

    /**
     * Determine whether the user can update the model.
     * Aturan: Pengguna bisa menyetujui/menolak pesanan jika ID pengguna sama dengan ID pemilik barang.
     */
    public function update(User $user, Rental $rental): bool
    {
        return $user->id === $rental->item->user_id;
    }
}
