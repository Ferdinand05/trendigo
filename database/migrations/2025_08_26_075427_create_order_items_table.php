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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // relasi ke orders
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            // relasi ke products
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2); // harga per item saat order
            $table->decimal('subtotal', 12, 2); // quantity * price
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
