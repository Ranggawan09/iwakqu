<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'admin_latitude' => '-7.5113618',
            'admin_longitude' => '112.2585315',
            'admin_address' => 'Jalan Tanjunggunung - Dukuhklopo, Kali Kejambon, Jombang, Jawa Timur, Jawa, 61481, Indonesia',
            'shipping_rate_per_km' => '3000',
            'min_distance_km' => '3',
            'max_distance_km' => '10',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrInsert(
            ['key' => $key],
            ['value' => $value]
            );
        }
    }
}
