<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IwakQu — Ikan Marinasi Premium') | IwakQu</title>
    <meta name="description" content="@yield('description', 'IwakQu - Penjualan Ikan Marinasi Premium pilihan Nusantara. Gurame, Nila, Kakap, Patin, dan banyak lagi. Segar, lezat, dan siap antar ke rumah Anda.')">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Poppins', sans-serif; }
        .gradient-hero { background: linear-gradient(135deg, #14532D 0%, #166534 50%, #15803D 100%); }
        .gradient-text { background: linear-gradient(135deg, #FACC15, #FDE047); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
        .btn-glow { box-shadow: 0 0 20px rgba(250,204,21,0.4); transition: all 0.3s ease; }
        .btn-glow:hover { box-shadow: 0 0 35px rgba(250,204,21,0.7); transform: translateY(-2px); }
        .nav-glass { backdrop-filter: blur(12px); background: rgba(20, 83, 45, 0.95); }
        .badge-cart { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.15); } }
        .fade-in { animation: fadeIn 0.6s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">

<!-- Navbar -->
<nav class="nav-glass fixed top-0 left-0 right-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                <img src="{{ asset('images/logo.png') }}" alt="IwakQu Logo" class="w-12 h-12 bg-white rounded-xl p-1 object-contain group-hover:scale-110 transition-transform drop-shadow-md">
                <div>
                    <span class="text-yellow-400 font-black text-xl">Iwak</span><span class="text-white font-black text-xl">Qu</span>
                    <p class="text-green-300 text-xs -mt-1 hidden sm:block">Ikan Marinasi Premium</p>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-green-100 hover:text-yellow-400 font-medium transition-colors">Beranda</a>
                <a href="{{ route('home') }}#produk" class="text-green-100 hover:text-yellow-400 font-medium transition-colors">Produk</a>
                <a href="{{ route('home') }}#tentang" class="text-green-100 hover:text-yellow-400 font-medium transition-colors">Tentang</a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-green-100 hover:text-yellow-400 font-medium transition-colors">Dashboard Admin</a>
                    @else
                        <a href="{{ route('cart.index') }}" class="relative text-green-100 hover:text-yellow-400 font-medium transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity'); @endphp
                            @if($cartCount > 0)
                                <span class="badge-cart absolute -top-2 -right-2 bg-yellow-400 text-green-900 text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('orders.index') }}" class="text-green-100 hover:text-yellow-400 font-medium transition-colors">Pesanan</a>
                    @endif

                    <div class="relative group">
                        <a href="{{ route('profile.show') }}" class="flex items-center space-x-2 text-green-100 hover:text-yellow-400 font-medium transition-colors">
                            <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                                <span class="text-green-900 font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <span class="hidden lg:inline">{{ auth()->user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                        <div class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 border border-gray-100 overflow-hidden">
                            {{-- User info header --}}
                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                                <p class="text-xs text-gray-400">Login sebagai</p>
                                <p class="font-bold text-gray-800 text-sm truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            {{-- Profil --}}
                            <a href="{{ route('profile.show') }}"
                               class="flex items-center gap-2 px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profil Saya
                            </a>
                            <div class="border-t border-gray-100 mx-3"></div>
                            {{-- Logout --}}
                            <form method="POST" action="{{ route('logout') }}" class="p-1">
                                @csrf
                                <button type="submit" class="w-full text-left px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-xl font-medium flex items-center gap-2 text-sm transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-green-100 hover:text-yellow-400 font-medium transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="bg-yellow-400 text-green-900 px-5 py-2 rounded-xl font-bold hover:bg-yellow-300 btn-glow transition-all">Daftar</a>
                @endauth
            </div>

            <!-- Mobile: avatar dropdown + hamburger -->
            <div class="md:hidden flex items-center gap-3">
                @auth
                    @if(!auth()->user()->isAdmin())
                        <a href="{{ route('cart.index') }}" class="relative text-green-100 hover:text-yellow-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity'); @endphp
                            @if($cartCount > 0)
                                <span class="badge-cart absolute -top-1 -right-2 bg-yellow-400 text-green-900 text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        </a>
                    @endif
                @endauth
                <button id="mobile-menu-btn" class="text-white p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                @auth
                <a href="{{ route('profile.show') }}" class="flex items-center gap-1.5 text-green-100 hover:text-yellow-400 transition-colors">
                    <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                        <span class="text-green-900 font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                </a>
                @endauth
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <div class="flex flex-col space-y-3 pt-2 border-t border-green-700">
                <a href="{{ route('home') }}" class="text-green-100 hover:text-yellow-400 font-medium py-2">Beranda</a>
                <a href="{{ route('home') }}#produk" class="text-green-100 hover:text-yellow-400 font-medium py-2">Produk</a>
                @auth
                    @if(!auth()->user()->isAdmin())
                        <a href="{{ route('cart.index') }}" class="text-green-100 hover:text-yellow-400 font-medium py-2">Keranjang</a>
                        <a href="{{ route('orders.index') }}" class="text-green-100 hover:text-yellow-400 font-medium py-2">Pesanan Saya</a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="text-green-100 hover:text-yellow-400 font-medium py-2">Dashboard Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-400 font-medium py-2">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-green-100 hover:text-yellow-400 font-medium py-2">Login</a>
                    <a href="{{ route('register') }}" class="bg-yellow-400 text-green-900 px-4 py-2 rounded-xl font-bold text-center">Daftar Sekarang</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
@if(session('success'))
<div id="flash-success" class="fixed top-20 right-4 z-50 bg-green-600 text-white px-6 py-4 rounded-xl shadow-xl flex items-center space-x-3 fade-in max-w-sm">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div id="flash-error" class="fixed top-20 right-4 z-50 bg-red-600 text-white px-6 py-4 rounded-xl shadow-xl flex items-center space-x-3 fade-in max-w-sm">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
    </svg>
    <span>{{ session('error') }}</span>
</div>
@endif

<main class="pt-16">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-green-950 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="IwakQu Logo" class="w-12 h-12 bg-white rounded-xl p-1 object-contain">
                    <div>
                        <span class="text-yellow-400 font-black text-xl">Iwak</span><span class="text-white font-black text-xl">Qu</span>
                    </div>
                </div>
                <p class="text-green-300 text-sm leading-relaxed">Usaha UMKM penjualan ikan marinasi premium dari berbagai jenis ikan pilihan Nusantara. Segar, lezat, dan bergizi untuk keluarga Indonesia.</p>
            </div>
            <div>
                <h4 class="font-bold text-yellow-400 mb-4">Menu</h4>
                <div class="space-y-2">
                    <a href="{{ route('home') }}" class="block text-green-300 hover:text-yellow-400 text-sm transition-colors">Beranda</a>
                    <a href="{{ route('home') }}#produk" class="block text-green-300 hover:text-yellow-400 text-sm transition-colors">Produk</a>
                    <a href="{{ route('home') }}#tentang" class="block text-green-300 hover:text-yellow-400 text-sm transition-colors">Tentang Kami</a>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-yellow-400 mb-4">Hubungi Kami</h4>
                <div class="space-y-2 text-sm text-green-300">
                    <p>📱 WhatsApp: +62 812-3456-7890</p>
                    <p>📧 Email: info@iwakqu.id</p>
                    <p>📍 Jl. Ikan Segar No. 10, Jakarta</p>
                </div>
            </div>
        </div>
        <div class="border-t border-green-800 mt-8 pt-6 text-center text-green-400 text-sm">
            © {{ date('Y') }} IwakQu. Dibuat dengan ❤️ untuk UMKM Indonesia.
        </div>
    </div>
</footer>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });

    // Auto hide flash messages
    setTimeout(() => {
        ['flash-success', 'flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) { el.style.transition = 'opacity 0.5s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 500); }
        });
    }, 4000);
</script>
@stack('scripts')
</body>
</html>
