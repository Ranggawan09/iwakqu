@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')
<div class="py-12 min-h-screen bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 space-y-6">

        <h1 class="text-2xl font-black text-gray-900">Profil Saya</h1>

        {{-- Flash --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
            ✅ {{ session('success') }}
        </div>
        @endif

        {{-- ── Informasi Akun ──────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h2 class="font-bold text-gray-900 text-lg mb-5 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-700 text-white rounded-full flex items-center justify-center text-xs font-black">1</span>
                Informasi Akun
            </h2>

            <form method="POST" action="{{ route('profile.update-info') }}" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Avatar --}}
                <div class="flex items-center gap-4 mb-6">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="Foto Profil"
                             class="w-16 h-16 rounded-full object-cover flex-shrink-0 border-2 border-green-200">
                    @else
                        <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-green-900 font-black text-2xl">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-900 text-lg">{{ auth()->user()->name }}</p>
                        <p class="text-gray-400 text-sm">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->isGoogleUser())
                            <span class="inline-flex items-center gap-1 text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full mt-1">
                                <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                Akun Google
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none transition-colors @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none transition-colors @error('email') border-red-400 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full bg-green-700 text-white font-bold py-3 rounded-xl hover:bg-green-600 transition-all text-sm">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- ── Ganti Kata Sandi ────────────────────────────────────────────── --}}
        @if(auth()->user()->isGoogleUser())
        {{-- User Google tidak punya password, tampilkan info saja --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h2 class="font-bold text-gray-900 text-lg mb-4 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-700 text-white rounded-full flex items-center justify-center text-xs font-black">2</span>
                Kata Sandi
            </h2>
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl p-4">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-800">Akun Google tidak membutuhkan kata sandi</p>
                    <p class="text-xs text-blue-600 mt-1">Anda login menggunakan akun Google. Keamanan akun dikelola langsung oleh Google.</p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
            <h2 class="font-bold text-gray-900 text-lg mb-5 flex items-center gap-2">
                <span class="w-8 h-8 bg-green-700 text-white rounded-full flex items-center justify-center text-xs font-black">2</span>
                Ganti Kata Sandi
            </h2>

            <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Current password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none transition-colors @error('current_password') border-red-400 @enderror">
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- New password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kata Sandi Baru</label>
                    <input type="password" name="password" required autocomplete="new-password"
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none transition-colors @error('password') border-red-400 @enderror">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-gray-400 text-xs mt-1">Minimal 8 karakter</p>
                </div>

                {{-- Confirm password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none transition-colors">
                </div>

                <button type="submit"
                        class="w-full bg-yellow-400 text-green-900 font-bold py-3 rounded-xl hover:bg-yellow-300 transition-all text-sm">
                    Ubah Kata Sandi
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
