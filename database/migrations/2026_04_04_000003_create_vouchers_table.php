<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percent']);
            $table->enum('target', ['subtotal', 'shipping']);
            $table->decimal('value', 12, 0);
            $table->decimal('min_purchase', 12, 0)->default(0);
            $table->decimal('max_discount', 12, 0)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_single_use')->default(false);
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
