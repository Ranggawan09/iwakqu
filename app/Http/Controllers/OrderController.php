<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderRating;
use App\Models\Setting;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    // =========================================================================
    // Halaman checkout — tampilkan keranjang + data terakhir user
    // =========================================================================
    public function checkout()
    {
        $carts = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong.');
        }

        $total = $carts->sum(fn($c) => $c->subtotal);

        // Ambil order terakhir untuk auto-fill nama, HP, alamat, dan koordinat
        $lastOrder = Order::where('user_id', auth()->id())
            ->latest()
            ->first();

        return view('customer.checkout', compact('carts', 'total', 'lastOrder'));
    }

    // =========================================================================
    // Proses order — hitung ongkir, simpan order, buat snap token Midtrans
    // =========================================================================
    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'address'       => 'required|string',
            'phone'         => 'required|string|max:20',
        ]);

        $carts = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong.');
        }

        $subtotal = $carts->sum(fn($c) => $c->subtotal);

        // ── Hitung ongkos kirim via OSRM ──────────────────────────────────────
        $userLat   = $request->filled('latitude')  ? (float) $request->latitude  : null;
        $userLng   = $request->filled('longitude') ? (float) $request->longitude : null;
        $adminLat  = (float) Setting::get('admin_latitude',  0);
        $adminLng  = (float) Setting::get('admin_longitude', 0);
        $ratePerKm = (float) Setting::get('shipping_rate_per_km', 0);

        $distanceKm   = 0;
        $shippingCost = 0;

        if ($userLat && $userLng && ($adminLat || $adminLng) && $ratePerKm > 0) {
            $distanceKm   = $this->getOsrmDistance($adminLat, $adminLng, $userLat, $userLng);
            $shippingCost = (int) ceil($distanceKm * $ratePerKm);
        }

        $total = $subtotal + $shippingCost;

        // ── Simpan order ke database ───────────────────────────────────────────
        $order = Order::create([
            'user_id'       => auth()->id(),
            'customer_name' => $request->customer_name,
            'address'       => $request->address,
            'latitude'      => $userLat,
            'longitude'     => $userLng,
            'phone'         => $request->phone,
            'total_price'   => $total,
            'shipping_cost' => $shippingCost,
            'distance_km'   => $distanceKm > 0 ? round($distanceKm, 2) : null,
            'status'        => 'menunggu_pembayaran',
        ]);

        // ── Simpan item pesanan ────────────────────────────────────────────────
        foreach ($carts as $cart) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $cart->product_id,
                'quantity'   => $cart->quantity,
                'price'      => $cart->product->price,
            ]);
        }

        // ── Generate Mayar Payment Link ────────────────────────────────────────
        $referenceId = 'IWAKQU-' . $order->id . '-' . time();
        $paymentLink = $this->createMayarPayment($order, $referenceId, $carts, $shippingCost, $distanceKm);

        if ($paymentLink) {
            $order->update(['payment_link' => $paymentLink]);
        }

        // Clear cart
        Cart::where('user_id', auth()->id())->delete();

        return view('customer.payment', compact('order', 'paymentLink'));
    }

    // =========================================================================
    // Riwayat pesanan
    // =========================================================================
    public function history()
    {
        $orders = Order::with('orderItems.product', 'rating')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    // =========================================================================
    // Detail pesanan
    // =========================================================================
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load('orderItems.product');

        return view('customer.orders.show', compact('order'));
    }

    // =========================================================================
    // Simpan rating pesanan
    // =========================================================================
    public function storeRating(Request $request, Order $order)
    {
        // Pastikan order milik user yang login dan statusnya selesai
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'selesai') {
            return back()->with('error', 'Hanya pesanan yang selesai yang dapat diberi rating.');
        }

        // Jika sudah pernah rating, update; jika belum, buat baru
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        OrderRating::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => auth()->id(),
                'rating'  => $request->rating,
                'review'  => $request->review,
            ]
        );

        return back()->with('success', 'Terima kasih atas penilaian Anda! ⭐');
    }

    // =========================================================================
    // Retry pembayaran — redirect ulang ke Mayar payment link
    // =========================================================================
    public function retryPayment(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'menunggu_pembayaran') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Pesanan ini tidak perlu dibayar ulang.');
        }

        // Jika belum ada payment_link, coba generate ulang dari order items
        if (!$order->payment_link) {
            $order->load('orderItems.product');
            $referenceId = 'IWAKQU-' . $order->id . '-' . time();

            // Konversi orderItems ke koleksi seperti Cart untuk createMayarPayment
            $fakeItems = $order->orderItems->map(fn($item) => (object)[
                'product'  => $item->product,
                'quantity' => $item->quantity,
            ]);

            $paymentLink = $this->createMayarPayment(
                $order,
                $referenceId,
                $fakeItems,
                (float) $order->shipping_cost,
                (float) ($order->distance_km ?? 0)
            );

            if ($paymentLink) {
                $order->update(['payment_link' => $paymentLink]);
            } else {
                return redirect()->route('orders.show', $order)
                    ->with('error', 'Link pembayaran tidak tersedia. Silakan hubungi kami.');
            }
        }
        return redirect()->away($order->payment_link);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

     /**
     * Buat Payment Link via Mayar Headless API.
     * Endpoint  : POST https://api.mayar.id/hl/v1/payment/create
     * Required  : name, amount, mobile, email
     * Response  : { data: { link: '...' } }
     */
    private function createMayarPayment(Order $order, string $referenceId, $carts, float $shippingCost, float $distanceKm): ?string
    {
        $isProduction = config('services.mayar.is_production', false);
        $apiKey       = config('services.mayar.api_key', '');
        $baseUrl = $isProduction
            ? 'https://api.mayar.id/hl/v1'
            : 'https://api.mayar.club/hl/v1';

        if (empty($apiKey)) {
            \Illuminate\Support\Facades\Log::error('[Mayar] API key kosong, cek MAYAR_API_KEY di .env');
            return null;
        }

        // Susun deskripsi produk
        $items = [];
        foreach ($carts as $cart) {
            $items[] = $cart->product->name . ' x' . $cart->quantity;
        }
        if ($shippingCost > 0) {
            $items[] = 'Ongkos Kirim (' . round($distanceKm, 1) . ' km)';
        }
        $description = implode(', ', $items);

        // Format payload sesuai Mayar Headless API (field mobile/email di root)
        $payload = [
            'name'        => 'Pesanan IwakQu #' . $order->id,
            'amount'      => (int) $order->total_price,
            'description' => $description ?: 'Ikan Marinasi IwakQu',
            'mobile'      => $order->phone,
            'email'       => auth()->user()->email,
            'redirectUrl' => url('/orders/' . $order->id),
        ];

        try {
            $endpoint = $baseUrl . '/payment/create';
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $apiKey,
                ],
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response  = curl_exec($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            \Illuminate\Support\Facades\Log::info('[Mayar] Payment create', [
                'endpoint'  => $endpoint,
                'http_code' => $httpCode,
                'curl_err'  => $curlError ?: null,
                'response'  => $response ? substr($response, 0, 500) : null,
            ]);

            if ($response && $httpCode === 200) {
                $data = json_decode($response, true);
                $link = $data['data']['link']
                    ?? $data['data']['paymentLink']
                    ?? $data['data']['payment_link']
                    ?? $data['data']['url']
                    ?? $data['link']
                    ?? null;

                if (!$link) {
                    \Illuminate\Support\Facades\Log::warning('[Mayar] Link tidak ditemukan', ['data' => $data]);
                }
                return $link;
            }

            \Illuminate\Support\Facades\Log::error('[Mayar] API gagal', [
                'http_code' => $httpCode,
                'response'  => $response,
                'curl_err'  => $curlError,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[Mayar] Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
    
     * Hitung jarak jalan nyata (km) via OSRM public API.
     * Profile: driving (paling mendekati sepeda motor).
     * Fallback otomatis ke Haversine jika OSRM tidak tersedia.
     */
    private function getOsrmDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        try {
            // OSRM menerima koordinat dalam format: longitude,latitude
            $url = sprintf(
                'https://router.project-osrm.org/route/v1/driving/%f,%f;%f,%f?overview=false',
                $lon1, $lat1,
                $lon2, $lat2
            );

            $ctx      = stream_context_create(['http' => ['timeout' => 6]]);
            $response = @file_get_contents($url, false, $ctx);

            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['routes'][0]['distance'])) {
                    // OSRM mengembalikan jarak dalam meter → konversi ke km
                    return round($data['routes'][0]['distance'] / 1000, 2);
                }
            }
        } catch (\Exception $e) {
            // Lanjut ke fallback
        }

        // Fallback: Haversine (garis lurus) jika OSRM tidak dapat dihubungi
        return $this->haversine($lat1, $lon1, $lat2, $lon2);
    }

    /**
     * Hitung jarak garis lurus (km) dengan rumus Haversine.
     * Digunakan sebagai fallback jika OSRM tidak tersedia.
     */
    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }
}
