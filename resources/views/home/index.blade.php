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
                Nikmati kelezatan berbagai ikan pilihan Nusantara. Dimarinasi bumbu khas, diolah higienis, dikirim cepat.
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
                    <div class="text-yellow-400 font-black text-2xl">20+</div>
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
                <img src="{{ asset('images/products/hero.webp') }}" alt="Ikan Marinasi IwakQu"
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
            <p class="text-gray-500 max-w-xl mx-auto">Ikan Nusantara dengan resep marinasi bumbu asli — diolah higienis, segar, siap masak, dan penuh cita rasa.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-6 max-w-7xl mx-auto">
            @foreach($products as $product)
            @php
                $finalPrice = $product->price;
                if (isset($globalDiscount) && $globalDiscount['active'] && $globalDiscount['target'] === 'subtotal') {
                    if ($globalDiscount['type'] === 'percent') {
                        $finalPrice -= ($product->price * ($globalDiscount['value'] / 100));
                    } else {
                        $finalPrice -= $globalDiscount['value'];
                    }
                    $finalPrice = max(0, $finalPrice);
                }
                $isDiscounted = $finalPrice < $product->price;
            @endphp
            <div class="bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-md card-hover border border-gray-100 flex flex-col">
                <div class="relative h-40 sm:h-64 overflow-hidden flex-shrink-0">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                         onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=600&q=80'; this.onerror=null;">
                    <div class="absolute top-2 left-2 sm:top-4 sm:left-4 bg-green-600 text-white text-[10px] sm:text-xs font-bold px-2 py-1 sm:px-3 sm:py-1.5 rounded-full">
                        Stok: {{ $product->stock }}
                    </div>
                    @if($product->stock < 9 && $product->stock > 0)
                    <div class="absolute top-2 right-2 sm:top-4 sm:right-4 bg-orange-500 text-white text-[10px] sm:text-xs font-bold px-2 py-1 sm:px-3 sm:py-1.5 rounded-full shadow-sm">
                        Hampir Habis!
                    </div>
                    @endif

                    {{-- Global Discount Badge --}}
                    @if($isDiscounted)
                    <div class="absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-yellow-400 text-green-900 text-[10px] sm:text-xs font-black px-2 py-1 sm:px-3 sm:py-1.5 rounded-lg shadow-lg flex items-center gap-1 animate-bounce z-10">
                        <span>
                            {{ $globalDiscount['type'] === 'percent' ? $globalDiscount['value'] . '%' : 'Rp' . number_format($globalDiscount['value'], 0, ',', '.') }} OFF
                        </span>
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
                                    @if($isDiscounted)
                                        <span class="text-[10px] sm:text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        <div class="flex items-center gap-1">
                                            <span class="text-sm xl:text-xl sm:text-lg font-black text-green-600">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                                            <span class="text-gray-400 text-[10px] sm:text-xs">/ porsi</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1">
                                            <span class="text-sm xl:text-xl sm:text-lg font-black text-green-700">{{ $product->formatted_price }}</span>
                                            <span class="text-gray-400 text-[10px] sm:text-xs">/ porsi</span>
                                        </div>
                                    @endif
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
                                @if($isDiscounted)
                                    <span class="block text-[10px] sm:text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-sm sm:text-2xl font-black text-green-600">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                                        <span class="text-gray-400 text-[10px] sm:text-xs">/ porsi</span>
                                    </div>
                                @else
                                    <div>
                                        <span class="text-sm sm:text-2xl font-black text-green-700">{{ $product->formatted_price }}</span>
                                        <span class="text-gray-400 text-[10px] sm:text-xs">/ porsi</span>
                                    </div>
                                @endif
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

