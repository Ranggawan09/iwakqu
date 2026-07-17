@extends('layouts.app')
@section('title', 'Pembayaran — Order #{{ $order->id }}')

@section('content')
<div class="py-12 min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-3xl shadow-lg p-8 text-center border border-gray-100">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-5xl">🎉</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 mb-2">Pesanan Berhasil Dibuat!</h1>
            <p class="text-gray-500 mb-2">Order #{{ $order->id }}</p>
            <p class="text-gray-500 mb-6">Selesaikan pembayaran untuk memproses pesanan Anda.</p>

            <div class="bg-gray-50 rounded-2xl p-4 text-left mb-6 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Nama Penerima</span>
                    <span class="font-semibold">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Alamat</span>
                    <span class="font-semibold text-right max-w-xs">{{ $order->address }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">No. HP</span>
                    <span class="font-semibold">{{ $order->phone }}</span>
                </div>
                <div class="border-t border-gray-200 pt-2 flex justify-between">
                    <span class="font-black text-gray-900">Total</span>
                    <span class="font-black text-green-700 text-xl">{{ $order->formatted_total }}</span>
                </div>
            </div>

            @if($order->qris_string)
            <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-3xl border border-gray-100 mb-6">
                <div class="relative p-3 bg-white rounded-2xl shadow-sm mb-4">
                    <!-- ShopeePay & QRIS header logo -->
                    <div class="flex justify-between items-center mb-2 px-2">
                        <span class="text-xs font-bold text-gray-400">QRIS DYNAMIC</span>
                        <img src="https://gokepo.com/wp-content/uploads/2020/09/Logo-ShopeePay.png" alt="ShopeePay" class="h-5 object-contain">
                    </div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($order->qris_string) }}" 
                         alt="QRIS ShopeePay" 
                         class="w-60 h-60 rounded-xl">
                </div>
                
                <div class="text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 mb-2">
                        Scan dengan ShopeePay atau E-Wallet Lain
                    </span>
                    <div class="text-xs text-gray-400 mb-1">Batas Waktu Pembayaran:</div>
                    <div id="countdown" class="text-2xl font-black text-red-600">20:00</div>
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4 text-sm text-yellow-700">
                ⚠️ Kode QRIS pembayaran gagal dikonfigurasi. Silakan hubungi admin atau coba buat pesanan baru.
            </div>
            @endif

            <a href="{{ route('orders.index') }}" class="block text-green-700 font-medium hover:underline mt-2">
                Lihat Riwayat Pesanan →
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($order->qris_string && $order->qris_expiry)
<script>
    // 1. Countdown Timer
    const expiryTime = new Date("{{ $order->qris_expiry }}").getTime();

    const countdownInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = expiryTime - now;

        if (distance < 0) {
            clearInterval(countdownInterval);
            document.getElementById("countdown").innerHTML = "KADALUARSA";
            document.getElementById("countdown").className = "text-xl font-black text-gray-400";
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerHTML = 
            (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    }, 1000);

    // 2. Realtime AJAX Polling
    const orderId = "{{ $order->id }}";
    const statusPollInterval = setInterval(function() {
        fetch(`/api/orders/${orderId}/status`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'dibayar') {
                    clearInterval(statusPollInterval);
                    clearInterval(countdownInterval);
                    
                    // Show success notification and redirect
                    alert('Pembayaran Berhasil! Mengalihkan ke halaman detail pesanan...');
                    window.location.href = "{{ route('orders.show', $order) }}";
                }
            })
            .catch(err => console.error('Error polling status:', err));
    }, 3000); // poll every 3 seconds
</script>
@endif
@endpush

