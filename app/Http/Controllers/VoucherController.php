<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Cart;
use App\Models\Setting;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Terapkan voucher via AJAX di halaman checkout.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper($request->code);
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Kode voucher tidak valid.'
            ], 422);
        }

        // Hitung subtotal keranjang user saat ini
        $carts = Cart::with('product')->where('user_id', auth()->id())->get();
        $subtotal = $carts->sum(fn($c) => $c->subtotal);

        // Validasi voucher
        if (!$voucher->isValid($subtotal, auth()->user())) {
            $msg = 'Voucher tidak dapat digunakan.';
            
            if (!$voucher->is_active) $msg = 'Voucher sudah tidak aktif.';
            elseif ($voucher->expires_at && $voucher->expires_at->isPast()) $msg = 'Voucher sudah kadaluarsa.';
            elseif ($subtotal < $voucher->min_purchase) $msg = 'Minimal belanja Rp ' . number_format($voucher->min_purchase, 0, ',', '.') . ' untuk menggunakan voucher ini.';
            elseif ($voucher->is_single_use) $msg = 'Anda sudah pernah menggunakan voucher ini.';

            return response()->json([
                'success' => false,
                'message' => $msg
            ], 422);
        }

        // Hitung diskon
        // Target: subtotal atau shipping
        // Jika shipping, diskon dihitung nanti di JS/Backend berdasarkan ongkir riil
        // Namun kita kirimkan info vouchernya
        
        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan!',
            'voucher' => [
                'code'   => $voucher->code,
                'type'   => $voucher->type,
                'target' => $voucher->target,
                'value'  => $voucher->value,
                'max_discount' => $voucher->max_discount,
            ]
        ]);
    }
}
