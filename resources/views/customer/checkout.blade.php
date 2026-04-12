@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
    <div class="py-12 min-h-screen bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-black text-gray-900 mb-8">Checkout</h1>

            {{-- Flash Alert (ditampilkan via JS) --}}
            <div id="checkout-flash-alert" class="hidden mb-6 rounded-2xl p-4 flex items-start gap-3 shadow-md transition-all duration-300" role="alert">
                <div id="flash-icon" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white text-base font-black"></div>
                <p id="flash-message" class="flex-1 text-sm font-semibold leading-snug"></p>
                <button type="button" onclick="hideFlashAlert()" class="flex-shrink-0 ml-auto text-lg leading-none opacity-60 hover:opacity-100 transition-opacity">&times;</button>
            </div>
            <form id="checkout-form" action="{{ route('checkout.place') }}" method="POST"
                class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                @csrf

                <!-- Pesan Operasional (JIKA TUTUP) -->
                @if(!$op['is_open'])
                    <div
                        class="lg:col-span-12 bg-red-50 border-2 border-red-200 rounded-2xl p-4 sm:p-6 mb-4 flex items-start gap-4">
                        <div
                            class="w-12 h-12 bg-red-500 text-white rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-red-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-red-900">MOHON MAAF, PELAYANAN ONLINE SEDANG TUTUP</h3>
                            <p class="text-red-700 mt-1 font-medium leading-relaxed">
                                {{ $op['message'] }}
                            </p>
                            <p class="text-red-600 text-xs mt-2 italic font-bold uppercase tracking-wider">Anda tidak dapat
                                melanjutkan pembayaran saat ini.</p>
                        </div>
                    </div>
                @endif

                <!-- Kolom Kiri: Data Pengiriman -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100">
                        <h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                            <span
                                class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs font-black">1</span>
                            Data Pengiriman
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-semibold text-sm mb-1">Nama Penerima *</label>
                                <input type="text" name="customer_name"
                                    value="{{ old('customer_name', auth()->user()->name) }}" required
                                    class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors @error('customer_name') border-red-400 @enderror">
                                @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold text-sm mb-1">
                                    No. HP / WhatsApp *
                                    @if(!old('phone') && !empty($lastOrder?->phone))
                                        <span
                                            class="ml-1 text-[10px] sm:text-xs font-normal text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full inline-block mt-1 sm:mt-0">
                                            ✓ dari pesanan sebelumnya
                                        </span>
                                    @endif
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $lastOrder?->phone) }}" required
                                    pattern="[0-9]+" minlength="10" maxlength="20" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors @error('phone') border-red-400 @enderror"
                                    placeholder="Contoh: 081234567890">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold text-sm mb-2">
                                    Alamat Lengkap *
                                    @if(!old('address') && !empty($lastOrder?->address))
                                        <span
                                            class="ml-1 text-[10px] sm:text-xs font-normal text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full inline-block mt-1 sm:mt-0">
                                            ✓ dari pesanan sebelumnya
                                        </span>
                                    @endif
                                </label>

                                {{-- Map Pinpoint --}}
                                <div class="mb-3 rounded-2xl overflow-hidden border-2 border-gray-200 relative z-0"
                                    style="height: 260px;">
                                    <div id="checkout-map" style="height:100%; width:100%;"></div>
                                </div>
                                <p class="text-xs text-gray-400 mb-2 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500 flex-shrink-0"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Seret pin merah ke lokasi Anda. Alamat akan terisi otomatis.
                                </p>

                                {{-- Hidden lat/lng --}}
                                <input type="hidden" name="latitude" id="input-lat"
                                    value="{{ old('latitude', $lastOrder?->latitude) }}">
                                <input type="hidden" name="longitude" id="input-lng"
                                    value="{{ old('longitude', $lastOrder?->longitude) }}">

                                {{-- GPS Button --}}
                                <button type="button" id="btn-my-location"
                                    class="w-full mb-2 flex items-center justify-center gap-2 bg-green-50 hover:bg-green-100 active:scale-95 border-2 border-green-200 text-green-700 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Gunakan Lokasi Saya
                                </button>

                                {{-- Address textarea --}}
                                <textarea name="address" id="address-textarea" required rows="3"
                                    class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors resize-none @error('address') border-red-400 @enderror"
                                    placeholder="Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos">{{ old('address', $lastOrder?->address) }}</textarea>
                                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Ringkasan Pesanan & Tombol Bayar -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6 border border-gray-100 sticky top-24">
                        <h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                            <span
                                class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs font-black">2</span>
                            Ringkasan Pesanan
                        </h2>
                        <div class="space-y-3">
                            @foreach($carts as $cart)
                                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                                    <img src="{{ $cart->product->image_url }}"
                                        class="w-12 h-12 rounded-lg object-cover flex-shrink-0"
                                        onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=100&q=80'; this.onerror=null;">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $cart->product->name }}</p>
                                        <p class="text-gray-400 text-xs">{{ $cart->product->formatted_price }} x
                                            {{ $cart->quantity }}
                                        </p>
                                    </div>
                                    <p class="font-bold text-gray-900 text-sm sm:text-base whitespace-nowrap">
                                        {{ $cart->formatted_subtotal }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Global Discount Info --}}
                        @if($globalDiscount['active'])
                            <div class="mt-4 p-3 bg-green-50 border border-green-100 rounded-xl">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-green-700 font-bold flex items-center gap-1">
                                        Diskon Toko (Otomatis)
                                    </span>
                                    <span class="font-black text-green-700" id="global-discount-display">
                                        -Rp {{ number_format($globalDiscountAmount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-green-600 mt-0.5 italic">Potongan
                                    {{ $globalDiscount['type'] === 'percent' ? $globalDiscount['value'] . '%' : 'Rp ' . number_format($globalDiscount['value'], 0, ',', '.') }}
                                    untuk {{ $globalDiscount['target'] === 'subtotal' ? 'produk' : 'ongkir' }}.
                                </p>
                            </div>
                        @endif

                        {{-- Voucher Input --}}
                        <div class="mt-6 border-t border-gray-100 pt-6">
                            <label class="block text-xs font-black text-gray-400 uppercase mb-2">Punya Kode Voucher?</label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" id="voucher-input" name="voucher_code"
                                    class="flex-1 border-2 border-gray-100 rounded-xl px-4 py-2.5 text-sm font-bold focus:border-green-500 outline-none uppercase placeholder:normal-case"
                                    placeholder="Masukkan kode voucher...">
                                <button type="button" id="btn-apply-voucher"
                                    class="w-full sm:w-auto bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-green-600 active:scale-95 transition-all">
                                    Pakai
                                </button>
                            </div>
                            <div id="voucher-message" class="hidden mt-2 text-[10px] font-bold"></div>
                        </div>

                        {{-- Shipping Estimate Panel --}}
                        <div id="shipping-estimate-panel" class="mt-4 hidden">
                            <div class="border border-dashed border-green-300 bg-green-50 rounded-xl p-3 space-y-1.5">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                        Ongkir
                                        <span class="text-gray-400 text-[10px] sm:text-xs" id="distance-display">-</span>
                                    </span>
                                    <span class="font-bold text-green-700 mt-1 sm:mt-0 whitespace-nowrap text-right"
                                        id="shipping-cost-display"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-black text-gray-900 text-sm">Subtotal</span>
                                    <span class="font-bold text-gray-800 text-sm whitespace-nowrap">Rp
                                        {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                <div id="voucher-discount-row" class="hidden justify-between items-center text-sm py-1">
                                    <span class="text-green-700 font-bold flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h1.17A3.001 3.001 0 015 5zm4.553 2.236A1 1 0 0010 7v3.586l.293.293a1 1 0 001.414-1.414l-2-2a1 1 0 00-1.414 0l-2 2a1 1 0 001.414 1.414l.293-.293V7a1 1 0 00.553-.894z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Voucher (<span id="voucher-code-label"></span>)
                                    </span>
                                    <span class="font-bold text-green-700" id="voucher-discount-display"></span>
                                </div>

                                <div class="border-t border-green-200 pt-1.5 flex justify-between items-center">
                                    <span class="font-black text-gray-900">Total</span>
                                    <span class="font-black text-green-700 text-lg whitespace-nowrap"
                                        id="grand-total-display">Rp
                                        {{ number_format($total - $globalDiscountAmount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Normal total (hidden when estimate shown) --}}
                        <div id="normal-total-block" class="bg-green-50 rounded-xl p-3 sm:p-4 mt-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-black text-gray-900 text-sm">Total Pembayaran</span>
                                    <span class="font-black text-green-700 text-xl sm:text-2xl" id="normal-total-display">
                                        Rp {{ number_format($total - $globalDiscountAmount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" @if(!$op['is_open']) disabled @endif
                            class="w-full {{ $op['is_open'] ? 'bg-green-700 hover:bg-green-600 btn-glow' : 'bg-gray-400 cursor-not-allowed opacity-60' }} text-white py-3.5 sm:py-4 mt-6 rounded-2xl font-black text-lg sm:text-xl transition-all shadow-lg">
                            {{ $op['is_open'] ? 'Bayar Sekarang' : 'Toko Sedang Tutup' }}
                        </button>
                        <p class="text-center text-gray-400 text-[10px] sm:text-xs mt-3">Pembayaran diproses dengan aman
                            melalui Mayar.</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .leaflet-control-zoom {
            border: none !important;
        }

        .leaflet-control-zoom a {
            border-radius: 10px !important;
            border: 1px solid #e5e7eb !important;
            color: #15803d !important;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .10);
        }

        #checkout-map .leaflet-control-zoom {
            margin: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            // Koordinat dari pesanan sebelumnya (jika ada)
            var savedLat = {!! isset($lastOrder->latitude) ? $lastOrder->latitude : 'null' !!};
            var savedLng = {!! isset($lastOrder->longitude) ? $lastOrder->longitude : 'null' !!};

            var initLat = savedLat || -2.5;
            var initLng = savedLng || 118.0;
            var initZoom = savedLat ? 16 : 5;

            var map = L.map('checkout-map', { zoomControl: true }).setView([initLat, initLng], initZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(map);

            // Custom red pin icon
            var pinIcon = L.divIcon({
                html: `<div style="position:relative;width:32px;height:42px;">
                                     <svg viewBox="0 0 32 42" xmlns="http://www.w3.org/2000/svg">
                                       <path d="M16 0C9.37 0 4 5.37 4 12c0 9 12 30 12 30S28 21 28 12C28 5.37 22.63 0 16 0z" fill="#ef4444"/>
                                       <circle cx="16" cy="12" r="6" fill="white"/>
                                     </svg>
                                   </div>`,
                iconSize: [32, 42],
                iconAnchor: [16, 42],
                className: '',
            });

            var marker = null;

            function setMarker(lat, lng) {
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { icon: pinIcon, draggable: true }).addTo(map);
                    marker.on('dragend', function (e) {
                        var pos = marker.getLatLng();
                        updatePosition(pos.lat, pos.lng);
                    });
                }
                document.getElementById('input-lat').value = lat.toFixed(7);
                document.getElementById('input-lng').value = lng.toFixed(7);
            }

            function reverseGeocode(lat, lng) {
                var url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&addressdetails=1&accept-language=id';
                fetch(url, { headers: { 'Accept-Language': 'id' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data && data.display_name) {
                            var ta = document.getElementById('address-textarea');
                            if (!ta.dataset.manualEdit) {
                                ta.value = data.display_name;
                            }
                        }
                    })
                    .catch(function () { });
            }

            var subtotal = {{ $total }};
            var currentShipping = 0;
            var appliedVoucher = null;

            var globalDiscount = {
                active: {{ $globalDiscount['active'] ? 'true' : 'false' }},
                type: '{{ $globalDiscount["type"] }}',
                target: '{{ $globalDiscount["target"] }}',
                value: {{ $globalDiscount["value"] }},
            };

            function recalculateTotal() {
                var globalDiscAmount = 0;
                if (globalDiscount.active) {
                    var targetVal = (globalDiscount.target === 'subtotal') ? subtotal : currentShipping;
                    if (globalDiscount.type === 'percent') {
                        globalDiscAmount = targetVal * (globalDiscount.value / 100);
                    } else {
                        globalDiscAmount = Math.min(globalDiscount.value, targetVal);
                    }
                }

                var voucherDiscAmount = 0;
                if (appliedVoucher) {
                    var targetVal = (appliedVoucher.target === 'subtotal') ? subtotal : currentShipping;
                    if (appliedVoucher.type === 'percent') {
                        voucherDiscAmount = targetVal * (appliedVoucher.value / 100);
                        if (appliedVoucher.max_discount > 0) {
                            voucherDiscAmount = Math.min(voucherDiscAmount, appliedVoucher.max_discount);
                        }
                    } else {
                        voucherDiscAmount = Math.min(appliedVoucher.value, targetVal);
                    }
                }

                var grandTotal = subtotal + currentShipping - globalDiscAmount - voucherDiscAmount;
                if (grandTotal < 0) grandTotal = 0;

                if (globalDiscount.active) {
                    var gEl = document.getElementById('global-discount-display');
                    if (gEl) gEl.textContent = '-Rp ' + Math.ceil(globalDiscAmount).toLocaleString('id-ID');
                }

                if (appliedVoucher) {
                    document.getElementById('voucher-discount-row').classList.remove('hidden');
                    document.getElementById('voucher-discount-row').classList.add('flex');
                    document.getElementById('voucher-code-label').textContent = appliedVoucher.code;
                    document.getElementById('voucher-discount-display').textContent = '-Rp ' + Math.ceil(voucherDiscAmount).toLocaleString('id-ID');
                } else {
                    document.getElementById('voucher-discount-row').classList.add('hidden');
                    document.getElementById('voucher-discount-row').classList.remove('flex');
                }

                document.getElementById('grand-total-display').textContent = 'Rp ' + Math.ceil(grandTotal).toLocaleString('id-ID');
                document.getElementById('normal-total-display').textContent = 'Rp ' + Math.ceil(grandTotal).toLocaleString('id-ID');
            }

            function fetchShippingEstimate(lat, lng) {
                fetch('/api/shipping-estimate?lat=' + lat + '&lng=' + lng, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        var panel = document.getElementById('shipping-estimate-panel');
                        var normalBlk = document.getElementById('normal-total-block');
                        var costEl = document.getElementById('shipping-cost-display');
                        var distEl = document.getElementById('distance-display');
                        var orderBtn = document.querySelector('button[type="submit"]');

                        if (data.is_out_of_range) {
                            costEl.innerHTML = '<span class="text-red-600 text-xs">Diluar Jangkauan Maksimal</span>';
                            distEl.textContent = '(' + data.distance_km + ' km)';
                            panel.classList.remove('hidden');
                            normalBlk.classList.add('hidden');
                            orderBtn.disabled = true;
                            orderBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                            orderBtn.classList.remove('bg-green-700', 'hover:bg-green-600', 'btn-glow');
                            orderBtn.innerHTML = '🚫 Lokasi di luar jangkauan (' + data.distance_km + ' km)';
                            currentShipping = 0;
                        } else if (data.shipping_cost > 0) {
                            currentShipping = data.shipping_cost;
                            costEl.innerHTML = 'Rp ' + data.shipping_cost.toLocaleString('id-ID');
                            distEl.textContent = '(' + data.distance_km + ' km)';
                            panel.classList.remove('hidden');
                            normalBlk.classList.add('hidden');
                            orderBtn.disabled = false;
                            orderBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                            orderBtn.classList.add('bg-green-700', 'hover:bg-green-600', 'btn-glow');
                            orderBtn.innerHTML = 'Bayar Sekarang';
                        } else {
                            currentShipping = 0;
                            panel.classList.add('hidden');
                            normalBlk.classList.remove('hidden');
                            orderBtn.disabled = false;
                            orderBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                            orderBtn.classList.add('bg-green-700', 'hover:bg-green-600', 'btn-glow');
                            orderBtn.innerHTML = 'Bayar Sekarang';
                        }
                        recalculateTotal();
                    })
                    .catch(function () { });
            }

            document.getElementById('btn-apply-voucher').addEventListener('click', function () {
                var codeInput = document.getElementById('voucher-input');
                var code = codeInput.value.trim();
                var msgEl = document.getElementById('voucher-message');
                var btn = this;
                if (!code) return;
                btn.disabled = true;
                btn.textContent = '...';
                msgEl.className = 'mt-2 text-[10px] font-bold text-gray-400';
                msgEl.textContent = 'Mengecek voucher...';
                msgEl.classList.remove('hidden');
                fetch('{{ route('vouchers.apply') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ code: code })
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        btn.disabled = false;
                        btn.textContent = 'Pakai';
                        if (data.success) {
                            appliedVoucher = data.voucher;
                            msgEl.className = 'mt-2 text-[10px] font-bold text-green-600';
                            msgEl.textContent = data.message;
                            recalculateTotal();
                        } else {
                            appliedVoucher = null;
                            msgEl.className = 'mt-2 text-[10px] font-bold text-red-600';
                            msgEl.textContent = data.message;
                            recalculateTotal();
                        }
                    })
                    .catch(function () {
                        btn.disabled = false;
                        btn.textContent = 'Pakai';
                        msgEl.className = 'mt-2 text-[10px] font-bold text-red-600';
                        msgEl.textContent = 'Terjadi kesalahan sistem.';
                    });
            });

            function updatePosition(lat, lng) {
                setMarker(lat, lng);
                reverseGeocode(lat, lng);
                fetchShippingEstimate(lat, lng);
            }

            if (savedLat && savedLng) {
                setMarker(savedLat, savedLng);
                fetchShippingEstimate(savedLat, savedLng);
            }

            map.on('click', function (e) {
                map.setView(e.latlng, Math.max(map.getZoom(), 16));
                updatePosition(e.latlng.lat, e.latlng.lng);
            });

            document.getElementById('btn-my-location').addEventListener('click', function () {
                if (!navigator.geolocation) {
                    alert('Browser Anda tidak mendukung geolokasi.');
                    return;
                }
                this.disabled = true;
                this.innerHTML = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Mencari...';
                var btn = this;
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        var lat = pos.coords.latitude, lng = pos.coords.longitude;
                        map.setView([lat, lng], 17);
                        updatePosition(lat, lng);
                        btn.disabled = false;
                        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Lokasi Saya';
                    },
                    function () {
                        alert('Tidak dapat mengakses lokasi. Pastikan izin lokasi diaktifkan.');
                        btn.disabled = false;
                        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Lokasi Saya';
                    },
                    { timeout: 10000 }
                );
            });

            document.getElementById('address-textarea').addEventListener('input', function () {
                this.dataset.manualEdit = '1';
            });

            // ── Flash Alert Helpers ──────────────────────────────────────────
            window.hideFlashAlert = function () {
                var el = document.getElementById('checkout-flash-alert');
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                setTimeout(function () { el.classList.add('hidden'); el.style.opacity = ''; el.style.transform = ''; }, 300);
            };

            window.showFlashAlert = function (msg, type) {
                // type: 'error' | 'warning' | 'info'
                var el      = document.getElementById('checkout-flash-alert');
                var iconEl  = document.getElementById('flash-icon');
                var msgEl   = document.getElementById('flash-message');
                var configs = {
                    error:   { bg: 'bg-red-50',    border: 'border-2 border-red-200',   textColor: 'text-red-800',    iconBg: 'bg-red-500',    icon: '✕' },
                    warning: { bg: 'bg-amber-50',  border: 'border-2 border-amber-200', textColor: 'text-amber-900',  iconBg: 'bg-amber-400',  icon: '!' },
                    info:    { bg: 'bg-blue-50',   border: 'border-2 border-blue-200',  textColor: 'text-blue-800',   iconBg: 'bg-blue-500',   icon: 'i' },
                };
                var cfg = configs[type] || configs.info;

                // Reset classes
                el.className = 'mb-6 rounded-2xl p-4 flex items-start gap-3 shadow-md transition-all duration-300 ' + cfg.bg + ' ' + cfg.border + ' ' + cfg.textColor;
                iconEl.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-black ' + cfg.iconBg;
                iconEl.textContent = cfg.icon;
                msgEl.textContent  = msg;

                el.style.opacity   = '0';
                el.style.transform = 'translateY(-8px)';
                el.classList.remove('hidden');

                // Animate in
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        el.style.opacity   = '1';
                        el.style.transform = 'translateY(0)';
                    });
                });

                // Auto dismiss after 5s
                clearTimeout(el._dismissTimer);
                el._dismissTimer = setTimeout(hideFlashAlert, 5000);

                // Scroll ke alert
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            };

            document.getElementById('checkout-form').addEventListener('submit', function (e) {
                var lat = document.getElementById('input-lat').value;
                var lng = document.getElementById('input-lng').value;
                if (!lat || !lng) {
                    e.preventDefault();
                    showFlashAlert('📍 Silakan pilih lokasi pengiriman terlebih dahulu — seret pin merah pada peta ke lokasi Anda.', 'error');
                    // Highlight map border
                    var mapWrap = document.getElementById('checkout-map').parentElement;
                    mapWrap.classList.add('border-red-400');
                    mapWrap.classList.remove('border-gray-200');
                    setTimeout(function () {
                        mapWrap.classList.remove('border-red-400');
                        mapWrap.classList.add('border-gray-200');
                    }, 3000);
                    document.getElementById('checkout-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
                var panel    = document.getElementById('shipping-estimate-panel');
                var orderBtn = document.querySelector('button[type="submit"]');
                if (!panel.classList.contains('hidden')) {
                    if (orderBtn.disabled) {
                        e.preventDefault();
                        showFlashAlert('🚫 Pesanan tidak dapat diproses karena lokasi Anda di luar jangkauan pengiriman.', 'error');
                        return;
                    }
                } else {
                    // Panel ongkir hidden = estimasi belum tampil, blok submit
                    e.preventDefault();
                    showFlashAlert('⏳ Mohon tunggu sebentar, estimasi ongkos kirim sedang dihitung. Coba geser pin lokasi Anda jika tidak ada respon.', 'warning');
                    document.getElementById('checkout-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
            });
        })();
    </script>
@endpush