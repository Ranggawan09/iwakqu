<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | IwakQu Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Poppins', sans-serif; }

        /* Sidebar */
        #sidebar {
            transition: transform 0.3s ease;
        }
        @media (max-width: 1023px) {
            #sidebar {
                position: fixed;
                top: 0; left: 0;
                height: 100%;
                z-index: 50;
                transform: translateX(-100%);
            }
            #sidebar.open {
                transform: translateX(0);
            }
        }

        /* Backdrop */
        #sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
            backdrop-filter: blur(2px);
        }
        #sidebar-backdrop.open {
            display: block;
        }

        /* Nav links */
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(250,204,21,0.15);
            color: #FACC15;
            border-left: 3px solid #FACC15;
        }

        /* Stat cards */
        .stat-card { transition: transform 0.2s ease; }
        .stat-card:hover { transform: translateY(-3px); }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Backdrop (mobile only) -->
<div id="sidebar-backdrop" onclick="closeSidebar()"></div>

<div class="flex min-h-screen">

    <!-- ─── Sidebar ─────────────────────────────────────────────────── -->
    <aside id="sidebar" class="w-64 bg-green-950 text-white flex-shrink-0 flex flex-col">

        <!-- Logo + Close button (mobile) -->
        <div class="p-5 border-b border-green-800 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                <img src="{{ asset('images/logo.png') }}" alt="IwakQu Logo" class="w-10 h-10 bg-white rounded-xl p-1 object-contain">
                <div>
                    <span class="text-yellow-400 font-black text-lg">Iwak</span><span class="text-white font-black text-lg">Qu</span>
                    <p class="text-green-400 text-xs -mt-1">Admin Panel</p>
                </div>
            </a>
            <!-- Close button: only visible on mobile -->
            <button onclick="closeSidebar()" class="lg:hidden text-green-300 hover:text-white transition-colors p-1 rounded-lg hover:bg-green-800" aria-label="Tutup sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Admin Info -->
        <div class="px-5 py-4 border-b border-green-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center">
                    <span class="text-green-900 font-bold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
                <div>
                    <p class="font-semibold text-sm text-white">{{ auth()->user()->name }}</p>
                    <p class="text-green-400 text-xs">Administrator</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4 space-y-1 px-3">
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-green-200 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="{{ route('admin.products.index') }}"
               class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-green-200 {{ request()->routeIs('admin.products*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="font-medium">Produk</span>
            </a>

            <a href="{{ route('admin.orders.index') }}"
               class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-green-200 {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span class="font-medium">Pesanan</span>
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="sidebar-link flex items-center space-x-3 px-4 py-3 rounded-lg text-green-200 {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
               onclick="closeSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="font-medium">Pengguna</span>
            </a>
        </nav>


    </aside>

    <!-- ─── Main Content ──────────────────────────────────────────────── -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top Bar -->
        <header class="bg-white shadow-sm px-4 lg:px-6 py-4 flex items-center justify-between gap-4 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <!-- Hamburger: only visible on mobile -->
                <button id="sidebar-toggle" onclick="openSidebar()" class="lg:hidden text-gray-600 hover:text-gray-900 transition-colors p-1.5 rounded-lg hover:bg-gray-100" aria-label="Buka sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-lg lg:text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                {{-- Admin Notifications --}}
                <div class="relative">
                    <button type="button" onclick="document.getElementById('admin-notif-dropdown').classList.toggle('hidden')" class="relative text-gray-400 hover:text-yellow-500 transition-colors mr-2 outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold rounded-full h-4 w-4 flex items-center justify-center">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <div id="admin-notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <p class="font-bold text-gray-800 text-sm">Notifikasi</p>
                        </div>
                        <div class="max-h-72 overflow-y-auto w-full">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 hover:bg-green-50 border-b border-gray-50 transition-colors">
                                    <p class="text-xs text-gray-800 font-medium whitespace-normal">{{ $notification->data['message'] ?? 'Notifikasi baru.' }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-sm text-gray-400">Belum ada notifikasi</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <span class="text-sm text-gray-400 hidden sm:block">{{ now()->format('d F Y') }}</span>
                <a href="{{ route('home') }}"
                   class="flex items-center gap-1.5 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 px-3 py-1.5 rounded-lg hover:bg-green-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span class="hidden sm:inline">Lihat Website</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-1.5 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="mx-4 lg:mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-4 lg:mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
            ❌ {{ session('error') }}
        </div>
        @endif

        <main class="flex-1 p-4 lg:p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('sidebar-backdrop').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-backdrop').classList.remove('open');
        document.body.style.overflow = '';
    }

    // Close sidebar on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });
</script>

@stack('scripts')
</body>
</html>
