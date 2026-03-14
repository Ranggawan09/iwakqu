@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="py-12 min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-gray-900 mb-8">Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('checkout.place') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                        <h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs font-black">1</span>
                            Data Pengiriman
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-semibold text-sm mb-1">Nama Penerima *</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors @error('customer_name') border-red-400 @enderror">
                                @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold text-sm mb-1">
                                    No. HP / WhatsApp *
                                    @if(!old('phone') && !empty($lastOrder?->phone))
                                    <span class="ml-1 text-xs font-normal text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">
                                        ✓ dari pesanan sebelumnya
                                    </span>
                                    @endif
                                </label>
                                <input type="text" name="phone"
                                       value="{{ old('phone', $lastOrder?->phone) }}" required
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors @error('phone') border-red-400 @enderror"
                                       placeholder="Contoh: 081234567890">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold text-sm mb-2">
                                    Alamat Lengkap *
                                    @if(!old('address') && !empty($lastOrder?->address))
                                    <span class="ml-1 text-xs font-normal text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">
                                        ✓ dari pesanan sebelumnya
                                    </span>
                                    @endif
                                </label>

                                {{-- Map Pinpoint --}}
                                <div class="mb-3 rounded-2xl overflow-hidden border-2 border-gray-200 relative z-0" style="height: 260px;">
                                    <div id="checkout-map" style="height:100%; width:100%;"></div>
                                    {{-- GPS button --}}
                                    <button type="button" id="btn-my-location"
                                            title="Gunakan lokasi saya"
                                            class="absolute bottom-3 right-3 z-[999] bg-white shadow-lg rounded-xl p-2.5 flex items-center gap-1.5 text-xs font-semibold text-green-700 border border-gray-200 hover:bg-green-50 transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Lokasi Saya
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mb-2 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Seret pin merah ke lokasi Anda. Alamat akan terisi otomatis.
                                </p>

                                {{-- Hidden lat/lng — pre-filled from last order so ongkir works even without moving pin --}}
                                <input type="hidden" name="latitude"  id="input-lat"
                                       value="{{ old('latitude',  $lastOrder?->latitude) }}">
                                <input type="hidden" name="longitude" id="input-lng"
                                       value="{{ old('longitude', $lastOrder?->longitude) }}">

                                {{-- Address textarea --}}
                                <textarea name="address" id="address-textarea" required rows="3"
                                          class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors resize-none @error('address') border-red-400 @enderror"
                                          placeholder="Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos">{{ old('address', $lastOrder?->address) }}</textarea>
                                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                        <h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs font-black">2</span>
                            Ringkasan Pesanan
                        </h2>
                        <div class="space-y-3">
                            @foreach($carts as $cart)
                            <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                                <img src="{{ $cart->product->image_url }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0"
                                     onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=100&q=80'; this.onerror=null;">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $cart->product->name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $cart->product->formatted_price }} x {{ $cart->quantity }}</p>
                                </div>
                                <p class="font-bold text-gray-900">{{ $cart->formatted_subtotal }}</p>
                            </div>
                            @endforeach
                        </div>

                        {{-- Shipping Estimate Panel --}}
                        <div id="shipping-estimate-panel" class="mt-4 hidden">
                            <div class="border border-dashed border-green-300 bg-green-50 rounded-xl p-3 space-y-1.5">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                        Ongkos Kirim
                                        <span class="text-gray-400 text-xs" id="distance-display">-</span>
                                    </span>
                                    <span class="font-bold text-green-700" id="shipping-cost-display"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-black text-gray-900 text-sm">Subtotal Produk</span>
                                    <span class="font-bold text-gray-800 text-sm">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t border-green-200 pt-1.5 flex justify-between items-center">
                                    <span class="font-black text-gray-900">Estimasi Total</span>
                                    <span class="font-black text-green-700 text-lg" id="grand-total-display">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Normal total (hidden when estimate shown) --}}
                        <div id="normal-total-block" class="bg-green-50 rounded-xl p-4 mt-4">
                            <div class="flex justify-between items-center">
                                <span class="font-black text-gray-900 text-lg">Total Pembayaran</span>
                                <span class="font-black text-green-700 text-2xl">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-green-700 text-white py-4 rounded-2xl font-black text-xl btn-glow hover:bg-green-600 transition-all shadow-lg">
                        Bayar Sekarang
                    </button>
                    <p class="text-center text-gray-400 text-xs">Pembayaran diproses dengan aman melalui Mayar. Mendukung Transfer Bank, QRIS, GoPay, OVO, dan metode lainnya.</p>
                </form>
            </div>

            <!-- Info Payment -->
            <div>
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4">Metode Pembayaran</h3>
                    <p class="text-xs text-gray-400 mb-3">Tersedia melalui platform Mayar:</p>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl">
                            <span>🏦</span> <span>Transfer Bank (Virtual Account)</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl">
                            <span>📱</span> <span>GoPay, OVO, Dana, QRIS</span>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-xl">
                            <span>🏪</span> <span>Alfamart</span>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-xl text-xs text-green-700">
                        🔒 Transaksi Anda dilindungi enkripsi SSL 256-bit
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-control-zoom { border: none !important; }
    .leaflet-control-zoom a {
        border-radius: 10px !important;
        border: 1px solid #e5e7eb !important;
        color: #15803d !important;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0,0,0,.10);
    }
    #checkout-map .leaflet-control-zoom { margin: 10px; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    // Koordinat dari pesanan sebelumnya (jika ada)
    var savedLat = {{ $lastOrder?->latitude  ? $lastOrder->latitude  : 'null' }};
    var savedLng = {{ $lastOrder?->longitude ? $lastOrder->longitude : 'null' }};

    var initLat  = savedLat || -2.5;
    var initLng  = savedLng || 118.0;
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
                    // Hanya update jika textarea masih kosong atau belum diedit manual
                    var ta = document.getElementById('address-textarea');
                    if (!ta.dataset.manualEdit) {
                        ta.value = data.display_name;
                    }
                }
            })
            .catch(function () {});
    }

    var subtotal = {{ $total }};

    function fetchShippingEstimate(lat, lng) {
        fetch('/api/shipping-estimate?lat=' + lat + '&lng=' + lng, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var panel      = document.getElementById('shipping-estimate-panel');
            var normalBlk  = document.getElementById('normal-total-block');
            var costEl     = document.getElementById('shipping-cost-display');
            var distEl     = document.getElementById('distance-display');
            var grandEl    = document.getElementById('grand-total-display');
            var orderBtn   = document.querySelector('button[type="submit"]');

            if (data.is_out_of_range) {
                costEl.innerHTML    = '<span class="text-red-600 text-xs">Diluar Jangkauan Maksimal</span>';
                distEl.textContent  = '(' + data.distance_km + ' km)';
                grandEl.textContent = '-';
                panel.classList.remove('hidden');
                normalBlk.classList.add('hidden');
                orderBtn.disabled = true;
                orderBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                orderBtn.classList.remove('bg-green-700', 'hover:bg-green-600', 'btn-glow');
                orderBtn.innerHTML = '🚫 Lokasi di luar jangkauan (' + data.distance_km + ' km)';
            } else if (data.shipping_cost > 0) {
                var grandTotal = subtotal + data.shipping_cost;
                costEl.innerHTML    = 'Rp ' + data.shipping_cost.toLocaleString('id-ID');
                distEl.textContent  = '(' + data.distance_km + ' km)';
                grandEl.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
                panel.classList.remove('hidden');
                normalBlk.classList.add('hidden');
                orderBtn.disabled = false;
                orderBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                orderBtn.classList.add('bg-green-700', 'hover:bg-green-600', 'btn-glow');
                orderBtn.innerHTML = 'Bayar Sekarang';
            } else {
                // Admin location not set yet — keep normal total
                panel.classList.add('hidden');
                normalBlk.classList.remove('hidden');
                orderBtn.disabled = false;
                orderBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                orderBtn.classList.add('bg-green-700', 'hover:bg-green-600', 'btn-glow');
                orderBtn.innerHTML = 'Bayar Sekarang';
            }
        })
        .catch(function () {});
    }

    function updatePosition(lat, lng) {
        setMarker(lat, lng);
        reverseGeocode(lat, lng);
        fetchShippingEstimate(lat, lng);
    }

    // ── Auto-restore dari pesanan sebelumnya ──────────────────────────────────
    if (savedLat && savedLng) {
        // Tempatkan pin tanpa memanggil reverseGeocode (alamat sudah terisi)
        setMarker(savedLat, savedLng);
        // Langsung hitung dan tampilkan estimasi ongkir
        fetchShippingEstimate(savedLat, savedLng);
    }

    // Klik peta untuk pindahkan pin
    map.on('click', function (e) {
        map.setView(e.latlng, Math.max(map.getZoom(), 16));
        updatePosition(e.latlng.lat, e.latlng.lng);
    });

    // Tombol "Lokasi Saya"
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

    // Tandai textarea sudah diedit manual agar tidak ditimpa geocoding
    document.getElementById('address-textarea').addEventListener('input', function () {
        this.dataset.manualEdit = '1';
    });

    // Validasi form: pastikan pin lokasi sudah dipilih sebelum checkout
    document.querySelector('form').addEventListener('submit', function (e) {
        var lat = document.getElementById('input-lat').value;
        var lng = document.getElementById('input-lng').value;
        
        if (!lat || !lng) {
            e.preventDefault();
            alert('Silakan pilih lokasi pengiriman (pin merah pada peta) terlebih dahulu agar pesanan dapat diproses.');
            document.getElementById('checkout-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Check if shipping estimate is loaded and valid (if panel is visible, cost must be valid)
        var panel = document.getElementById('shipping-estimate-panel');
        var orderBtn = document.querySelector('button[type="submit"]');

        if (!panel.classList.contains('hidden')) {
             if (orderBtn.disabled) {
                e.preventDefault();
                alert('Pesanan tidak dapat diproses karena lokasi di luar jangkauan pengiriman.');
                return;
             }
        } else {
             // Jika panel estimate hidden tapi sudah ada lat & lng, berarti masih loading api ongkir
             if (lat && lng) {
                 e.preventDefault();
                 alert('Mohon tunggu sebentar, sedang menghitung ongkos kirim ke lokasi Anda.');
                 return;
             }
        }
    });
})();
</script>
@endpush

