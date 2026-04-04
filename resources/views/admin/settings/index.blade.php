@extends('layouts.admin')
@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Left Column: Shipping & Location ────────────────────────── --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-700 to-green-600 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
                        Lokasi Toko & Pengiriman
                    </h2>
                    <p class="text-green-100 text-xs mt-0.5">Atur titik GPS toko dan tarif ongkos kirim</p>
                </div>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Map Side --}}
                    <div>
                        <div class="mb-3 rounded-xl overflow-hidden border-2 border-gray-200 relative" style="height:280px;">
                            <div id="admin-map" style="height:100%;width:100%;"></div>
                            <button type="button" id="btn-admin-gps"
                                    class="absolute bottom-2 right-2 z-[999] bg-white shadow-lg rounded-lg px-3 py-2 text-xs font-bold text-green-700 border border-gray-100 hover:bg-green-50 flex items-center gap-1.5 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Gunakan GPS Saya
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400 flex items-start gap-1.5 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            Klik pada peta atau seret pin hijau ke lokasi tepat toko Anda untuk akurasi perhitungan jarak ongkir.
                        </p>
                    </div>

                    {{-- Form Side --}}
                    <div class="space-y-4">
                        <input type="hidden" name="admin_latitude"  id="admin-lat"  value="{{ $setting['admin_latitude'] ?? '' }}">
                        <input type="hidden" name="admin_longitude" id="admin-lng"  value="{{ $setting['admin_longitude'] ?? '' }}">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Alamat Lengkap Toko</label>
                            <textarea name="admin_address" id="admin-address" rows="3" required
                                      class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none resize-none transition-all"
                                      placeholder="Contoh: Jl. Ikan Gurami No. 123, Kota Malang">{{ $setting['admin_address'] ?? '' }}</textarea>
                            @error('admin_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-2">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Konfigurasi Ongkos Kirim</label>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <label class="block text-[11px] text-gray-500 mb-1 font-semibold uppercase tracking-wider">Tarif per KM</label>
                                        <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden focus-within:border-green-500 transition-all bg-white">
                                            <span class="px-3 py-2.5 bg-gray-50 text-gray-500 text-sm font-bold border-r-2 border-gray-200">Rp</span>
                                            <input type="number" name="shipping_rate_per_km" min="0" step="100" required
                                                   value="{{ $setting['shipping_rate_per_km'] ?? 0 }}"
                                                   class="w-full px-3 py-2.5 text-sm outline-none font-bold">
                                        </div>
                                    </div>
                                    <div class="w-24">
                                        <label class="block text-[11px] text-gray-500 mb-1 font-semibold uppercase tracking-wider">Min (KM)</label>
                                        <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden focus-within:border-green-500 transition-all bg-white text-center">
                                            <input type="number" name="min_distance_km" min="0" step="0.1"
                                                   value="{{ $setting['min_distance_km'] ?? 0 }}"
                                                   class="w-full px-2 py-2.5 text-sm outline-none text-center font-bold">
                                        </div>
                                    </div>
                                    <div class="w-24">
                                        <label class="block text-[11px] text-gray-500 mb-1 font-semibold uppercase tracking-wider">Maks (KM)</label>
                                        <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden focus-within:border-green-500 transition-all bg-white text-center">
                                            <input type="number" name="max_distance_km" min="0" step="0.1"
                                                   value="{{ $setting['max_distance_km'] ?? 0 }}"
                                                   class="w-full px-2 py-2.5 text-sm outline-none text-center font-bold">
                                        </div>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 italic">* Maksimal 0 berarti tidak ada batasan jarak (unlimited).</p>
                            </div>
                        </div>


                    </div>
                </div>

                {{-- Operational Hours Section inside the same form for simplicity or separate it --}}
                <div class="mt-10 pt-10 border-t border-gray-100">
                    <h3 class="text-gray-900 font-bold flex items-center gap-2 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/><path d="M13 7h-2v6h6v-2h-4z"/></svg>
                        Jam Operasional Toko
                    </h3>

                    @php
                        $activeDays = json_decode($setting['operational_days'] ?? '[]', true);
                        $days = [
                            'Monday'    => 'Senin',
                            'Tuesday'   => 'Selasa',
                            'Wednesday' => 'Rabu',
                            'Thursday'  => 'Kamis',
                            'Friday'    => 'Jumat',
                            'Saturday'  => 'Sabtu',
                            'Sunday'    => 'Minggu'
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-4">Hari Operasional</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach($days as $key => $label)
                                <label class="cursor-pointer group">
                                    <input type="checkbox" name="operational_days[]" value="{{ $key }}"
                                           {{ in_array($key, $activeDays) ? 'checked' : '' }}
                                           class="hidden peer">
                                    <div class="px-3 py-2.5 text-xs font-bold text-center border-2 border-gray-100 rounded-xl peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 text-gray-400 transition-all group-hover:border-gray-200">
                                        {{ $label }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('operational_days') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-gray-700 mb-3">Jam Buka</label>
                                <input type="time" name="open_time" required
                                       value="{{ $setting['open_time'] ?? '17:00' }}"
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:border-green-500 outline-none transition-all">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-gray-700 mb-3">Jam Tutup</label>
                                <input type="time" name="close_time" required
                                       value="{{ $setting['close_time'] ?? '21:00' }}"
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:border-green-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 bg-orange-50 border border-orange-100 rounded-2xl p-4 flex items-start gap-4">
                        <div class="w-10 h-10 bg-orange-500 text-white rounded-xl flex items-center justify-center flex-shrink-0 animate-pulse">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-orange-800">Catatan Penting</p>
                            <p class="text-xs text-orange-700 mt-1 leading-relaxed">
                                Pelanggan **hanya** bisa melakukan checkout pesanan pada hari dan jam yang aktif di atas. Di luar waktu tersebut, sistem akan melarang proses checkout dengan pesan pemberitahuan.
                            </p>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <button type="submit"
                                class="w-full bg-green-700 text-white py-4 rounded-2xl font-black text-sm hover:bg-green-600 transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-100 hover:shadow-green-200 active:scale-[0.98]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </div>
            </form>
        </div>


    <div class="space-y-6">
        {{-- Manajemen Voucher --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-700 to-green-700 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12.75 3.75a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0v-1.5ZM7.5 5.25a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H7.5ZM16.5 5.25a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5ZM5.25 7.5a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0V7.5ZM18.75 7.5a.75.75 0 0 0 1.5 0v1.5a.75.75 0 0 0-1.5 0V7.5ZM3.75 12.75a.75.75 0 1 1 0-1.5 1.5 1.5 0 0 1 1.5 1.5h-1.5ZM18.75 12.75a1.5 1.5 0 0 1 1.5-1.5 1.5 1.5 0 0 1 0 3h-1.5a1.5 1.5 0 0 1-1.5-1.5h1.5ZM4.5 15.75a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0v-1.5ZM15.75 18.75a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5ZM11.25 18.75a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5ZM7.5 18.75a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5H7.5ZM15.75 4.5a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0V4.5ZM15.75 4.5v1.5M10.5 8.25a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1-.75-.75v-1.5Z" /></svg>
                        Manajemen Voucher
                    </h2>
                </div>
            </div>
            <div class="p-6">
                {{-- Form Tambah Voucher --}}
                <form action="{{ route('admin.vouchers.store') }}" method="POST" class="bg-gray-50 rounded-2xl p-4 border border-gray-100 mb-6">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-black text-gray-500 uppercase mb-1">Kode</label>
                            <input type="text" name="code" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm uppercase font-bold focus:border-green-500 outline-none" placeholder="DISKON10">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 uppercase mb-1">Tipe</label>
                                <select name="type" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-green-500 outline-none">
                                    <option value="percent">%</option>
                                    <option value="fixed">Rp</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 uppercase mb-1">Potongan</label>
                                <input type="number" name="value" required class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-green-500 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 uppercase mb-1">Target</label>
                                <select name="target" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-green-500 outline-none">
                                    <option value="subtotal">Produk</option>
                                    <option value="shipping">Ongkir</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-gray-500 uppercase mb-1">Min. Belanja</label>
                                <input type="number" name="min_purchase" value="0" class="w-full border-2 border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-green-500 outline-none">
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer pt-1">
                            <input type="checkbox" name="is_single_use" value="1" class="w-4 h-4 text-green-700 border-gray-300 rounded focus:ring-green-500">
                            <span class="text-xs font-bold text-gray-700">Sekali Pakai Per User</span>
                        </label>
                        <button type="submit" class="w-full bg-green-700 text-white py-2.5 rounded-xl font-bold text-sm hover:bg-green-600 transition-colors">
                            TAMBAH VOUCHER
                        </button>
                    </div>
                </form>

                {{-- Daftar Voucher (Compact) --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="pb-3 text-[10px] font-black text-gray-400 uppercase">Voucher</th>
                                <th class="pb-3 text-[10px] font-black text-gray-400 uppercase text-center">Status</th>
                                <th class="pb-3 text-[10px] font-black text-gray-400 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($vouchers as $v)
                            <tr>
                                <td class="py-3">
                                    <div class="flex flex-col">
                                        <span class="font-black text-gray-900 text-xs tracking-widest">{{ $v->code }}</span>
                                        <span class="text-[9px] text-gray-500 uppercase font-bold">
                                            {{ $v->type === 'percent' ? $v->value . '%' : 'Rp ' . number_format($v->value, 0, ',', '.') }}
                                            ({{ $v->target === 'subtotal' ? 'Prd' : 'Ong' }})
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <form action="{{ route('admin.vouchers.toggle', $v->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="relative inline-flex h-4 w-8 flex-shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none {{ $v->is_active ? 'bg-green-500' : 'bg-gray-200' }}">
                                            <span class="inline-block h-3 w-3 transform rounded-full bg-white shadow transition duration-200 {{ $v->is_active ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 text-right">
                                    <form action="{{ route('admin.vouchers.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-400 hover:text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-[10px] text-gray-400 italic">Kosong</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Diskon Global --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-pink-700 to-pink-600 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8Z"/><path d="M12 7a1 1 0 0 0-1 1v2.58l-1.29 1.3a1 1 0 0 0 1.42 1.42l2-2A1 1 0 0 0 13 10V8a1 1 0 0 0-1-1Z"/></svg>
                        Diskon Global
                    </h2>
                </div>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="admin_latitude" value="{{ $setting['admin_latitude'] ?? '' }}">
                <input type="hidden" name="admin_longitude" value="{{ $setting['admin_longitude'] ?? '' }}">
                <input type="hidden" name="admin_address" value="{{ $setting['admin_address'] ?? '' }}">
                <input type="hidden" name="shipping_rate_per_km" value="{{ $setting['shipping_rate_per_km'] ?? '0' }}">
                <input type="hidden" name="min_distance_km" value="{{ $setting['min_distance_km'] ?? '0' }}">
                <input type="hidden" name="max_distance_km" value="{{ $setting['max_distance_km'] ?? '0' }}">
                @foreach(json_decode($setting['operational_days'] ?? '[]', true) as $day)
                    <input type="hidden" name="operational_days[]" value="{{ $day }}">
                @endforeach
                <input type="hidden" name="open_time" value="{{ $setting['open_time'] ?? '17:00' }}">
                <input type="hidden" name="close_time" value="{{ $setting['close_time'] ?? '21:00' }}">

                <div class="space-y-4">
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <span class="text-xs font-black text-gray-700 uppercase">Aktifkan</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="global_discount_active" value="1" {{ ($setting['global_discount_active'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Target</label>
                            <select name="global_discount_target" class="w-full border-2 border-gray-100 rounded-xl px-2 py-2 text-xs font-bold focus:border-pink-500 outline-none">
                                <option value="subtotal" {{ ($setting['global_discount_target'] ?? '') === 'subtotal' ? 'selected' : '' }}>Produk</option>
                                <option value="shipping" {{ ($setting['global_discount_target'] ?? '') === 'shipping' ? 'selected' : '' }}>Ongkir</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Tipe</label>
                            <select name="global_discount_type" class="w-full border-2 border-gray-100 rounded-xl px-2 py-2 text-xs font-bold focus:border-pink-500 outline-none">
                                <option value="percent" {{ ($setting['global_discount_type'] ?? '') === 'percent' ? 'selected' : '' }}>%</option>
                                <option value="fixed" {{ ($setting['global_discount_type'] ?? '') === 'fixed' ? 'selected' : '' }}>Rp</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Nilai Potongan</label>
                        <input type="number" name="global_discount_value" value="{{ $setting['global_discount_value'] ?? 0 }}" class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 text-lg font-black text-pink-700 focus:border-pink-500 outline-none">
                    </div>

                    <button type="submit" class="w-full bg-pink-600 text-white py-2.5 rounded-xl font-bold text-sm hover:bg-pink-700 transition-all">
                        SIMPAN
                    </button>
                </div>
            </form>
        </div>

    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Informasi Pengaturan</h3>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 font-bold text-xs">?</div>
                    <div>
                        <p class="text-xs font-bold text-gray-800">Kenapa butuh GPS?</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Sistem menghitung jarak tempuh dari toko ke alamat pelanggan secara riil menggunakan rute aspal (OSRM) untuk akurasi ongkir.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center flex-shrink-0 font-bold text-xs">?</div>
                    <div>
                        <p class="text-xs font-bold text-gray-800">Tarif / KM</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">Misal jarak 5km dan tarif Rp 2.000, maka ongkir otomatis jadi Rp 10.000.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-green-950 rounded-2xl shadow-xl p-6 text-white overflow-hidden relative">
            <div class="relative z-10">
                <p class="text-xs font-bold text-green-400 uppercase tracking-widest mb-2">Butuh Bantuan?</p>
                <p class="text-sm leading-relaxed mb-4">Jika mengalami kendala dalam pengaturan lokasi atau sinkronisasi jam operasional, silakan hubungi tim IT.</p>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-ping"></div>
                    <span class="text-[10px] font-bold text-green-100">Sistem Berjalan Normal</span>
                </div>
            </div>
            <svg class="absolute -right-10 -bottom-10 w-40 h-40 text-green-900 opacity-20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
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
    var savedLat = {!! isset($setting['admin_latitude']) ? $setting['admin_latitude'] : 'null' !!};
    var savedLng = {!! isset($setting['admin_longitude']) ? $setting['admin_longitude'] : 'null' !!};

    var initLat  = savedLat || -2.5;
    var initLng  = savedLng || 118.0;
    var initZoom = savedLat ? 16 : 5;

    var map = L.map('admin-map', { zoomControl: true }).setView([initLat, initLng], initZoom);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19
    }).addTo(map);

    var pinIcon = L.divIcon({
        html: '<div style="position:relative;width:32px;height:42px;"><svg viewBox="0 0 32 42" xmlns="http://www.w3.org/2000/svg"><path d="M16 0C9.37 0 4 5.37 4 12c0 9 12 30 12 30S28 21 28 12C28 5.37 22.63 0 16 0z" fill="#15803d"/><circle cx="16" cy="12" r="6" fill="white"/></svg></div>',
        iconSize: [32, 42], iconAnchor: [16, 42], className: ''
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
