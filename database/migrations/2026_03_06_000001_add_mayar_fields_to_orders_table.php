<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_link')->nullable()->after('snap_token');
            $table->string('mayar_id')->nullable()->after('payment_link'); // ID transaksi dari Mayar
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_link', 'mayar_id']);
        });
    }
};
