<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderRating;
use App\Models\Product;
use App\Models\Setting;

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
    // Proses order — hitung ongkir, simpan order
    // =========================================================================
    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'address'       => 'required|string',
            'phone'         => 'required|string|max:20',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
        ], [
            'latitude.required'  => 'Titik lokasi pengiriman peta (Pin Merah) belum dipilih.',
            'longitude.required' => 'Titik lokasi pengiriman peta (Pin Merah) belum dipilih.',
            'latitude.numeric'   => 'Format titik lokasi tidak valid.',
            'longitude.numeric'  => 'Format titik lokasi tidak valid.',
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
        $minKm     = (float) Setting::get('min_distance_km', 0);
        $maxKm     = (float) Setting::get('max_distance_km', 0);

        // Jika lokasi pengguna / toko belum di set pastikan di tolak
        if (!$userLat || !$userLng) {
             return back()->withInput()->with('error', 'Silakan pilih lokasi pengiriman pada peta terlebih dahulu.');
        }

        if (!$adminLat && !$adminLng) {
            return back()->withInput()->with('error', 'Mohon maaf, lokasi toko kami belum diatur sehingga ongkos kirim belum bisa dihitung.');
        }

        $distanceKm   = 0;
        $shippingCost = 0;

        if ($ratePerKm > 0) {
            $distanceKm   = $this->getOsrmDistance($adminLat, $adminLng, $userLat, $userLng);
            
            if ($maxKm > 0 && $distanceKm > $maxKm) {
                return back()->withInput()->with('error', 'Lokasi pengiriman diluar jangkauan maksimal (' . $maxKm . ' km).');
            }

            $calcDist = max($distanceKm, $minKm);
            $shippingCost = (int) ceil($calcDist * $ratePerKm);
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
        $mayarResult = $this->createMayarPayment($order, $referenceId, $carts, $shippingCost, $distanceKm);
        $paymentLink = $mayarResult['link'] ?? null;
        $mayarId     = $mayarResult['mayar_id'] ?? null;

        if ($paymentLink) {
            $order->update([
                'payment_link' => $paymentLink,
                'mayar_id'     => $mayarId,
            ]);
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
        $user = auth()->user();

        if (!$user || ($order->user_id != $user->id && !$user->isAdmin())) {
            abort(403);
        }

        $order->load('orderItems.product');

        // Auto-sync status pembayaran dari Mayar API saat user kembali ke halaman ini.
        // Ini berguna saat webhook tidak bisa menjangkau server lokal / dev environment.
        if ($order->status === 'menunggu_pembayaran') {
            $this->checkAndSyncMayarStatus($order);
            $order->refresh(); // Reload agar status terbaru tampil
        }

        return view('customer.orders.show', compact('order'));
    }

    // =========================================================================
    // Simpan rating pesanan
    // =========================================================================
    public function storeRating(Request $request, Order $order)
    {
        // Pastikan order milik user yang login dan statusnya selesai
        if ($order->user_id != auth()->id()) {
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
    // Payment Return — redirect target dari Mayar setelah user selesai bayar
    // =========================================================================
    public function paymentReturn(Order $order)
    {
        // Sync status dari Mayar API (fallback karena webhook tidak bisa akses localhost)
        if ($order->status === 'menunggu_pembayaran') {
            $this->checkAndSyncMayarStatus($order);
            $order->refresh();
        }

        $message = match ($order->status) {
            'dibayar'   => 'Pembayaran berhasil! Pesanan Anda sedang diproses. ✅',
            'dibatalkan' => 'Pembayaran dibatalkan atau kadaluarsa.',
            default      => 'Silakan cek kembali status pesanan Anda.',
        };

        $type = $order->status === 'dibayar' ? 'success' : 'info';

        return redirect()->route('orders.show', $order)->with($type, $message);
    }

    // =========================================================================
    // Retry pembayaran — redirect ulang ke Mayar payment link
    // =========================================================================
    public function retryPayment(Order $order)
    {
        if ($order->user_id != auth()->id()) {
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

            $mayarResult = $this->createMayarPayment(
                $order,
                $referenceId,
                $fakeItems,
                (float) $order->shipping_cost,
                (float) ($order->distance_km ?? 0)
            );

            $paymentLink = $mayarResult['link'] ?? null;
            $mayarId     = $mayarResult['mayar_id'] ?? null;

            if ($paymentLink) {
                $order->update([
                    'payment_link' => $paymentLink,
                    'mayar_id'     => $mayarId,
                ]);
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
     * Response  : { data: { link: '...', id: '...' } }
     *
     * @return array{link: string|null, mayar_id: string|null}|null
     */
    private function createMayarPayment(Order $order, string $referenceId, $carts, float $shippingCost, float $distanceKm): ?array
    {
        $isProduction = config('services.mayar.is_production', false);
        $apiKey       = config('services.mayar.api_key', '');
        $baseUrl = $isProduction
            ? 'https://api.mayar.id/hl/v1'
            : 'https://api.mayar.club/hl/v1';

        if (empty($apiKey)) {
            Log::error('[Mayar] API key kosong, cek MAYAR_API_KEY di .env');
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
            'referenceId' => $referenceId, // ← agar Mayar mengirim balik di webhook
            // Route publik agar tidak error 403 saat session hilang setelah redirect dari Mayar
            'redirectUrl' => route('payment.return', $order),
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

            Log::info('[Mayar] Payment create', [
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

                // Ambil Mayar invoice/payment ID untuk keperluan status check
                $mayarId = $data['data']['id']
                    ?? $data['data']['paymentId']
                    ?? $data['id']
                    ?? null;

                // Fallback: extract ID dari URL link (contoh: .../invoices/xxstjgnbu9)
                if (!$mayarId && $link && preg_match('/\/invoices\/([a-z0-9]+)/i', $link, $m)) {
                    $mayarId = $m[1];
                }

                if (!$link) {
                    Log::warning('[Mayar] Link tidak ditemukan', ['data' => $data]);
                }

                return ['link' => $link, 'mayar_id' => $mayarId];
            }

            Log::error('[Mayar] API gagal', [
                'http_code' => $httpCode,
                'response'  => $response,
                'curl_err'  => $curlError,
            ]);
        } catch (\Exception $e) {
            Log::error('[Mayar] Exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Cek status pembayaran ke Mayar API dan update order jika sudah dibayar.
     * Digunakan sebagai fallback saat webhook tidak bisa menjangkau server (misal: localhost/dev).
     */
    private function checkAndSyncMayarStatus(Order $order): void
    {
        // Butuh mayar_id untuk cek status; bisa dari DB atau extract dari payment_link URL
        $mayarId = $order->mayar_id;

        if (!$mayarId && $order->payment_link) {
            // Extract dari URL: .../invoices/{id} atau .../pay/{id}
            if (preg_match('/\/invoices\/([a-z0-9]+)/i', $order->payment_link, $m)) {
                $mayarId = $m[1];
            } elseif (preg_match('/\/pay\/([a-z0-9]+)/i', $order->payment_link, $m)) {
                $mayarId = $m[1];
            }
        }

        if (!$mayarId) {
            Log::info('[Mayar] checkAndSyncMayarStatus: mayar_id tidak ditemukan', ['order_id' => $order->id]);
            return;
        }

        $isProduction = config('services.mayar.is_production', false);
        $apiKey       = config('services.mayar.api_key', '');
        $baseUrl      = $isProduction
            ? 'https://api.mayar.id/hl/v1'
            : 'https://api.mayar.club/hl/v1';

        if (empty($apiKey)) return;

        try {
            $endpoint = $baseUrl . '/payment/' . $mayarId;
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPGET        => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $apiKey,
                ],
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('[Mayar] Status check', [
                'order_id'  => $order->id,
                'mayar_id'  => $mayarId,
                'http_code' => $httpCode,
                'response'  => $response ? substr($response, 0, 300) : null,
            ]);

            if ($response && $httpCode === 200) {
                $data   = json_decode($response, true);
                $status = $data['data']['status']
                    ?? $data['data']['paymentStatus']
                    ?? $data['status']
                    ?? null;

                if (in_array(strtolower((string) $status), ['paid', 'settlement', 'success'])) {
                    $order->update([
                        'status'         => 'dibayar',
                        'transaction_id' => $data['data']['id'] ?? $order->transaction_id,
                        'payment_method' => $data['data']['paymentType'] ?? $data['data']['payment_method'] ?? $order->payment_method,
                        'mayar_id'       => $mayarId,
                    ]);
                    $this->deductStock($order);
                    Log::info('[Mayar] Order diupdate ke dibayar via API check', ['order_id' => $order->id]);
                } elseif (in_array(strtolower((string) $status), ['expired', 'failed', 'cancel', 'cancelled'])) {
                    $order->update(['status' => 'dibatalkan']);
                    Log::info('[Mayar] Order diupdate ke dibatalkan via API check', ['order_id' => $order->id]);
                }
            }
        } catch (\Exception $e) {
            Log::error('[Mayar] checkAndSyncMayarStatus exception: ' . $e->getMessage());
        }
    }

    /**
     * Kurangi stok produk berdasarkan item-item dalam pesanan.
     * Dipanggil sekali saat status order berubah menjadi 'dibayar'.
     */
    private function deductStock(Order $order): void
    {
        $order->loadMissing('orderItems');

        foreach ($order->orderItems as $item) {
            Product::where('id', $item->product_id)
                ->where('stock', '>', 0)
                ->decrement('stock', $item->quantity);
        }

        Log::info('[Stock] Stok dikurangi setelah pembayaran', ['order_id' => $order->id]);
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
