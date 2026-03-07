<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        $total = $carts->sum(fn($c) => $c->subtotal);

        return view('customer.cart', compact('carts', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi!');
        }

        $cart = Cart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            $newQty = $cart->quantity + $request->quantity;
            if ($product->stock < $newQty) {
                return back()->with('error', 'Stok tidak mencukupi!');
            }
            $cart->update(['quantity' => $newQty]);
        }
        else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        // Jika tombol "Pesan Sekarang", langsung arahkan ke checkout
        if ($request->boolean('redirect_checkout')) {
            return redirect()->route('checkout.index')->with('success', $product->name . ' berhasil ditambahkan ke keranjang!');
        }

        return back()->with('success', $product->name . ' berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['quantity' => 'required|integer|min:1']);

        if ($cart->product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi!');
        }

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Jumlah berhasil diperbarui.');
    }

    public function remove(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();
        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }
}
