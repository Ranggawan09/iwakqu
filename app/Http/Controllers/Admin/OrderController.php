<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'orderItems.product')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('orderItems.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:menunggu_pembayaran,dibayar,diproses,dikirim,selesai,dibatalkan',
        ]);

        if ($order->status !== $request->status) {
            $order->update(['status' => $request->status]);

            if (in_array($request->status, ['diproses', 'dikirim', 'selesai', 'dibatalkan'])) {
                if ($order->user) {
                    $order->user->notify(new \App\Notifications\OrderStatusUpdatedNotification($order, $request->status));
                }
            }
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}
