@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="min-h-screen gradient-hero flex items-center justify-center py-20 px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-green-700 px-8 py-8 text-center">
                <div class="w-16 h-16 bg-yellow-400 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <span class="text-4xl">🐟</span>
                </div>
                <h1 class="text-white font-black text-2xl">Selamat Datang!</h1>
                <p class="text-green-200 text-sm mt-1">Login ke akun LeleFresh Anda</p>
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
                            <button type="button" onclick="togglePass()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-green-600 border-gray-300 rounded">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                    </div>

                    <button type="submit"
                            class="w-full bg-green-700 text-white py-3.5 rounded-xl font-bold text-lg hover:bg-green-600 btn-glow transition-all">
                        Masuk Sekarang
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-gray-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-green-700 font-bold hover:text-green-500">Daftar Gratis</a>
                </div>

                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-xs text-yellow-700">
                    <strong>Demo:</strong> Admin: admin@iwakqu.id / password | User: user@iwakqu.id / password
                </div>
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
