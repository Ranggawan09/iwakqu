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

        if (!isset($setting['open_time'])) {
            $setting['open_time'] = "17:00";
        }

        if (!isset($setting['close_time'])) {
            $setting['close_time'] = "21:00";
        }

        // Global Discount defaults
        if (!isset($setting['global_discount_active'])) {
             $setting['global_discount_active'] = "0";
        }
        if (!isset($setting['global_discount_type'])) {
             $setting['global_discount_type'] = "percent";
        }
        if (!isset($setting['global_discount_target'])) {
             $setting['global_discount_target'] = "subtotal";
        }
        if (!isset($setting['global_discount_value'])) {
             $setting['global_discount_value'] = "0";
        }

        $vouchers = \App\Models\Voucher::latest()->get();

        return view('admin.settings.index', compact('setting', 'vouchers'));
    }

    /**
     * Save admin location, shipping rate, and operational hours.
     */
    public function update(Request $request)
    {
        // Karena ada beberapa form di satu halaman, kita validasi secara dinamis
        // atau pastikan semua field minimal 'nullable'.
        $request->validate([
            'admin_latitude' => 'nullable|numeric|between:-90,90',
            'admin_longitude' => 'nullable|numeric|between:-180,180',
            'admin_address' => 'nullable|string|max:500',
            'shipping_rate_per_km' => 'nullable|numeric|min:0',
            'min_distance_km' => 'nullable|numeric|min:0',
            'max_distance_km' => 'nullable|numeric|min:0',
            'operational_days' => 'nullable|array|min:1',
            'operational_days.*' => 'string',
            'open_time' => 'nullable|date_format:H:i',
            'close_time' => 'nullable|date_format:H:i',
            'global_discount_active' => 'nullable',
            'global_discount_type'   => 'nullable|in:fixed,percent',
            'global_discount_target' => 'nullable|in:subtotal,shipping',
            'global_discount_value'  => 'nullable|numeric|min:0',
        ]);

        if ($request->has('admin_latitude')) {
            Setting::set('admin_latitude', $request->admin_latitude);
        }
        if ($request->has('admin_longitude')) {
            Setting::set('admin_longitude', $request->admin_longitude);
        }
        if ($request->has('admin_address')) {
            Setting::set('admin_address', $request->admin_address);
        }
        if ($request->has('shipping_rate_per_km')) {
            Setting::set('shipping_rate_per_km', $request->shipping_rate_per_km);
        }
        if ($request->has('min_distance_km')) {
            Setting::set('min_distance_km', $request->min_distance_km ?: 0);
        }
        if ($request->has('max_distance_km')) {
            Setting::set('max_distance_km', $request->max_distance_km ?: 0);
        }
        
        if ($request->has('operational_days')) {
            Setting::set('operational_days', json_encode($request->operational_days));
        }
        if ($request->has('open_time')) {
            Setting::set('open_time', $request->open_time);
        }
        if ($request->has('close_time')) {
            Setting::set('close_time', $request->close_time);
        }

        if ($request->has('global_discount_type')) {
            Setting::set('global_discount_active', $request->has('global_discount_active') ? "1" : "0");
            Setting::set('global_discount_type',   $request->global_discount_type);
            Setting::set('global_discount_target', $request->global_discount_target);
            Setting::set('global_discount_value',  $request->global_discount_value);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
