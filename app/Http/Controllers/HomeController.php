<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderRating;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        // Track unique visitors per session
        if (!session()->has('visitor_counted')) {
            $current = (int) Setting::get('total_visitors', 0);
            Setting::set('total_visitors', $current + 1);
            session(['visitor_counted' => true]);
        }

        $products = Product::where('is_active', true)->get();

        // Ambil 3 testimoni terbaru (rating >= 4 dan memiliki ulasan)
        $testimonials = OrderRating::with('user')
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->where('rating', '>=', 4)
            ->latest()
            ->take(3)
            ->get();

        return view('home.index', compact('products', 'testimonials'));
    }
}
