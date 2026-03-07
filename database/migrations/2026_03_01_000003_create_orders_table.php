<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->text('address');
            $table->string('phone', 20);
            $table->decimal('total_price', 12, 0);
            $table->enum('status', [
                'menunggu_pembayaran',
                'dibayar',
                'diproses',
                'dikirim',
                'selesai',
                'dibatalkan'
            ])->default('menunggu_pembayaran');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('snap_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
