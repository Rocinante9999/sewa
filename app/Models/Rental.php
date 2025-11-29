<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'renter_name',
        'renter_phone',
        'renter_identity_card_path',
        'start_date',
        'end_date',
        'total_price',
        'payment_status',
        'payment_method',
        'payment_proof_path',
    ];

    /**
     * Get the item that was rented.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
