<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Save admin location and shipping rate.
     */
    public function update(Request $request)
    {
        $request->validate([
            'admin_latitude' => 'required|numeric|between:-90,90',
            'admin_longitude' => 'required|numeric|between:-180,180',
            'admin_address' => 'required|string|max:500',
            'shipping_rate_per_km' => 'required|numeric|min:0',
        ]);

        Setting::set('admin_latitude', $request->admin_latitude);
        Setting::set('admin_longitude', $request->admin_longitude);
        Setting::set('admin_address', $request->admin_address);
        Setting::set('shipping_rate_per_km', $request->shipping_rate_per_km);

        return back()->with('success', 'Pengaturan pengiriman berhasil disimpan.');
    }
}
