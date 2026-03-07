<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderRating;

class HomeController extends Controller
{
    public function index()
    {
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
