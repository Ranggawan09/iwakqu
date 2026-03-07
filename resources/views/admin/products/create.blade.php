@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.products.index') }}" class="text-gray-400 text-sm hover:text-gray-600 mb-4 inline-block">← Kembali ke Daftar</a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
            <ul class="text-red-600 text-sm space-y-1">
                @foreach ($errors->all() as $error) <li>• {{ $error }}</li> @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-gray-700 font-semibold text-sm mb-1">Nama Produk *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold text-sm mb-1">Deskripsi *</label>
                <textarea name="description" rows="4" required
                          class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors resize-none">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Harga (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price') }}" min="0" required
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors"
                           placeholder="55000">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Stok *</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold text-sm mb-1">Gambar Produk</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 focus:border-green-500 outline-none transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 file:text-sm file:font-semibold">
                <p class="text-gray-400 text-xs mt-1">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                       class="w-5 h-5 text-green-600 border-gray-300 rounded cursor-pointer">
                <label for="is_active" class="text-gray-700 font-semibold text-sm cursor-pointer">Aktif (tampil di website)</label>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-green-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-600 transition-all">
                    Simpan Produk
                </button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-200 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
