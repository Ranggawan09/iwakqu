@extends('layouts.app')
@section('title', 'Login')

@section('content')
    <div class="min-h-screen gradient-hero flex items-center justify-center py-20 px-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="bg-green-700 px-8 py-8 text-center">
                    <a class="flex justify-center items-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="IwakQu Logo"
                            class="w-20 h-20 bg-white rounded-xl flex justify-center items-center">
                    </a>
                    <h1 class="text-white font-black text-2xl">Selamat Datang!</h1>
                    <p class="text-green-200 text-sm mt-1">Login ke akun IwakQu Anda</p>
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

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-gray-700 font-semibold text-sm mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 focus:ring-0 outline-none transition-colors text-gray-800 @error('email') border-red-400 @enderror"
                                placeholder="contoh@email.com">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold text-sm mb-2">Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required
                                    class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-green-500 focus:ring-0 outline-none transition-colors text-gray-800 @error('password') border-red-400 @enderror"
                                    placeholder="Min. 8 karakter">
                                <button type="button" onclick="togglePass()"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember"
                                class="w-4 h-4 text-green-600 border-gray-300 rounded">
                            <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                        </div>

                        <button type="submit"
                            class="w-full bg-green-700 text-white py-3.5 rounded-xl font-bold text-lg hover:bg-green-600 btn-glow transition-all">
                            Masuk Sekarang
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
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        Masuk dengan Google
                    </a>

                    <div class="mt-6 text-center text-sm text-gray-500">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-green-700 font-bold hover:text-green-500">Daftar
                            Gratis</a>
                    </div>

                    <!-- <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-xs text-yellow-700">
                            <strong>Demo:</strong> Admin: admin@iwakqu.id / password | User: user@iwakqu.id / password
                        </div> -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePass() {
            const inp = document.getElementById('password');
            inp.type = inp.type === 'password' ? 'text' : 'password';
        }
    </script>
@endpush