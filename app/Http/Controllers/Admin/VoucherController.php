<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'code'          => 'required|string|unique:vouchers,code',
            'type'          => 'required|in:fixed,percent',
            'target'        => 'required|in:subtotal,shipping',
            'value'         => 'required|numeric|min:0',
            'min_purchase'  => 'required|numeric|min:0',
            'max_discount'  => 'nullable|numeric|min:0',
            'is_single_use' => 'nullable|boolean',
            'expires_at'    => 'nullable|date',
        ]);

        Voucher::create([
            'code'          => strtoupper($request->code),
            'type'          => $request->type,
            'target'        => $request->target,
            'value'         => $request->value,
            'min_purchase'  => $request->min_purchase,
            'max_discount'  => $request->max_discount,
            'is_single_use' => $request->has('is_single_use'),
            'expires_at'    => $request->expires_at,
            'is_active'     => true,
        ]);

        return back()->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'Voucher berhasil dihapus.');
    }

    public function toggle(Voucher $voucher)
    {
        $voucher->update(['is_active' => !$voucher->is_active]);
        return back()->with('success', 'Status voucher berhasil diubah.');
    }
}
