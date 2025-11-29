<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            
            // Kolom untuk data penyewa
            $table->string('renter_name');
            $table->string('renter_phone');
            $table->string('renter_identity_card_path'); // Kolom yang benar

            // Kolom untuk detail sewa
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_price', 15, 2);

            // Kolom untuk detail pembayaran
            $table->string('payment_method');
            $table->string('payment_proof_path');
            $table->string('payment_status')->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};