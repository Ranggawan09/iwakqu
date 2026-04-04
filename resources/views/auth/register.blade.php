@extends('layouts.app')
@section('title', 'Daftar Akun')

@section('content')
<div class="min-h-screen gradient-hero flex items-center justify-center py-20 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-green-700 px-8 py-8 text-center">
                <a class="flex justify-center items-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="IwakQu Logo" class="w-20 h-20 bg-white rounded-xl flex justify-center items-center">
                </a>
                <h1 class="text-white font-black text-2xl">Buat Akun Baru</h1>
            </div>

            <div class="px-8 py-8">
                @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <ul class="text-red-600 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-gray-700 font-semibold text-sm mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 focus:ring-0 outline-none transition-colors @error('name') border-red-400 @enderror"
                               placeholder="Nama lengkap Anda">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold text-sm mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 focus:ring-0 outline-none transition-colors @error('email') border-red-400 @enderror"
                               placeholder="contoh@email.com">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold text-sm mb-2">Password</label>
                        <input type="password" name="password" id="password" required
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 focus:ring-0 outline-none transition-colors @error('password') border-red-400 @enderror"
                               placeholder="Min. 8 karakter">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold text-sm mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 focus:ring-0 outline-none transition-colors"
                               placeholder="Ulangi password">
                    </div>

                    <button type="submit"
                            class="w-full bg-green-700 text-white py-3.5 rounded-xl font-bold text-lg hover:bg-green-600 btn-glow transition-all">
                        Daftar Sekarang
                    </button>
                </form>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-400">atau</span>
                    </div>
                </div>

                <a href="{{ route('auth.google') }}"
                   class="w-full flex items-center justify-center gap-3 border-2 border-gray-200 rounded-xl px-4 py-3 font-semibold text-gray-700 hover:border-gray-400 hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Daftar dengan Google
                </a>

                <div class="mt-6 text-center text-sm text-gray-500">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-green-700 font-bold hover:text-green-500">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
