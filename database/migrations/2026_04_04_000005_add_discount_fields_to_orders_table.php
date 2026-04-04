<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('voucher_code')->nullable()->after('distance_km');
            $table->decimal('voucher_discount', 12, 0)->default(0)->after('voucher_code');
            $table->decimal('global_discount_amount', 12, 0)->default(0)->after('voucher_discount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['voucher_code', 'voucher_discount', 'global_discount_amount']);
        });
    }
};
