@extends('layouts.app')
@section('title', 'Daftar Akun')

@section('content')
<div class="min-h-screen gradient-hero flex items-center justify-center py-20 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-green-700 px-8 py-8 text-center">
                <div class="w-16 h-16 bg-yellow-400 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <span class="text-4xl">🐟</span>
                </div>
                <h1 class="text-white font-black text-2xl">Buat Akun Baru</h1>
                <p class="text-green-200 text-sm mt-1">Bergabung dengan komunitas LeleFresh</p>
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
                        Daftar Sekarang 🚀
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-gray-500">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-green-700 font-bold hover:text-green-500">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
