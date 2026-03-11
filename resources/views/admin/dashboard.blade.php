@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm font-medium">Total Pengguna</p>
                <p class="text-4xl font-black text-gray-900 mt-1">{{ $totalUsers }}</p>
                <p class="text-green-600 text-xs mt-1">Pelanggan terdaftar</p>
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center"><span class="text-3xl">👥</span></div>
        </div>
    </div>
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm font-medium">Total Pesanan</p>
                <p class="text-4xl font-black text-gray-900 mt-1">{{ $totalOrders }}</p>
                <p class="text-green-600 text-xs mt-1">Semua status</p>
            </div>
            <div class="w-14 h-14 bg-yellow-50 rounded-2xl flex items-center justify-center"><span class="text-3xl">📦</span></div>
        </div>
    </div>
    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm font-medium">Total Pendapatan</p>
                <p class="text-2xl font-black text-green-700 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <p class="text-green-600 text-xs mt-1">Sudah dibayar & selesai</p>
            </div>
            <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center"><span class="text-3xl">💰</span></div>
        </div>
    </div>
</div>

{{-- 2-column layout: Recent Orders (left) + Shipping Config (right) --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ── Recent Orders (2/3 width) ────────────────────────────────── --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-green-700 text-sm font-medium hover:underline">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Order</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Produk</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lokasi</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tgl</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-bold text-green-700 hover:underline">#{{ $order->id }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-700 text-sm">{{ $order->user->name }}</td>
                        <td class="px-4 py-3">
                            @foreach($order->orderItems as $item)
                                <p class="text-xs text-gray-700 leading-snug">
                                    {{ $item->product->name ?? $item->product_name }}
                                    <span class="text-gray-400 font-medium">x{{ $item->quantity }}</span>
                                </p>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 text-sm whitespace-nowrap">{{ $order->formatted_total }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                @if($order->status === 'selesai') bg-green-100 text-green-700
                                @elseif($order->status === 'dibayar') bg-blue-100 text-blue-700
                                @elseif($order->status === 'diproses') bg-indigo-100 text-indigo-700
                                @elseif($order->status === 'dikirim') bg-orange-100 text-orange-700
                                @elseif($order->status === 'dibatalkan') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700 @endif">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        {{-- Lokasi --}}
                        <td class="px-4 py-3">
                            @if($order->latitude && $order->longitude)
                            <div class="flex">
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->latitude }},{{ $order->longitude }}&travelmode=driving"
                                   target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 bg-green-50 text-green-700 border border-green-100 px-2 py-0.5 rounded-lg text-sm font-semibold hover:bg-green-100 transition-colors whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                                    Rute
                                    @if($order->distance_km)
                                    <span class="text-gray-400 font-normal">({{ $order->distance_km }} km)</span>
                                    @endif
                                </a>
                            </div>
                            @else
                            <span class="text-gray-300 text-xs italic">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">{{ $order->created_at->format('d M') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">Belum ada pesanan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Shipping Config Card (1/3 width) ─────────────────────────── --}}
    <div class="xl:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-700 to-green-600 px-5 py-4">
                <h2 class="font-bold text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                    Pengaturan Pengiriman
                </h2>
                <p class="text-green-200 text-xs mt-0.5">Lokasi toko & tarif ongkir</p>
            </div>

            <div class="p-5">
                @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-3 py-2 rounded-xl flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST" id="shipping-form">
                    @csrf

                    {{-- Hidden coordinates --}}
                    <input type="hidden" name="admin_latitude"  id="admin-lat"  value="{{ $setting['admin_latitude'] }}">
                    <input type="hidden" name="admin_longitude" id="admin-lng"  value="{{ $setting['admin_longitude'] }}">

                    {{-- Map --}}
                    <div class="mb-3 rounded-xl overflow-hidden border-2 border-gray-200 relative" style="height:220px;">
                        <div id="admin-map" style="height:100%;width:100%;"></div>
                        <button type="button" id="btn-admin-gps"
                                class="absolute bottom-2 right-2 z-[999] bg-white shadow rounded-lg px-2.5 py-1.5 text-xs font-semibold text-green-700 border border-gray-200 hover:bg-green-50 flex items-center gap-1 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            GPS Saya
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mb-3 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        Klik peta atau seret pin ke lokasi toko Anda
                    </p>

                    {{-- Address text --}}
                    <div class="mb-3">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Toko</label>
                        <textarea name="admin_address" id="admin-address" rows="2" required
                                  class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-green-500 outline-none resize-none transition-colors"
                                  placeholder="Alamat lengkap toko">{{ $setting['admin_address'] }}</textarea>
                        @error('admin_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Shipping rules --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pengaturan Ongkos Kirim</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] text-gray-500 mb-1">Tarif / km</label>
                                <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden focus-within:border-green-500 transition-colors">
                                    <span class="px-2 py-2 bg-gray-50 text-gray-500 text-xs font-semibold border-r-2 border-gray-200 select-none">Rp</span>
                                    <input type="number" name="shipping_rate_per_km" min="0" step="500" required
                                           value="{{ $setting['shipping_rate_per_km'] ?? 0 }}"
                                           class="flex-1 px-2 py-2 text-xs outline-none bg-white" placeholder="5000">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] text-gray-500 mb-1">Minimal (km)</label>
                                <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden focus-within:border-green-500 transition-colors">
                                    <input type="number" name="min_distance_km" min="0" step="0.1"
                                           value="{{ $setting['min_distance_km'] ?? 0 }}"
                                           class="flex-1 px-2 py-2 text-xs outline-none bg-white w-full" placeholder="3">
                                    <span class="px-2 py-2 bg-gray-50 text-gray-500 text-[11px] font-semibold border-l-2 border-gray-200 select-none">km</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] text-gray-500 mb-1">Maksimal (km)</label>
                                <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden focus-within:border-green-500 transition-colors">
                                    <input type="number" name="max_distance_km" min="0" step="0.1"
                                           value="{{ $setting['max_distance_km'] ?? 0 }}"
                                           class="flex-1 px-2 py-2 text-xs outline-none bg-white w-full"
                                           placeholder="0 = ∞">
                                    <span class="px-2 py-2 bg-gray-50 text-gray-500 text-[11px] font-semibold border-l-2 border-gray-200 select-none">km</span>
                                </div>
                            </div>
                        </div>
                        @error('shipping_rate_per_km') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Current saved location badge --}}
                    @if($setting['admin_latitude'] && $setting['admin_longitude'])
                    <div class="mb-4 text-xs text-gray-500 bg-gray-50 rounded-xl px-3 py-2 flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                        <span>
                            <span class="font-semibold text-gray-700 block">Lokasi tersimpan</span>
                            {{ $setting['admin_latitude'] }}, {{ $setting['admin_longitude'] }}
                        </span>
                    </div>
                    @endif

                    <button type="submit"
                            class="w-full bg-green-700 text-white py-2.5 rounded-xl font-bold text-sm hover:bg-green-600 transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #admin-map .leaflet-control-zoom a {
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        color: #15803d !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var savedLat = {{ $setting['admin_latitude'] ?: 'null' }};
    var savedLng = {{ $setting['admin_longitude'] ?: 'null' }};

    var initLat  = savedLat || -2.5;
    var initLng  = savedLng || 118.0;
    var initZoom = savedLat ? 16 : 5;

    var map = L.map('admin-map', { zoomControl: true }).setView([initLat, initLng], initZoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19
    }).addTo(map);

    var pinIcon = L.divIcon({
        html: '<div style="position:relative;width:28px;height:38px;"><svg viewBox="0 0 32 42" xmlns="http://www.w3.org/2000/svg"><path d="M16 0C9.37 0 4 5.37 4 12c0 9 12 30 12 30S28 21 28 12C28 5.37 22.63 0 16 0z" fill="#15803d"/><circle cx="16" cy="12" r="6" fill="white"/></svg></div>',
        iconSize: [28, 38], iconAnchor: [14, 38], className: ''
    });

    var marker = null;

    function setMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { icon: pinIcon, draggable: true }).addTo(map);
            marker.on('dragend', function () {
                var p = marker.getLatLng();
                updatePosition(p.lat, p.lng);
            });
        }
        document.getElementById('admin-lat').value = lat.toFixed(7);
        document.getElementById('admin-lng').value = lng.toFixed(7);
    }

    function reverseGeocode(lat, lng) {
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=id')
            .then(function (r) { return r.json(); })
            .then(function (d) {
                var el = document.getElementById('admin-address');
                if (d && d.display_name && !el.dataset.manual) {
                    el.value = d.display_name;
                }
            }).catch(function () {});
    }

    function updatePosition(lat, lng) {
        setMarker(lat, lng);
        reverseGeocode(lat, lng);
    }

    // Restore saved pin
    if (savedLat && savedLng) setMarker(savedLat, savedLng);

    // Click on map
    map.on('click', function (e) {
        map.setView(e.latlng, Math.max(map.getZoom(), 16));
        updatePosition(e.latlng.lat, e.latlng.lng);
    });

    // GPS button
    document.getElementById('btn-admin-gps').addEventListener('click', function () {
        if (!navigator.geolocation) return;
        var btn = this;
        btn.disabled = true;
        navigator.geolocation.getCurrentPosition(function (pos) {
            var lat = pos.coords.latitude, lng = pos.coords.longitude;
            map.setView([lat, lng], 17);
            updatePosition(lat, lng);
            btn.disabled = false;
        }, function () { btn.disabled = false; }, { timeout: 10000 });
    });

    document.getElementById('admin-address').addEventListener('input', function () {
        this.dataset.manual = '1';
    });
})();
</script>
@endpush
