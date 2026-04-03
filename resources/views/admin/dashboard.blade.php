@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
{{-- Stat Cards --}}
{{--
  Mobile layout (2-col grid):
    [Pengguna] [Pesanan]    ← col-span-1 each = row 1
    [Pengunjung (compact)]  ← col-span-2 = row 2
    [Pendapatan (full)]     ← col-span-2 = row 3

  Desktop layout (sm: 4-col grid):
    [Pengguna] [Pesanan] [Pengunjung] [Pendapatan]
--}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

    {{-- Card 1: Total Pengguna --}}
    <div class="stat-card bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs sm:text-sm font-medium">Total Pengguna</p>
                <p class="text-3xl sm:text-4xl font-black text-gray-900 mt-1">{{ $totalUsers }}</p>
                <p class="text-green-600 text-xs mt-1">Pelanggan terdaftar</p>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-blue-50 rounded-2xl flex items-center justify-center">
                <span class="text-2xl sm:text-3xl">👥</span>
            </div>
        </div>
    </div>

    {{-- Card 2: Total Pesanan --}}
    <div class="stat-card bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs sm:text-sm font-medium">Total Pesanan</p>
                <p class="text-3xl sm:text-4xl font-black text-gray-900 mt-1">{{ $totalOrders }}</p>
                <p class="text-green-600 text-xs mt-1">Semua status</p>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-yellow-50 rounded-2xl flex items-center justify-center">
                <span class="text-2xl sm:text-3xl">📦</span>
            </div>
        </div>
    </div>

    {{-- Card 3: Pengunjung (compact on mobile = col-span-2 full width with smaller styling) --}}
    <div class="stat-card col-span-2 sm:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs sm:text-sm font-medium">Pengunjung</p>
                <p class="text-2xl sm:text-4xl font-black text-gray-900 mt-0.5 sm:mt-1">{{ number_format($totalVisitors) }}</p>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-purple-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                <span class="text-2xl sm:text-3xl">🌐</span>
            </div>
        </div>
    </div>

    {{-- Card 4: Total Pendapatan (full width on mobile = col-span-2) --}}
    <div class="stat-card col-span-2 sm:col-span-1 bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs sm:text-sm font-medium">Total Pendapatan</p>
                <p class="text-xl sm:text-2xl font-black text-green-700 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <p class="text-green-600 text-xs mt-1">Sudah dibayar & selesai</p>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-green-50 rounded-2xl flex items-center justify-center">
                <span class="text-2xl sm:text-3xl">💰</span>
            </div>
        </div>
    </div>

</div>


{{-- 1-column layout: Recent Orders --}}
<div class="grid grid-cols-1 gap-6">

    {{-- ── Recent Orders (Full width) ────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-green-700 text-sm font-medium hover:underline">Lihat Semua →</a>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($recentOrders as $order)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-3">
                    {{-- Left: ID + customer + items --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="font-black text-green-700 hover:underline text-sm">#{{ $order->id }}</a>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                @if($order->status === 'selesai') bg-green-100 text-green-700
                                @elseif($order->status === 'dibayar') bg-blue-100 text-blue-700
                                @elseif($order->status === 'diproses') bg-indigo-100 text-indigo-700
                                @elseif($order->status === 'dikirim') bg-orange-100 text-orange-700
                                @elseif($order->status === 'dibatalkan') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ $order->status_label }}
                            </span>
                            <span class="text-gray-300 text-xs">{{ $order->created_at->format('d M') }}</span>
                        </div>

                        <p class="font-semibold text-gray-800 text-sm">{{ $order->user->name }}</p>

                        <div class="mt-1 text-xs text-gray-500 space-y-0.5">
                            @foreach($order->orderItems as $item)
                            <p>{{ $item->product->name ?? $item->product_name }}
                                <span class="text-gray-400">×{{ $item->quantity }}</span>
                            </p>
                            @endforeach
                        </div>
                    </div>

                    {{-- Right: total + actions --}}
                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                        <p class="font-bold text-gray-900 text-sm">{{ $order->formatted_total }}</p>
                        <div class="flex items-center gap-1.5">
                            @if($order->latitude && $order->longitude)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->latitude }},{{ $order->longitude }}&travelmode=driving"
                               target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1 bg-green-50 text-green-700 border border-green-100 px-2 py-1 rounded-lg text-xs font-semibold hover:bg-green-100 transition-colors whitespace-nowrap">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                                Rute
                                @if($order->distance_km)
                                <span class="text-gray-400 font-normal">({{ $order->distance_km }}km)</span>
                                @endif
                            </a>
                            @endif

                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="inline-flex items-center bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg text-xs font-semibold hover:bg-gray-200 transition-colors">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-gray-400">Belum ada pesanan</div>
            @endforelse
        </div>
    </div>

    </div>
</div>
@endsection
