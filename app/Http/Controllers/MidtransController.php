<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Transaction;

class MidtransController extends Controller
{
    /**
     * Webhook callback dari server Midtrans.
     * Dipanggil otomatis oleh Midtrans (tidak bisa diakses localhost tanpa tunnel).
     */
    public function callback(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash('sha512',
            $request->order_id .
            $request->status_code .
            $request->gross_amount .
            $serverKey
        );

        if ($hashed !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $this->updateOrderStatus(
            $request->order_id,
            $request->transaction_status,
            $request->payment_type,
            $request->transaction_id
        );

        return response()->json(['message' => 'OK']);
    }

    /**
     * Dipanggil dari frontend (Snap.js onSuccess/onPending) untuk update
     * status order secara langsung — solusi untuk development lokal.
     */
    public function finish(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Cari midtrans order_id dari snap_token — kita simpan dengan format LELE-{id}-{time}
        // Query langsung ke Midtrans untuk cek status transaksi
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            // Midtrans order_id kita format LELE-{order->id}-{timestamp}
            // Kita cek semua kemungkinan dengan prefix
            $status = Transaction::status('LELE-' . $order->id . '-' . $request->midtrans_order_id_suffix ?? '');

            $this->updateOrderStatus(
                $status->order_id,
                $status->transaction_status,
                $status->payment_type,
                $status->transaction_id,
                $order
            );
        }
        catch (\Exception $e) {
            // Jika query gagal, gunakan data dari result Snap.js yang dikirim client
            if ($request->transaction_status && in_array($request->transaction_status, ['capture', 'settlement'])) {
                $order->update([
                    'status' => 'dibayar',
                    'payment_method' => $request->payment_type,
                    'transaction_id' => $request->transaction_id,
                ]);
            }
        }

        return response()->json(['message' => 'OK', 'status' => $order->fresh()->status]);
    }

    /**
     * Helper: update order berdasarkan transaction_status Midtrans.
     */
    private function updateOrderStatus(string $midtransOrderId, string $transactionStatus, ?string $paymentType, ?string $transactionId, ?Order $order = null): void
    {
        if (!$order) {
            // Extract order ID dari format "LELE-5-1234567890"
            $parts = explode('-', $midtransOrderId);
            $orderId = $parts[1] ?? null;
            if (!$orderId)
                return;
            $order = Order::find($orderId);
            if (!$order)
                return;
        }

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $order->update([
                'status' => 'dibayar',
                'payment_method' => $paymentType,
                'transaction_id' => $transactionId,
            ]);
        }
        elseif ($transactionStatus === 'pending') {
            $order->update(['status' => 'menunggu_pembayaran']);
        }
        elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $order->update(['status' => 'dibatalkan']);
        }
    }
}
