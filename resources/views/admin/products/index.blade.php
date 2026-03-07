@extends('layouts.admin')
@section('title', 'Kelola Produk')
@section('page-title', 'Kelola Produk')

@section('content')
<div class="mb-5 flex items-center justify-between">
    <p class="text-gray-400 text-sm">{{ $products->total() }} produk terdaftar</p>
    <a href="{{ route('admin.products.create') }}"
       class="bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-green-600 transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Produk
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Produk</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Harga</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Stok</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $product->image_url }}" class="w-12 h-12 object-cover rounded-xl"
                             onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=100&q=80'; this.onerror=null;">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                            <p class="text-gray-400 text-xs line-clamp-1">{{ Str::limit($product->description, 60) }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4 font-semibold text-green-700">{{ $product->formatted_price }}</td>
                <td class="px-5 py-4">
                    <span class="font-semibold {{ $product->stock < 10 ? 'text-orange-600' : 'text-gray-900' }}">
                        {{ $product->stock }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">
                            Edit
                        </a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-10 text-gray-400">Belum ada produk</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($products->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $products->links() }}</div>
    @endif
</div>
@endsection
