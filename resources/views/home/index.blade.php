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

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-6 max-w-7xl mx-auto">
            @foreach($products as $product)
            <div class="bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-md card-hover border border-gray-100 flex flex-col">
                <div class="relative h-40 sm:h-64 overflow-hidden flex-shrink-0">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                         onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=600&q=80'; this.onerror=null;">
                    <div class="absolute top-2 left-2 sm:top-4 sm:left-4 bg-green-600 text-white text-[10px] sm:text-xs font-bold px-2 py-1 sm:px-3 sm:py-1.5 rounded-full">
                        Stok: {{ $product->stock }}
                    </div>
                    @if($product->stock < 9 && $product->stock > 0)
                    <div class="absolute top-2 right-2 sm:top-4 sm:right-4 bg-orange-500 text-white text-[10px] sm:text-xs font-bold px-2 py-1 sm:px-3 sm:py-1.5 rounded-full">
                        Hampir Habis!
                    </div>
                    @endif
                </div>
                <div class="p-4 sm:p-6 flex flex-col flex-1">
                    <h3 class="font-black text-sm sm:text-xl text-gray-900 mb-1 sm:mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-500 text-xs sm:text-sm leading-relaxed mb-3 sm:mb-4 line-clamp-3">{{ $product->description }}</p>
                    <div class="flex flex-col gap-2 sm:gap-3 mt-auto">
                        @auth
                            @if(!auth()->user()->isAdmin())
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-3">
                                <div class="flex-shrink-0">
                                    <span class="text-sm xl:text-xl sm:text-lg font-black text-green-700">{{ $product->formatted_price }}</span>
                                    <span class="text-gray-400 text-[10px] sm:text-xs">/ porsi</span>
                                </div>
                                
                                {{-- Tambah ke keranjang --}}
                                <form action="{{ route('cart.add') }}" method="POST" class="flex items-center gap-1 sm:gap-2 w-full sm:w-auto mt-1 sm:mt-0 justify-between sm:justify-end">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                           class="w-full sm:w-16 border border-gray-200 rounded-lg sm:rounded-xl text-center text-xs sm:text-sm py-1.5 sm:py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <button type="submit" title="Tambah ke Keranjang"
                                            class="bg-green-700 text-white w-9 h-9 sm:w-[42px] sm:h-[42px] rounded-lg sm:rounded-xl font-bold hover:bg-green-600 transition-all flex items-center justify-center shadow-md hover:shadow-lg flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            {{-- Pesan Sekarang: tambah ke cart lalu langsung checkout --}}
                            <form action="{{ route('cart.add') }}" method="POST" class="w-full mt-1 sm:mt-0">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" id="direct-buy-qty-{{ $product->id }}" value="1">
                                <input type="hidden" name="redirect_checkout" value="1">
                                <button type="submit"
                                        class="w-full bg-yellow-400 text-green-900 py-2 sm:py-2.5 rounded-lg sm:rounded-xl font-black hover:bg-yellow-300 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-1 sm:gap-2 text-xs sm:text-base">
                                    Pesan Sekarang
                                </button>
                            </form>
                            @endif
                        @else
                        <div class="flex items-center justify-between mb-1 sm:mb-3">
                            <div>
                                <span class="text-sm sm:text-2xl font-black text-green-700">{{ $product->formatted_price }}</span>
                                <span class="text-gray-400 text-[10px] sm:text-xs">/ porsi</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5 sm:gap-2 w-full">
                            <a href="{{ route('login') }}" class="w-full text-center bg-green-700 text-white px-3 sm:px-5 py-1.5 sm:py-2.5 rounded-lg sm:rounded-xl font-semibold hover:bg-green-600 transition-all shadow-md text-xs sm:text-base">
                                Login
                            </a>
                            <a href="{{ route('login') }}" class="w-full text-center bg-yellow-400 text-green-900 px-3 sm:px-5 py-1.5 sm:py-2.5 rounded-lg sm:rounded-xl font-black hover:bg-yellow-300 transition-all shadow-md text-xs sm:text-base">
                                Pesan
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
<!-- WhatsApp Float Button -->
<a href="https://api.whatsapp.com/send?phone=628980625805&text=hai,%20saya%20mau%20pesan%20"
   target="_blank"
   class="fixed bottom-6 right-6 bg-[#25D366] text-white p-4 rounded-full shadow-lg hover:bg-[#128C7E] transition-all z-50 flex items-center justify-center hover:scale-110"
   title="Hubungi Kami via WhatsApp">
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
        <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
    </svg>
</a>
@endsection
