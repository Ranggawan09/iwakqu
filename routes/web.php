<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MayarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\SocialAuthController;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Google OAuth
    Route::get('/auth/google',          [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Mayar Webhook Callback (no CSRF — called by Mayar server)
// Daftarkan URL ini di dashboard Mayar sebagai webhook URL
Route::post('/mayar/callback', [MayarController::class, 'callback'])
    ->name('mayar.callback')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Mayar Payment Return — redirect target setelah user selesai bayar di Mayar
// Tidak butuh auth agar session yang hilang saat redirect tidak menyebabkan error
Route::get('/payment/return/{order}', [OrderController::class, 'paymentReturn'])
    ->name('payment.return');

// ─── Customer Routes (auth required) ─────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Profile
    Route::get('/profile',          [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/info',     [ProfileController::class, 'updateInfo'])->name('profile.update-info');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Web Push
    Route::post('/push-subscriptions', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push-subscriptions.store');

    // Notifications
    Route::get('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? url('/'));
    })->name('notifications.read');

    // Cart
    Route::get('/cart',           [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add',      [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{cart}',    [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart',        [CartController::class, 'clear'])->name('cart.clear');

    // Checkout & Orders
    Route::get('/checkout',               [OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout',              [OrderController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/orders',                 [OrderController::class, 'history'])->name('orders.index');
    Route::get('/orders/{order}',         [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/rating', [OrderController::class, 'storeRating'])->name('orders.rating');
    Route::put('/orders/{order}/cancel',  [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/pay',     [OrderController::class, 'retryPayment'])->name('orders.pay');

    // ── Shipping Estimate API (dipanggil JS checkout) ─────────────────────────
    // Menggunakan OSRM routing (driving profile ≈ sepeda motor)
    // Fallback ke Haversine jika OSRM tidak bisa diakses
    Route::get('/api/shipping-estimate', function (\Illuminate\Http\Request $req) {
        $lat      = (float) $req->query('lat', 0);
        $lng      = (float) $req->query('lng', 0);
        $adminLat = (float) \App\Models\Setting::get('admin_latitude',  0);
        $adminLng = (float) \App\Models\Setting::get('admin_longitude', 0);
        $rate     = (float) \App\Models\Setting::get('shipping_rate_per_km', 0);
        $minKm    = (float) \App\Models\Setting::get('min_distance_km', 0);
        $maxKm    = (float) \App\Models\Setting::get('max_distance_km', 0);

        if (!$lat || !$lng || (!$adminLat && !$adminLng)) {
            return response()->json(['distance_km' => 0, 'shipping_cost' => 0, 'is_out_of_range' => false]);
        }

        // Coba OSRM terlebih dahulu
        $dist = 0;
        try {
            // OSRM menerima koordinat dalam format: lng,lat
            $url = sprintf(
                'https://router.project-osrm.org/route/v1/driving/%f,%f;%f,%f?overview=false',
                $adminLng, $adminLat,
                $lng, $lat
            );
            $ctx      = stream_context_create(['http' => ['timeout' => 6]]);
            $response = @file_get_contents($url, false, $ctx);

            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['routes'][0]['distance'])) {
                    $dist = round($data['routes'][0]['distance'] / 1000, 2);
                }
            }
        } catch (\Exception $e) {}

        // Fallback ke Haversine jika OSRM gagal
        if ($dist <= 0) {
            $R    = 6371;
            $dLat = deg2rad($lat - $adminLat);
            $dLon = deg2rad($lng  - $adminLng);
            $a    = sin($dLat / 2) ** 2 + cos(deg2rad($adminLat)) * cos(deg2rad($lat)) * sin($dLon / 2) ** 2;
            $dist = round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
        }

        $isOutOfRange = false;
        if ($maxKm > 0 && $dist > $maxKm) {
            $isOutOfRange = true;
        }

        $calcDist = max($dist, $minKm);

        return response()->json([
            'distance_km'     => $dist,
            'shipping_cost'   => (int) ceil($calcDist * $rate),
            'is_out_of_range' => $isOutOfRange,
        ]);
    })->name('shipping.estimate');

    // ── Voucher API ───────────────────────────────────────────────────────────
    Route::post('/api/vouchers/apply', [\App\Http\Controllers\VoucherController::class, 'apply'])->name('vouchers.apply');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products CRUD
    Route::resource('products', AdminProductController::class)->except(['show']);

    // Orders
    Route::get('/orders',                [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',        [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

    // Users
    Route::get('/users',          [AdminUserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}',[AdminUserController::class, 'destroy'])->name('users.destroy');

    // Settings (shipping config & operational hours)
    Route::get('/settings',  [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Vouchers CRUD
    Route::post('/vouchers',          [\App\Http\Controllers\Admin\VoucherController::class, 'store'])->name('vouchers.store');
    Route::delete('/vouchers/{voucher}', [\App\Http\Controllers\Admin\VoucherController::class, 'destroy'])->name('vouchers.destroy');
    Route::post('/vouchers/{voucher}/toggle', [\App\Http\Controllers\Admin\VoucherController::class, 'toggle'])->name('vouchers.toggle');
});
