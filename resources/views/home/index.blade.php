@extends('layouts.app')

@section('title', 'Beranda')
@section('description', 'IwakQu - Ikan Marinasi Premium pilihan Nusantara. Gurame, Nila, Kakap, Patin, dan banyak lagi. Segar, lezat, bergizi untuk keluarga.')

@section('content')
<!-- Hero Section -->
<section class="gradient-hero min-h-screen flex items-center relative overflow-hidden">
    <!-- decorative circles -->
    <div class="absolute top-20 right-0 w-96 h-96 bg-yellow-400 opacity-5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="fade-in">
            <div class="inline-flex items-center bg-yellow-400/20 border border-yellow-400/30 rounded-full px-4 py-2 mb-6">
                <span class="text-yellow-400 text-sm font-semibold">Ikan Marinasi Premium</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                Ikan <span class="gradient-text">Marinasi</span><br>
                Segar Langsung<br>ke Pintu Anda
            </h1>
            <p class="text-green-200 text-lg mb-8 leading-relaxed max-w-md">
                Nikmati kelezatan berbagai ikan pilihan Nusantara — Gurame, Nila, Kakap, Patin & lebih banyak lagi. Dimarinasi bumbu khas, diolah higienis, dikirim cepat.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ auth()->check() ? route('cart.index') : route('register') }}#produk"
                   onclick="document.getElementById('produk').scrollIntoView({behavior:'smooth'}); return false;"
                   class="bg-yellow-400 text-green-900 px-8 py-4 rounded-2xl font-bold text-lg btn-glow text-center hover:bg-yellow-300 transition-all">Pesan Sekarang
                </a>
                <a href="#produk" class="border-2 border-white/30 text-white px-8 py-4 rounded-2xl font-bold text-lg text-center hover:bg-white/10 transition-all">
                    Lihat Produk
                </a>
            </div>
            <div class="flex items-center gap-6 mt-10">
                <div class="text-center">
                    <div class="text-yellow-400 font-black text-2xl">500+</div>
                    <div class="text-green-300 text-sm">Pelanggan Puas</div>
                </div>
                <div class="w-px h-10 bg-green-600"></div>
                <div class="text-center">
                    <div class="text-yellow-400 font-black text-2xl">⭐ 4.9</div>
                    <div class="text-green-300 text-sm">Rating</div>
                </div>
                <div class="w-px h-10 bg-green-600"></div>
                <div class="text-center">
                    <div class="text-yellow-400 font-black text-2xl">30 Menit</div>
                    <div class="text-green-300 text-sm">Pengiriman</div>
                </div>
            </div>
        </div>
        <div class="flex justify-center fade-in">
            <div class="relative">
                <div class="absolute inset-0 bg-yellow-400 opacity-20 rounded-full blur-3xl scale-110"></div>
                <img src="{{ asset('images/products/hero.jpg') }}" alt="Ikan Marinasi IwakQu"
                     class="relative w-full max-w-md rounded-3xl shadow-2xl object-cover aspect-square"
                     onerror="this.src='https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?w=600&q=80'; this.onerror=null;">
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gray-50" style="clip-path: ellipse(55% 100% at 50% 100%)"></div>
</section>

