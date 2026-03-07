<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('status', ['dibayar', 'diproses', 'selesai'])->sum('total_price');
        $recentOrders = Order::with('user', 'orderItems.product')->latest()->take(5)->get();

        $setting = [
            'admin_latitude' => Setting::get('admin_latitude'),
            'admin_longitude' => Setting::get('admin_longitude'),
            'admin_address' => Setting::get('admin_address'),
            'shipping_rate_per_km' => Setting::get('shipping_rate_per_km', 5000),
        ];

        return view('admin.dashboard', compact(
            'totalUsers', 'totalOrders', 'totalRevenue', 'recentOrders', 'setting'
        ));
    }
}
