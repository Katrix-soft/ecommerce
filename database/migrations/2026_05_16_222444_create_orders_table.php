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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Snapshot de la dirección de envío en JSON para histórico
            $table->json('shipping_address');
            
            $table->string('payment_method'); // credit_card, bank_transfer, cash
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
