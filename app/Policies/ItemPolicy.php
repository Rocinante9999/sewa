<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ItemPolicy
{
    /**
     * Determine whether the user can view any models.
     * Logika ini ditangani oleh controller (query berdasarkan user),
     * jadi kita izinkan di sini.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'seller';
    }

    /**
     * Determine whether the user can view the model.
     * Hanya user yang memiliki item yang boleh melihatnya.
     */
    public function view(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    /**
     * Determine whether the user can create models.
     * Hanya seller yang boleh membuat item baru.
     */
    public function create(User $user): bool
    {
        return $user->role === 'seller';
    }

    /**
     * Determine whether the user can update the model.
     * Hanya user yang memiliki item yang boleh mengupdatenya.
     */
    public function update(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * Hanya user yang memiliki item yang boleh menghapusnya.
     */
    public function delete(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Item $item): bool
    {
        // Biarkan false jika Anda tidak menggunakan soft deletes
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Item $item): bool
    {
        // Biarkan false jika Anda tidak menggunakan soft deletes
        return false;
    }
}
