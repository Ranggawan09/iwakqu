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
        @php
            $hasInvalidStock = $carts->contains(function ($cart) {
                return $cart->quantity > $cart->product->stock;
            });
        @endphp
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($carts as $cart)
                @php
                    $isItemOutOfStock = $cart->product->stock <= 0;
                    $isItemInsufficient = $cart->quantity > $cart->product->stock;
                @endphp
                <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-100 flex flex-col sm:flex-row sm:items-center gap-4 relative {{ $isItemInsufficient ? 'opacity-75 bg-red-50/30' : '' }}"
                     data-cart-id="{{ $cart->id }}">
                    <div class="flex items-center gap-4 w-full sm:w-auto sm:flex-1 min-w-0">
                        <div class="relative flex-shrink-0">
                            <img src="{{ $cart->product->image_url }}" alt="{{ $cart->product->name }}"
                                 class="w-20 h-20 object-cover rounded-xl {{ $isItemOutOfStock ? 'grayscale' : '' }}"
                                 onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=200&q=80'; this.onerror=null;">
                            @if($isItemOutOfStock)
                                <div class="absolute inset-0 bg-black/40 rounded-xl flex items-center justify-center">
                                    <span class="text-[10px] font-bold text-white uppercase tracking-wider">Habis</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h3 class="font-bold text-gray-900 truncate">{{ $cart->product->name }}</h3>
                                @if($isItemOutOfStock)
                                    <span class="bg-red-100 text-red-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">Stok Habis</span>
                                @elseif($isItemInsufficient)
                                    <span class="bg-orange-100 text-orange-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">Stok Tidak Cukup</span>
                                @endif
                            </div>
                            <p class="text-green-700 font-semibold">{{ $cart->product->formatted_price }}</p>
                            @if($isItemInsufficient && !$isItemOutOfStock)
                                <p class="text-[10px] text-orange-600 font-medium">Tersedia: {{ $cart->product->stock }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto mt-2 sm:mt-0 px-1 sm:px-0">
                        <div class="flex items-center">
                            {{-- Form tetap ada sebagai fallback non-JS --}}
                            <form action="{{ route('cart.update', $cart) }}" method="POST"
                                  class="flex items-center gap-1 cart-update-form"
                                  data-cart-id="{{ $cart->id }}"
                                  data-price="{{ $cart->product->price }}"
                                  data-update-url="{{ route('cart.update', $cart) }}">
                                @csrf @method('PUT')
                                <button type="button"
                                        class="btn-qty w-8 h-8 bg-gray-100 rounded-lg text-gray-700 font-bold hover:bg-gray-200 active:scale-90 transition-all"
                                        data-delta="-1">−</button>
                                <input type="number" name="quantity" value="{{ $cart->quantity }}"
                                       min="1" max="{{ $cart->product->stock }}"
                                       class="qty-input w-14 text-center border border-gray-200 rounded-lg py-1 text-sm font-bold focus:ring-2 focus:ring-green-500 transition-all">
                                <button type="button"
                                        class="btn-qty w-8 h-8 bg-gray-100 rounded-lg text-gray-700 font-bold hover:bg-gray-200 active:scale-90 transition-all"
                                        data-delta="1">+</button>
                            </form>
                        </div>
                        <div class="text-right min-w-0 sm:ml-4">
                            <p class="item-subtotal font-black text-gray-900 text-base transition-all"
                               data-cart-id="{{ $cart->id }}">{{ $cart->formatted_subtotal }}</p>
                        </div>
                    </div>

                    {{-- Tombol hapus --}}
                    <form action="{{ route('cart.remove', $cart) }}" method="POST" class="absolute top-4 right-4 sm:static sm:top-auto sm:right-auto">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-500 transition-colors p-1" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>

                    {{-- Toast error per item --}}
                    <div class="item-error hidden absolute bottom-2 left-4 right-4 text-xs text-red-600 font-bold bg-red-50 border border-red-200 rounded-lg px-3 py-1.5"
                         data-cart-id="{{ $cart->id }}"></div>
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
                    <div class="space-y-3 mb-4" id="summary-list">
                        @foreach($carts as $cart)
                        <div class="flex justify-between text-sm" data-summary-cart-id="{{ $cart->id }}">
                            <span class="summary-name text-gray-500 truncate pr-2">{{ $cart->product->name }} x<span class="summary-qty">{{ $cart->quantity }}</span></span>
                            <span class="summary-subtotal font-medium text-gray-900 whitespace-nowrap">{{ $cart->formatted_subtotal }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="border-t border-gray-100 pt-4 mb-6">
                        <div class="flex justify-between">
                            <span class="font-black text-gray-900">Total</span>
                            <span class="font-black text-green-700 text-xl" id="grand-total">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @if($hasInvalidStock)
                        <div class="mb-4 p-3 bg-red-50 border border-red-100 rounded-xl">
                            <p class="text-xs text-red-600 font-bold flex items-center gap-2 border-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Sebagian stok produk tidak mencukupi atau habis.
                            </p>
                        </div>
                        <button disabled
                           class="w-full block text-center bg-gray-200 text-gray-400 py-3.5 rounded-xl font-bold text-lg cursor-not-allowed">
                            Lanjut Checkout
                        </button>
                    @else
                        <a href="{{ route('checkout.index') }}"
                           class="block text-center bg-green-700 text-white py-3.5 rounded-xl font-bold text-lg hover:bg-green-600 btn-glow transition-all">
                            Lanjut Checkout →
                        </a>
                    @endif
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

<style>
@keyframes flash-green {
    0%   { color: #15803d; transform: scale(1.08); }
    100% { color: inherit; transform: scale(1); }
}
.flash-update { animation: flash-green 0.4s ease-out; }

@keyframes spin-once {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
.qty-loading { opacity: 0.5; pointer-events: none; }
</style>
@endsection

@push('scripts')
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content
        ?? '{{ csrf_token() }}';

    function formatRupiah(amount) {
        return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
    }

    function flashEl(el) {
        el.classList.remove('flash-update');
        void el.offsetWidth; // reflow
        el.classList.add('flash-update');
        setTimeout(() => el.classList.remove('flash-update'), 450);
    }

    function showItemError(cartId, msg) {
        const err = document.querySelector(`.item-error[data-cart-id="${cartId}"]`);
        if (!err) return;
        err.textContent = msg;
        err.classList.remove('hidden');
        setTimeout(() => err.classList.add('hidden'), 3000);
    }

    function updateCartItem(form, newQty) {
        const cartId = form.dataset.cartId;
        const url    = form.dataset.updateUrl;

        // Kunci UI sementara
        const card = form.closest('[data-cart-id]');
        card.classList.add('qty-loading');

        const body = new FormData();
        body.append('_method', 'PUT');
        body.append('_token', CSRF);
        body.append('quantity', newQty);

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
            body: body,
        })
        .then(r => r.json())
        .then(data => {
            card.classList.remove('qty-loading');

            if (!data.success) {
                showItemError(cartId, data.message ?? 'Terjadi kesalahan.');
                // Kembalikan nilai input ke server value (ambil dari form tersimpan)
                form.querySelector('.qty-input').value = form.querySelector('.qty-input').dataset.lastValid ?? 1;
                return;
            }

            // Simpan nilai valid terakhir
            form.querySelector('.qty-input').dataset.lastValid = newQty;

            // Update subtotal item (kanan card)
            const subtotalEl = document.querySelector(`.item-subtotal[data-cart-id="${cartId}"]`);
            if (subtotalEl) {
                subtotalEl.textContent = data.subtotal_fmt;
                flashEl(subtotalEl);
            }

            // Update ringkasan sidebar
            const summaryRow = document.querySelector(`[data-summary-cart-id="${cartId}"]`);
            if (summaryRow) {
                summaryRow.querySelector('.summary-qty').textContent = newQty;
                const sumSub = summaryRow.querySelector('.summary-subtotal');
                if (sumSub) { sumSub.textContent = data.subtotal_fmt; flashEl(sumSub); }
            }

            // Update grand total
            const grandTotalEl = document.getElementById('grand-total');
            if (grandTotalEl) {
                grandTotalEl.textContent = data.grand_total_fmt;
                flashEl(grandTotalEl);
            }
        })
        .catch(() => {
            card.classList.remove('qty-loading');
            showItemError(cartId, 'Gagal terhubung ke server.');
        });
    }

    // Debounce untuk input manual ketik angka
    const debounceTimers = {};

    document.querySelectorAll('.cart-update-form').forEach(form => {
        const input   = form.querySelector('.qty-input');
        const cartId  = form.dataset.cartId;
        input.dataset.lastValid = input.value;

        // Tombol + / –
        form.querySelectorAll('.btn-qty').forEach(btn => {
            btn.addEventListener('click', () => {
                const delta  = parseInt(btn.dataset.delta);
                const max    = parseInt(input.getAttribute('max'));
                let newVal   = parseInt(input.value) + delta;
                if (newVal < 1)   newVal = 1;
                if (newVal > max) newVal = max;
                if (newVal === parseInt(input.value)) return; // tidak berubah
                input.value = newVal;
                updateCartItem(form, newVal);
            });
        });

        // Input angka manual — debounce 600ms
        input.addEventListener('input', () => {
            clearTimeout(debounceTimers[cartId]);
            debounceTimers[cartId] = setTimeout(() => {
                const max   = parseInt(input.getAttribute('max'));
                let newVal  = parseInt(input.value);
                if (!newVal || newVal < 1) { input.value = 1; newVal = 1; }
                if (newVal > max)          { input.value = max; newVal = max; }
                if (newVal === parseInt(input.dataset.lastValid)) return;
                updateCartItem(form, newVal);
            }, 600);
        });
    });
})();
</script>
@endpush
