<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan
     */
    public function index()
    {
        $setting = \App\Models\Setting::pluck('value', 'key')->toArray();

        // Siapkan default value untuk operational_days jika belum ada
        if (!isset($setting['operational_days'])) {
            // Default Senin-Minggu
            $setting['operational_days'] = "[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\",\"Sunday\"]";
        }
        
        if (!isset($setting['open_time'])) {
            $setting['open_time'] = "18:00";
        }

        if (!isset($setting['close_time'])) {
            $setting['close_time'] = "21:00";
        }

        return view('admin.settings.index', compact('setting'));
    }

    /**
     * Save admin location, shipping rate, and operational hours.
     */
    public function update(Request $request)
    {
        $request->validate([
            'admin_latitude' => 'required|numeric|between:-90,90',
            'admin_longitude' => 'required|numeric|between:-180,180',
            'admin_address' => 'required|string|max:500',
            'shipping_rate_per_km' => 'required|numeric|min:0',
            'min_distance_km' => 'nullable|numeric|min:0',
            'max_distance_km' => 'nullable|numeric|min:0',
            'operational_days' => 'required|array|min:1',
            'operational_days.*' => 'string',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
        ]);

        Setting::set('admin_latitude', $request->admin_latitude);
        Setting::set('admin_longitude', $request->admin_longitude);
        Setting::set('admin_address', $request->admin_address);
        Setting::set('shipping_rate_per_km', $request->shipping_rate_per_km);
        Setting::set('min_distance_km', $request->min_distance_km ?: 0);
        Setting::set('max_distance_km', $request->max_distance_km ?: 0);
        
        // Simpan operasional sebagai JSON
        Setting::set('operational_days', json_encode($request->operational_days));
        Setting::set('open_time', $request->open_time);
        Setting::set('close_time', $request->close_time);

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