<!-- Sertifikasi & Legalitas Section -->
<section id="sertifikasi" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-yellow-100 text-yellow-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-3">Terjamin & Terpercaya</span>
            <h2 class="text-4xl font-black text-gray-900 mb-4">Legalitas & <span class="text-green-700">Sertifikasi</span></h2>
            <p class="text-gray-500 max-w-xl mx-auto">Komitmen kami untuk memberikan produk yang aman, higienis, dan terjamin kualitasnya melalui izin resmi dan sertifikasi halal.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- NIB -->
            <div class="group relative bg-white p-4 rounded-3xl shadow-xl border border-gray-100 overflow-hidden cursor-pointer transform transition-all duration-500 hover:-translate-y-2" onclick="openCertModal('{{ asset('images/certificates/nib.webp') }}', 'Surat Izin Usaha (NIB)')">
                <div class="relative aspect-[3/4] overflow-hidden rounded-2xl bg-gray-100">
                    <img src="{{ asset('images/certificates/nib.webp') }}" alt="Surat Izin Usaha (NIB)" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                         onerror="this.src='https://placehold.co/600x800?text=NIB+Belum+Diunggah'; this.onerror=null;">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <div class="bg-white/20 backdrop-blur-md p-4 rounded-full border border-white/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 7v6m4-3H10" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <h3 class="text-xl font-black text-gray-900">Surat Izin Usaha</h3>
                    <p class="text-green-600 text-sm font-semibold">Nomor Induk Berusaha (NIB)</p>
                </div>
            </div>

            <!-- HALAL -->
            <div class="group relative bg-white p-4 rounded-3xl shadow-xl border border-gray-100 overflow-hidden cursor-pointer transform transition-all duration-500 hover:-translate-y-2" onclick="openCertModal('{{ asset('images/certificates/halal.webp') }}', 'Sertifikat Halal')">
                <div class="relative aspect-[3/4] overflow-hidden rounded-2xl bg-gray-100">
                    <img src="{{ asset('images/certificates/halal.webp') }}" alt="Sertifikat Halal" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                         onerror="this.src='https://placehold.co/600x800?text=Sertifikat+Halal+Belum+Diunggah'; this.onerror=null;">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <div class="bg-white/20 backdrop-blur-md p-4 rounded-full border border-white/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 7v6m4-3H10" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <h3 class="text-xl font-black text-gray-900">Sertifikat Halal</h3>
                    <p class="text-green-600 text-sm font-semibold">BPJPH Indonesia</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certificate Modal -->
<div id="cert-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="relative max-w-4xl w-full h-fit my-auto outline-none">
        <button onclick="closeCertModal()" class="fixed top-6 right-6 text-white hover:text-yellow-400 transition-colors z-[110] bg-black/50 p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="bg-white rounded-3xl overflow-hidden shadow-2xl scale-95 opacity-0 transition-all duration-300" id="cert-modal-content">
            <div class="p-2">
                <img id="cert-modal-img" src="" alt="" class="w-full h-auto rounded-2xl max-h-[85vh] object-contain mx-auto">
            </div>
            <div class="bg-white px-8 py-4 text-center border-t border-gray-100">
                <h3 id="cert-modal-title" class="text-2xl font-black text-gray-900"></h3>
            </div>
        </div>
    </div>
</div>

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

