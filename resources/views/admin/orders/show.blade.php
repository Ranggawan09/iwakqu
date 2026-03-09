@extends('layouts.admin')
@section('title', 'Detail Pesanan #{{ $order->id }}')
@section('page-title', 'Detail Pesanan #{{ $order->id }}')

@section('content')
<div class="max-w-3xl space-y-5">
    <a href="{{ route('admin.orders.index') }}" class="text-gray-400 text-sm hover:text-gray-600 inline-block">← Kembali</a>

    <!-- Status Update Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-900 mb-4">Update Status Pesanan</h2>
        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex items-center gap-3">
            @csrf @method('PUT')
            <select name="status" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 focus:border-green-500 outline-none font-medium text-gray-700">
                @foreach(['menunggu_pembayaran'=>'Menunggu Pembayaran','dibayar'=>'Dibayar','diproses'=>'Diproses','dikirim'=>'Sedang Dikirim','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'] as $val => $label)
                <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-green-600 transition-all">
                Update Status
            </button>
        </form>
    </div>

    <!-- Order Info -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="font-black text-xl">Order #{{ $order->id }}</h2>
                <p class="text-gray-400 text-sm">{{ $order->created_at->format('d F Y, H:i') }}</p>
            </div>
            <span class="inline-flex px-3 py-1.5 rounded-full text-sm font-bold
                @if($order->status === 'selesai') bg-green-100 text-green-700
                @elseif($order->status === 'dibayar') bg-blue-100 text-blue-700
                @elseif($order->status === 'diproses') bg-indigo-100 text-indigo-700
                @elseif($order->status === 'dikirim') bg-orange-100 text-orange-700
                @elseif($order->status === 'dibatalkan') bg-red-100 text-red-700
                @else bg-yellow-100 text-yellow-700 @endif">
                {{ $order->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-5">
            <div><p class="text-gray-400">Pelanggan</p><p class="font-semibold">{{ $order->customer_name }}</p></div>
            <div><p class="text-gray-400">Email</p><p class="font-semibold">{{ $order->user->email }}</p></div>
            <div><p class="text-gray-400">No. HP</p><p class="font-semibold">{{ $order->phone }}</p></div>
            <div><p class="text-gray-400">Metode Bayar</p><p class="font-semibold">{{ $order->payment_method ?? '-' }}</p></div>
            <div class="col-span-2"><p class="text-gray-400">Alamat</p><p class="font-semibold">{{ $order->address }}</p></div>
            @if($order->transaction_id)
            <div class="col-span-2"><p class="text-gray-400">ID Transaksi Mayar</p><p class="font-mono font-semibold text-xs">{{ $order->transaction_id }}</p></div>
            @endif
        </div>

        <h3 class="font-bold text-gray-900 mb-3">Produk yang Dipesan</h3>
        <div class="space-y-3">
            @foreach($order->orderItems as $item)
            <div class="flex items-center gap-3 pb-3 border-b border-gray-50 last:border-0">
                <img src="{{ $item->product->image_url }}" class="w-12 h-12 object-cover rounded-xl"
                     onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=100&q=80'; this.onerror=null;">
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                    <p class="text-gray-400 text-sm">Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                </div>
                <p class="font-bold text-gray-900">{{ $item->formatted_subtotal }}</p>
            </div>
            @endforeach
        </div>
        <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between">
            <span class="font-black text-gray-900">Total Pembayaran</span>
            <span class="font-black text-green-700 text-xl">{{ $order->formatted_total }}</span>
        </div>
    </div>
</div>
@endsection
