@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')

@section('content')
{{-- Filter bar --}}
<div class="mb-5 flex flex-wrap gap-2">
    @foreach([''=> 'Semua', 'menunggu_pembayaran'=> 'Menunggu', 'dibayar'=> 'Dibayar', 'diproses'=> 'Diproses', 'dikirim'=> 'Dikirim', 'selesai'=> 'Selesai', 'dibatalkan'=> 'Dibatalkan'] as $val => $label)
    <a href="{{ route('admin.orders.index', $val ? ['status' => $val] : []) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ request('status') === $val || (!request('status') && $val === '') ? 'bg-green-700 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full min-w-[800px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">ID</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Produk Dipesan</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lokasi</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50 transition-colors">

                {{-- ID --}}
                <td class="px-5 py-4 font-bold text-green-700">#{{ $order->id }}</td>

                {{-- Pelanggan --}}
                <td class="px-5 py-4">
                    <p class="font-semibold text-gray-900">{{ $order->customer_name }}</p>
                    <p class="text-gray-400 text-xs">{{ $order->user->email }}</p>
                </td>

                {{-- Produk Dipesan --}}
                <td class="px-5 py-4">
                    @foreach($order->orderItems as $item)
                        <p class="text-sm text-gray-700 leading-snug">
                            {{ $item->product->name ?? $item->product_name }}
                            <span class="text-gray-400 font-medium">x{{ $item->quantity }}</span>
                        </p>
                    @endforeach
                </td>

                {{-- Total --}}
                <td class="px-5 py-4 font-semibold text-gray-900">{{ $order->formatted_total }}</td>

                {{-- Quick Status Update --}}
                <td class="px-5 py-4">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex items-center gap-1.5">
                        @csrf @method('PUT')
                        <select name="status" onchange="this.form.submit()"
                                class="text-xs font-semibold rounded-lg px-2 py-1.5 border outline-none cursor-pointer transition-colors
                                    @if($order->status === 'selesai') bg-green-50 border-green-200 text-green-700
                                    @elseif($order->status === 'dibayar') bg-blue-50 border-blue-200 text-blue-700
                                    @elseif($order->status === 'diproses') bg-indigo-50 border-indigo-200 text-indigo-700
                                    @elseif($order->status === 'dikirim') bg-orange-50 border-orange-200 text-orange-700
                                    @elseif($order->status === 'dibatalkan') bg-red-50 border-red-200 text-red-700
                                    @else bg-yellow-50 border-yellow-200 text-yellow-700 @endif">
                            @foreach([
                                'menunggu_pembayaran' => 'Menunggu',
                                'dibayar'             => 'Dibayar',
                                'diproses'            => 'Diproses',
                                'dikirim'            => 'Dikirim',
                                'selesai'             => 'Selesai',
                                'dibatalkan'          => 'Dibatalkan',
                            ] as $val => $lbl)
                            <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </form>
                </td>

                {{-- Lokasi / Google Maps --}}
                <td class="px-5 py-4">
                    @if($order->latitude && $order->longitude)
                        {{-- Link buka di Google Maps dengan pin lokasi --}}
                        <a href="https://www.google.com/maps?q={{ $order->latitude }},{{ $order->longitude }}"
                           target="_blank" rel="noopener"
                           title="Buka di Google Maps"
                           class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-600 border border-blue-200 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/>
                            </svg>
                            Lihat Peta
                        </a>
                        {{-- Link rute dari lokasi admin ke lokasi pemesan --}}
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->latitude }},{{ $order->longitude }}&travelmode=driving"
                           target="_blank" rel="noopener"
                           title="Lihat rute pengiriman"
                           class="mt-1 inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-200 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-green-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            Rute
                        </a>
                    @else
                        <span class="text-gray-300 text-xs italic">Tidak ada koordinat</span>
                    @endif
                </td>

                {{-- Tanggal --}}
                <td class="px-5 py-4 text-gray-500 text-sm whitespace-nowrap">{{ $order->created_at->format('d M Y') }}</td>

                {{-- Aksi --}}
                <td class="px-5 py-4">
                    <a href="{{ route('admin.orders.show', $order) }}"
                       class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-10 text-gray-400">Tidak ada pesanan</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($orders->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
