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
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shipping_address_id')->constrained()->onDelete('set null');
            $table->decimal('subtotal', 15, 2);
            $table->string('shipping_name');
            $table->decimal('shipping_cost', 15, 2);
            $table->string('shipping_service'); // JNE, TIKI, POS, dll
            $table->string('shipping_etd'); // Estimasi waktu pengiriman
            $table->decimal('total', 15, 2);
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_payment_type')->nullable();
            $table->string('midtrans_transaction_status')->nullable();
            $table->json('midtrans_response');
            $table->char('fraud_status');
            $table->enum('order_status', ['process', 'done', 'cancel']);
            $table->text('midtrans_response')->nullable();
            $table->string('midtrans_snap_token');
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
