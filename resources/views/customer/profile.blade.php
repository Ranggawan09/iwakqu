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
                    <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-green-900 font-black text-2xl">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-lg">{{ auth()->user()->name }}</p>
                        <p class="text-gray-400 text-sm">{{ auth()->user()->email }}</p>
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

    </div>
</div>
@endsection
