@extends('layouts.app')
@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
    <div class="py-12 min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-gray-600">← Kembali</a>
                <span class="text-gray-300">/</span>
                <span class="text-gray-700 font-semibold">Detail Pesanan #{{ $order->id }}</span>
            </div>

            <div class="space-y-4">
                {{-- Status + tombol bayar jika menunggu pembayaran --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="font-black text-xl text-gray-900">Order #{{ $order->id }}</h2>
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold
                            @if($order->status === 'selesai') bg-green-100 text-green-700
                            @elseif($order->status === 'dibayar') bg-blue-100 text-blue-700
                            @elseif($order->status === 'diproses') bg-indigo-100 text-indigo-700
                            @elseif($order->status === 'dikirim') bg-orange-100 text-orange-700
                            @elseif($order->status === 'dibatalkan') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm mb-4">{{ $order->created_at->format('d F Y, H:i') }}</p>

                    {{-- Tombol lanjutkan pembayaran --}}
                    @if($order->status === 'menunggu_pembayaran' && $order->payment_link)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                            <p class="text-yellow-700 text-sm font-medium mb-3">
                                ⏳ Pesanan ini belum dibayar. Selesaikan pembayaran sebelum batas waktu habis.
                            </p>
                            <a href="{{ $order->payment_link }}" target="_blank" rel="noopener"
                                class="w-full bg-green-700 text-white py-3 rounded-xl font-bold text-base hover:bg-green-600 transition-all shadow-md flex items-center justify-center gap-2">
                                Lanjutkan Pembayaran
                            </a>
                            <p class="text-xs text-gray-400 mt-2 text-center">Anda akan diarahkan ke halaman pembayaran Mayar
                            </p>
                        </div>
                    @elseif($order->status === 'menunggu_pembayaran' && !$order->payment_link)
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                            <p class="text-red-600 text-sm font-medium">Link pembayaran tidak tersedia. Silakan hubungi kami.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Delivery Info --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-3">📍 Informasi Pengiriman</h3>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><span class="text-gray-400">Nama</span>
                            <p class="font-semibold">{{ $order->customer_name }}</p>
                        </div>
                        <div><span class="text-gray-400">No. HP</span>
                            <p class="font-semibold">{{ $order->phone }}</p>
                        </div>
                        <div class="col-span-2"><span class="text-gray-400">Alamat</span>
                            <p class="font-semibold">{{ $order->address }}</p>
                        </div>
                        @if($order->latitude && $order->longitude)
                            <div class="col-span-2">
                                <span class="text-gray-400">Lokasi</span>
                                <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}"
                                    target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1 mt-1 text-xs text-blue-600 hover:underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                                    </svg>
                                    Lihat di Google Maps
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Items + Rincian Harga --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-4">🛍 Produk yang Dipesan</h3>
                    <div class="space-y-3">
                        @foreach($order->orderItems as $item)
                            <div class="flex items-center gap-4 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                                <img src="{{ $item->product->image_url }}"
                                    class="w-14 h-14 object-cover rounded-xl flex-shrink-0"
                                    onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=100&q=80'; this.onerror=null;">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                                    <p class="text-gray-400 text-sm">Rp {{ number_format($item->price, 0, ',', '.') }} ×
                                        {{ $item->quantity }}</p>
                                </div>
                                <p class="font-bold text-gray-900">{{ $item->formatted_subtotal }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Rincian biaya --}}
                    <div class="border-t border-gray-100 mt-4 pt-4 space-y-2">
                        {{-- Subtotal produk --}}
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal Produk</span>
                            <span class="font-semibold text-gray-800">
                                Rp
                                {{ number_format($order->orderItems->sum(fn($i) => $i->price * $i->quantity), 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Ongkir --}}
                        @if(($order->shipping_cost ?? 0) > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ongkos Kirim</span>
                                <span class="font-semibold text-gray-800">Rp
                                    {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        {{-- Global Discount --}}
                        @if(($order->global_discount_amount ?? 0) > 0)
                            <div class="flex justify-between text-sm text-pink-600">
                                <span>Diskon Toko (Otomatis)</span>
                                <span class="font-semibold">-Rp
                                    {{ number_format($order->global_discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        {{-- Voucher Discount --}}
                        @if(($order->voucher_discount ?? 0) > 0)
                            <div class="flex justify-between text-sm text-green-700">
                                <span>Voucher ({{ $order->voucher_code }})</span>
                                <span class="font-semibold">-Rp
                                    {{ number_format($order->voucher_discount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        {{-- Total --}}
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                            <span class="font-black text-gray-900">Total Pembayaran</span>
                            <span class="font-black text-green-700 text-xl">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-3">💳 Informasi Pembayaran</h3>
                    <div class="text-sm space-y-2">
                        @php
                            $displayMayarId = $order->mayar_id;
                            // Jika ID masih berupa UUID panjang, coba ambil yang pendek dari link (untuk pesanan lama)
                            if (strlen($displayMayarId ?? '') > 20 && $order->payment_link) {
                                if (preg_match('/\/(invoices|pay|p)\/([a-z0-9A-Z]+)/i', $order->payment_link, $m)) {
                                    $displayMayarId = $m[2];
                                }
                            }
                        @endphp

                        @if($displayMayarId)
                            <div class="flex justify-between">
                                <span class="text-gray-400">ID Transaksi</span>
                                <span class="font-mono font-semibold text-gray-700">{{ $displayMayarId }}</span>
                            </div>
                        @endif

                        <!-- @if($order->transaction_id)
                        <div class="flex justify-between">
                            <span class="text-gray-400">ID Transaksi</span>
                            <span class="font-mono font-semibold">{{ $order->transaction_id }}</span>
                        </div>
                        @endif

                        @if($order->payment_method)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Metode Pembayaran</span>
                            <span class="font-semibold">{{ $order->payment_method }}</span>
                        </div>
                        @endif -->

                        @if($order->status === 'menunggu_pembayaran')
                            <div class="pt-2">
                                <p class="text-yellow-600 font-medium flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-pulse" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Menunggu pembayaran...
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection