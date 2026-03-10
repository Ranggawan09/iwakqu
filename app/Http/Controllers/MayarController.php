<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class MayarController extends Controller
{
    /**
     * Webhook callback dari server Mayar.
     * Dipanggil otomatis oleh Mayar setelah pembayaran berhasil/gagal.
     *
     * Mayar mengirim POST dengan body JSON ke endpoint ini.
     * Pastikan URL ini didaftarkan di dashboard Mayar sebagai webhook URL.
     */
    public function callback(Request $request)
    {
        // Ambil raw body untuk verifikasi signature (opsional, jika Mayar mendukung)
        $payload = $request->all();

        Log::info('[Mayar Webhook]', $payload);

        // Mayar webhook payload fields (sesuaikan dengan format webhook Mayar)
        // Referensi: https://docs.mayar.id/developers/webhook
        $status = $payload['status'] ?? null; // 'paid', 'failed', 'expired', dll
        $referenceId = $payload['referenceId'] ?? null; // order ID kita (format: IWAKQU-{id}-{time})
        $trxId = $payload['id'] ?? null; // ID transaksi Mayar
        $paymentType = $payload['paymentType'] ?? null; // 'bank_transfer', 'qris', dll

        if (!$referenceId) {
            return response()->json(['message' => 'referenceId missing'], 400);
        }

        // Extract order ID dari format "IWAKQU-{id}-{time}"
        $parts = explode('-', $referenceId);
        $orderId = $parts[1] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid referenceId format'], 400);
        }

        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update status order berdasarkan status Mayar
        match ($status) {
                'paid', 'settlement' => (function () use ($order, $paymentType, $trxId) {
                // Guard: hanya kurangi stok jika status sebelumnya belum "dibayar"
                $wasAlreadyPaid = $order->status === 'dibayar';

                $order->update([
                    'status' => 'dibayar',
                    'payment_method' => $paymentType,
                    'transaction_id' => $trxId,
                    'mayar_id' => $trxId,
                ]);

                if (!$wasAlreadyPaid) {
                    // Kurangi stok produk setelah pembayaran dikonfirmasi via webhook
                    $order->loadMissing('orderItems');
                    foreach ($order->orderItems as $item) {
                        Product::where('id', $item->product_id)
                            ->where('stock', '>', 0)
                            ->decrement('stock', $item->quantity);
                    }
                    Log::info('[Stock] Stok dikurangi via webhook', ['order_id' => $order->id]);
                }
            })(),

                'expired', 'failed', 'cancel' => $order->update([
                'status' => 'dibatalkan',
            ]),
                default => null, // pending / processing — biarkan status tidak berubah
            };

        return response()->json(['message' => 'OK']);
    }
}
