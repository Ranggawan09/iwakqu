@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

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

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-gray-700 font-semibold text-sm mb-1">Nama Produk *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors">
            </div>
            <div>
                <label class="block text-gray-700 font-semibold text-sm mb-1">Deskripsi *</label>
                <textarea name="description" rows="4" required
                          class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors resize-none">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Harga (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" required
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm mb-1">Stok *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 outline-none transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-gray-700 font-semibold text-sm mb-1">Gambar Produk</label>
                @if($product->image)
                <div class="mb-3 flex items-center gap-3">
                    <img src="{{ $product->image_url }}" class="w-16 h-16 object-cover rounded-xl"
                         onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=100&q=80'; this.onerror=null;">
                    <span class="text-sm text-gray-500">Gambar saat ini: <strong>{{ $product->image }}</strong></span>
                </div>
                @endif
                <input type="file" name="image" accept="image/*"
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 focus:border-green-500 outline-none transition-colors file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 file:text-sm file:font-semibold">
                <p class="text-gray-400 text-xs mt-1">Kosongkan jika tidak ingin mengubah gambar.</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                       class="w-5 h-5 text-green-600 border-gray-300 rounded cursor-pointer">
                <label for="is_active" class="text-gray-700 font-semibold text-sm cursor-pointer">Aktif</label>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-green-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-green-600 transition-all">
                    Perbarui Produk
                </button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-bold hover:bg-gray-200 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
