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

            @if(isset($paymentLink) && $paymentLink)
            <a href="{{ $paymentLink }}" id="pay-button" target="_blank" rel="noopener"
                    class="block w-full bg-green-700 text-white py-4 rounded-2xl font-black text-xl btn-glow hover:bg-green-600 transition-all shadow-lg mb-4">
                💳 Bayar Sekarang — {{ $order->formatted_total }}
            </a>
            <p class="text-xs text-gray-400 mb-4">Klik tombol di atas untuk membuka halaman pembayaran Mayar</p>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4 text-sm text-yellow-700">
                ⚠️ Link pembayaran gagal dikonfigurasi. Pastikan pengaturan sudah diatur di file <code>.env</code>.
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
@if(isset($paymentLink) && $paymentLink)
<script>
    // Bisa tambahkan auto redirect jika diinginkan
    // window.location.href = "{{ $paymentLink }}";
</script>
@endif
@endpush

