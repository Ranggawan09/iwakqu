<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_cost', 12, 0)->default(0)->after('total_price');
            $table->decimal('distance_km', 8, 2)->nullable()->after('shipping_cost');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_cost', 'distance_km']);
        });
    }
};
