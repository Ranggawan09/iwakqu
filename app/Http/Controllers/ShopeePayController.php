<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\ShopeePayService;
use App\Notifications\OrderPaidNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ShopeePayController extends Controller
{
    protected ShopeePayService $shopeePayService;

    public function __construct(ShopeePayService $shopeePayService)
    {
        $this->shopeePayService = $shopeePayService;
    }

    /**
     * Webhook callback dari ShopeePay untuk MPM QRIS.
     * Endpoint ini dipanggil ketika user telah sukses membayar via QRIS.
     */
    public function callback(Request $request)
    {
        $signature = $request->header('X-SIGNATURE');
        $timestamp = $request->header('X-TIMESTAMP');
        
        // Dapatkan URL lengkap callback (ShopeePay menggunakan full URL beserta domain)
        $fullUrl = $request->fullUrl();
        $rawBody = $request->getContent();

        Log::info('[ShopeePay Webhook] Received callback', [
            'url' => $fullUrl,
            'timestamp' => $timestamp,
            'signature' => $signature,
            'body' => $rawBody
        ]);

        if (empty($signature) || empty($timestamp)) {
            Log::warning('[ShopeePay Webhook] Missing headers.');
            return response()->json([
                'responseCode' => '4005200',
                'responseMessage' => 'Bad Request. Missing X-SIGNATURE or X-TIMESTAMP.'
            ], 400);
        }

        // Verifikasi signature
        $isVerified = $this->shopeePayService->verifyCallbackSignature($fullUrl, $rawBody, $timestamp, $signature);
        if (!$isVerified) {
            Log::error('[ShopeePay Webhook] Signature verification failed.');
            return response()->json([
                'responseCode' => '4015200',
                'responseMessage' => 'Unauthorized. Invalid Signature.'
            ], 401);
        }

        $payload = json_decode($rawBody, true);
        $status = $payload['latestTransactionStatus'] ?? null; // '00' = Success
        $partnerRefNo = $payload['originalPartnerReferenceNo'] ?? null; // IWAKQU-{id}-{time}
        $trxId = $payload['originalReferenceNo'] ?? null; // Transaction ID ShopeePay

        if ($status !== '00') {
            Log::info('[ShopeePay Webhook] Payment not successful yet', ['status' => $status]);
            return response()->json([
                'responseCode' => '2005200',
                'responseMessage' => 'Successful receipt (Payment status is not success)'
            ]);
        }

        if (empty($partnerRefNo)) {
            Log::error('[ShopeePay Webhook] originalPartnerReferenceNo is missing.');
            return response()->json([
                'responseCode' => '4005200',
                'responseMessage' => 'Bad Request. Missing originalPartnerReferenceNo.'
            ], 400);
        }

        // Extract order ID dari format "IWAKQU-{id}-{time}"
        $parts = explode('-', $partnerRefNo);
        $orderId = $parts[1] ?? null;

        if (!$orderId) {
            Log::error('[ShopeePay Webhook] Invalid originalPartnerReferenceNo format: ' . $partnerRefNo);
            return response()->json([
                'responseCode' => '4005200',
                'responseMessage' => 'Bad Request. Invalid transaction reference format.'
            ], 400);
        }

        $order = Order::find($orderId);
        if (!$order) {
            Log::error('[ShopeePay Webhook] Order not found for ID: ' . $orderId);
            return response()->json([
                'responseCode' => '4045200',
                'responseMessage' => 'Order Not Found.'
            ], 404);
        }

        // Update status order menjadi "dibayar"
        $wasAlreadyPaid = $order->status === 'dibayar';

        $order->update([
            'status' => 'dibayar',
            'payment_method' => 'qris_shopeepay',
            'transaction_id' => $trxId,
        ]);

        if (!$wasAlreadyPaid) {
            // Kurangi stok produk
            $order->loadMissing('orderItems');
            foreach ($order->orderItems as $item) {
                Product::where('id', $item->product_id)
                    ->where('stock', '>', 0)
                    ->decrement('stock', $item->quantity);
            }
            
            // Kirim notifikasi ke admin
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new OrderPaidNotification($order));

            Log::info('[ShopeePay Webhook] Order successfully paid and stock decremented.', ['order_id' => $order->id]);
        }

        return response()->json([
            'responseCode' => '2005200',
            'responseMessage' => 'Successful'
        ]);
    }

    /**
     * Endpoint API untuk mengecek status pesanan secara realtime.
     * Dipanggil menggunakan AJAX polling dari halaman detail pembayaran.
     */
    public function checkStatus(Order $order)
    {
        // Pastikan hanya pemilik order atau admin yang bisa cek status
        if (auth()->id() !== $order->user_id && (!auth()->user() || !auth()->user()->isAdmin())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'order_id' => $order->id,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'status_color' => $order->status_color,
        ]);
    }
}