<!-- Products Section -->
<section id="produk" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-green-100 text-green-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-3">Produk Unggulan</span>
            <h2 class="text-4xl font-black text-gray-900 mb-4">Ikan Marinasi <span class="text-green-700">Pilihan Kami</span></h2>
            <p class="text-gray-500 max-w-xl mx-auto">9 pilihan ikan Nusantara dengan resep marinasi bumbu asli — diolah higienis, segar, siap masak, dan penuh cita rasa.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 max-w-7xl mx-auto">
            @foreach($products as $product)
            <div class="bg-white rounded-3xl overflow-hidden shadow-md card-hover border border-gray-100">
                <div class="relative h-64 overflow-hidden">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                         onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=600&q=80'; this.onerror=null;">
                    <div class="absolute top-4 left-4 bg-green-600 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        Stok: {{ $product->stock }}
                    </div>
                    @if($product->stock < 9 && $product->stock > 0)
                    <div class="absolute top-4 right-4 bg-orange-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        Hampir Habis!
                    </div>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="font-black text-xl text-gray-900 mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-3">{{ $product->description }}</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-3xl font-black text-green-700">{{ $product->formatted_price }}</span>
                            <br>
                            <span class="text-gray-400 text-sm">/ porsi</span>
                        </div>
                        @auth
                            @if(!auth()->user()->isAdmin())
                            <div class="flex flex-col gap-2">
                                {{-- Tambah ke keranjang --}}
                                <form action="{{ route('cart.add') }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                           class="w-16 border border-gray-200 rounded-xl text-center text-sm py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <button type="submit"
                                            class="flex-1 bg-green-700 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-600 transition-all flex items-center justify-center gap-1 shadow-md hover:shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Keranjang
                                    </button>
                                </form>

                                {{-- Pesan Sekarang: tambah ke cart lalu langsung checkout --}}
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="redirect_checkout" value="1">
                                    <button type="submit"
                                            class="w-full bg-yellow-400 text-green-900 px-2.5 py-2.5 rounded-xl font-black hover:bg-yellow-300 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                        Pesan Sekarang
                                    </button>
                                </form>
                            </div>
                            @endif
                        @else
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('login') }}" class="w-full text-center bg-green-700 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-green-600 transition-all shadow-md">
                                Login untuk Pesan
                            </a>
                            <a href="{{ route('login') }}" class="w-full text-center bg-yellow-400 text-green-900 px-5 py-2.5 rounded-xl font-black hover:bg-yellow-300 transition-all shadow-md">
                                Pesan Sekarang
                            </a>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Keunggulan Section -->
<section id="tentang" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-4xl font-black text-gray-900 mb-4">Kenapa Pilih <span class="text-green-700">IwakQu?</span></h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon' => '🐟', 'title' => 'Ikan Segar Pilihan', 'desc' => 'Berbagai jenis ikan Nusantara dipilih dari sumber terbaik, segar setiap hari.'],
                ['icon' => '🧼', 'title' => 'Higienis & Terjamin', 'desc' => 'Diolah dengan standar kebersihan tinggi, bebas bahan pengawet kimia.'],
                ['icon' => '🚚', 'title' => 'Pengiriman Cepat', 'desc' => 'Antar ke rumah dalam 2 jam, masih segar saat tiba di tangan Anda.'],
                ['icon' => '💰', 'title' => 'Harga Terjangkau', 'desc' => 'Kualitas premium dengan harga yang bersahabat untuk semua kalangan.'],
            ] as $item)
            <div class="bg-gradient-to-br from-green-50 to-yellow-50 rounded-2xl p-6 text-center border border-green-100 card-hover">
                <div class="text-5xl mb-4">{{ $item['icon'] }}</div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $item['title'] }}</h3>
                <p class="text-gray-500 text-sm">{{ $item['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimoni Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-4xl font-black text-gray-900 mb-4">Apa Kata Mereka?</h2>
        </div>
        @if($testimonials->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($testimonials as $t)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 card-hover flex flex-col justify-between">
                <div>
                    <div class="flex text-yellow-400 mb-3">
                        @for($i=0; $i<$t->rating; $i++) ⭐ @endfor
                    </div>
                    <p class="text-gray-600 text-sm mb-4 leading-relaxed italic">"{{ $t->review }}"</p>
                </div>
                <div class="flex items-center mt-auto">
                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <span class="text-white font-bold text-sm">{{ strtoupper(substr($t->user->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{ $t->user->name }}</div>
                        <div class="text-gray-400 text-xs">Pelanggan</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center text-gray-400 italic">Belum ada testimoni, jadilah yang pertama!</p>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="gradient-hero py-20">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-4xl font-black text-white mb-4">Siap Menikmati Ikan Marinasi Segar?</h2>
        <p class="text-green-200 mb-8">Daftar sekarang dan dapatkan berbagai pilihan ikan marinasi premium langsung ke pintu rumah Anda.</p>
        <a href="{{ auth()->check() ? route('home').'#produk' : route('register') }}"
           class="bg-yellow-400 text-green-900 px-10 py-4 rounded-2xl font-black text-xl btn-glow hover:bg-yellow-300 transition-all inline-block">
            {{ auth()->check() ? 'Pesan Sekarang' : 'Daftar Gratis' }}
        </a>
    </div>
</section>
@endsection
