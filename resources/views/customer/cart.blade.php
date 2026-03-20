@extends('layouts.app')
@section('title', 'Keranjang Belanja')

@section('content')
<div class="py-12 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-gray-900 mb-8 flex items-center gap-3">
            Keranjang Belanja
            @if($carts->count() > 0)
                <span class="bg-green-700 text-white text-base font-bold px-3 py-1 rounded-full">{{ $carts->count() }} item</span>
            @endif
        </h1>

        @if($carts->isEmpty())
        <div class="bg-white rounded-3xl shadow-sm p-16 text-center border border-gray-100">
            <div class="text-8xl mb-4">🛒</div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">Keranjang Anda Kosong</h3>
            <p class="text-gray-400 mb-6">Tambahkan produk favorit Anda ke keranjang terlebih dahulu.</p>
            <a href="{{ route('home') }}#produk" class="inline-block bg-green-700 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-600 transition-all">
                Lihat Produk
            </a>
        </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($carts as $cart)
                <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-100 flex flex-col sm:flex-row sm:items-center gap-4 relative">
                    <div class="flex items-center gap-4 w-full sm:w-auto sm:flex-1 min-w-0">
                        <img src="{{ $cart->product->image_url }}" alt="{{ $cart->product->name }}"
                             class="w-20 h-20 object-cover rounded-xl flex-shrink-0"
                             onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=200&q=80'; this.onerror=null;">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-900 truncate pr-8 sm:pr-0">{{ $cart->product->name }}</h3>
                            <p class="text-green-700 font-semibold">{{ $cart->product->formatted_price }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto mt-2 sm:mt-0 px-1 sm:px-0">
                        <div class="flex items-center">
                            <form action="{{ route('cart.update', $cart) }}" method="POST" class="flex items-center gap-1">
                                @csrf @method('PUT')
                                <button type="button" onclick="changeQty(this, -1)" class="w-8 h-8 bg-gray-100 rounded-lg text-gray-700 font-bold hover:bg-gray-200 transition-colors">-</button>
                                <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" max="{{ $cart->product->stock }}"
                                       class="w-14 text-center border border-gray-200 rounded-lg py-1 text-sm font-bold focus:ring-2 focus:ring-green-500"
                                       onchange="this.form.submit()">
                                <button type="button" onclick="changeQty(this, 1)" class="w-8 h-8 bg-gray-100 rounded-lg text-gray-700 font-bold hover:bg-gray-200 transition-colors">+</button>
                            </form>
                        </div>
                        <div class="text-right min-w-0 sm:ml-4">
                            <p class="font-black text-gray-900 text-base">{{ $cart->formatted_subtotal }}</p>
                        </div>
                    </div>

                    <form action="{{ route('cart.remove', $cart) }}" method="POST" class="absolute top-4 right-4 sm:static sm:top-auto sm:right-auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-500 transition-colors p-1" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
                @endforeach

                <form action="{{ route('cart.clear') }}" method="POST" class="text-right"
                      onsubmit="return confirm('Kosongkan seluruh keranjang?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 text-sm font-medium hover:text-red-700">
                        🗑 Kosongkan Keranjang
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 sticky top-24">
                    <h3 class="font-bold text-gray-900 text-lg mb-4">Ringkasan Pesanan</h3>
                    <div class="space-y-3 mb-4">
                        @foreach($carts as $cart)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 truncate pr-2">{{ $cart->product->name }} x{{ $cart->quantity }}</span>
                            <span class="font-medium text-gray-900 whitespace-nowrap">{{ $cart->formatted_subtotal }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-100 pt-4 mb-6">
                        <div class="flex justify-between">
                            <span class="font-black text-gray-900">Total</span>
                            <span class="font-black text-green-700 text-xl">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('checkout.index') }}"
                       class="block text-center bg-green-700 text-white py-3.5 rounded-xl font-bold text-lg hover:bg-green-600 btn-glow transition-all">
                        Lanjut Checkout →
                    </a>
                    <a href="{{ route('home') }}#produk"
                       class="block text-center text-gray-500 text-sm mt-3 hover:text-gray-700">
                        ← Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function changeQty(btn, delta) {
    const input = btn.parentElement.querySelector('input[name="quantity"]');
    const max = parseInt(input.getAttribute('max'));
    let newVal = parseInt(input.value) + delta;
    if (newVal < 1) newVal = 1;
    if (newVal > max) newVal = max;
    input.value = newVal;
    input.form.submit();
}
</script>
@endpush