<!-- PWA Install Popup Modal -->
<div id="pwa-install-popup" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-3xl w-full max-w-sm p-5 shadow-2xl relative scale-95 opacity-0 transition-all duration-300" id="pwa-popup-content">
        <!-- Close button -->
        <button onclick="closePwaPopup()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex items-start gap-4 mb-5 mt-1">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center border border-green-100 shadow-sm">
                    <img src="{{ asset('images/logo.png') }}" alt="IwakQu Logo" class="w-10 h-10 object-contain">
                </div>
            </div>
            <!-- Text Content -->
            <div class="pr-6 pt-0.5">
                <h3 class="text-lg font-black text-gray-900 mb-1 leading-tight">Install IwakQu</h3>
                <p class="text-[11px] sm:text-xs text-gray-500 leading-relaxed">
                    Akses lebih cepat, hemat kuota, dan fitur full screen tanpa perlu download di Store.
                </p>
            </div>
        </div>

        <!-- Install Button -->
        <button id="pwa-install-btn" class="w-full bg-green-700 text-white font-bold py-3.5 rounded-xl shadow-lg hover:bg-green-600 transition-all flex items-center justify-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Install Sekarang
        </button>

        <!-- Checkbox Jangan Tampilkan Lagi -->
        <div class="flex items-center text-left">
            <input type="checkbox" id="dont-show-again" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded outline-none cursor-pointer focus:ring-green-500">
            <label for="dont-show-again" class="ml-2 text-xs text-gray-400 cursor-pointer">jangan tampilkan lagi</label>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let deferredPrompt;
    const pwaPopup = document.getElementById('pwa-install-popup');
    const pwaContent = document.getElementById('pwa-popup-content');
    const installBtn = document.getElementById('pwa-install-btn');
    const dontShowCb = document.getElementById('dont-show-again');

    const hidePopup = localStorage.getItem('hideInstallPopup');
    const tempHide = sessionStorage.getItem('tempHideInstall');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67+ from automatically showing the prompt
        e.preventDefault();
        deferredPrompt = e;

        // Tampilkan modal jika pengguna belum pernah mencentang "jangan tampilkan lagi"
        // dan belum menutupnya di sesi ini
        if (hidePopup !== 'true' && tempHide !== 'true') {
            showPwaPopup();
        }
    });

    function showPwaPopup() {
        pwaPopup.classList.remove('hidden');
        pwaPopup.classList.add('flex');
        
        requestAnimationFrame(() => {
            pwaContent.classList.remove('scale-95', 'opacity-0');
            pwaContent.classList.add('scale-100', 'opacity-100');
        });
    }

    function closePwaPopup() {
        if (dontShowCb.checked) {
            localStorage.setItem('hideInstallPopup', 'true');
        } else {
            sessionStorage.setItem('tempHideInstall', 'true');
        }
        
        pwaContent.classList.remove('scale-100', 'opacity-100');
        pwaContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            pwaPopup.classList.add('hidden');
            pwaPopup.classList.remove('flex');
        }, 300);
    }

    installBtn.addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                localStorage.setItem('hideInstallPopup', 'true');
            }
            deferredPrompt = null;
            closePwaPopup();
        } else {
            // Untuk browser yang tidak mendeteksi beforeinstallprompt (misal iOS Safari Safari)
            // Sebaiknya ditangani secara independen, namun kita sertakan alert untuk amannya
            alert("Untuk menginstall, buka opsi browser (titik tiga atau icon Share) lalu pilih 'Add to Home screen' atau 'Install app'.");
            closePwaPopup();
        }
    });

    // Close modal ketika mengklik background di luar konten
    pwaPopup.addEventListener('click', (e) => {
        if (e.target === pwaPopup) {
            closePwaPopup();
        }
    });

    // Certificate Modal Logic
    const certModal = document.getElementById('cert-modal');
    const certModalContent = document.getElementById('cert-modal-content');
    const certModalImg = document.getElementById('cert-modal-img');
    const certModalTitle = document.getElementById('cert-modal-title');

    function openCertModal(imgSrc, title) {
        certModalImg.src = imgSrc;
        certModalTitle.innerText = title;
        certModal.classList.remove('hidden');
        certModal.classList.add('flex');
        
        // Prevent scrolling on body
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            certModalContent.classList.remove('scale-95', 'opacity-0');
            certModalContent.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeCertModal() {
        certModalContent.classList.remove('scale-100', 'opacity-100');
        certModalContent.classList.add('scale-95', 'opacity-0');
        
        // Re-enable scrolling
        document.body.style.overflow = 'auto';

        setTimeout(() => {
            certModal.classList.add('hidden');
            certModal.classList.remove('flex');
        }, 300);
    }

    // Close on click outside content
    certModal.addEventListener('click', (e) => {
        if (e.target === certModal) {
            closeCertModal();
        }
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !certModal.classList.contains('hidden')) {
            closeCertModal();
        }
    });
</script>
@endpush
@endsection
