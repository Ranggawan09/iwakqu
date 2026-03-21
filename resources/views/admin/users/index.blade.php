@extends('layouts.admin')
@section('title', 'Kelola Pengguna')
@section('page-title', 'Kelola Pengguna')

@section('content')
<div class="mb-4">
    <p class="text-gray-400 text-sm">{{ $users->total() }} pengguna terdaftar</p>
</div>

{{-- ============================================================ --}}
{{-- MOBILE: Card layout (shown only on small screens) --}}
{{-- ============================================================ --}}
<div class="md:hidden space-y-3">
    @forelse($users as $user)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex items-center gap-3">
        {{-- Avatar --}}
        <div class="w-11 h-11 {{ $user->isAdmin() ? 'bg-yellow-400' : 'bg-green-600' }} rounded-full flex items-center justify-center flex-shrink-0">
            <span class="{{ $user->isAdmin() ? 'text-green-900' : 'text-white' }} font-bold text-sm">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </span>
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <p class="font-semibold text-gray-900 text-sm">{{ $user->name }}</p>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold {{ $user->isAdmin() ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                    {{ $user->isAdmin() ? 'Admin' : 'User' }}
                </span>
            </div>
            <p class="text-gray-400 text-xs break-all">{{ $user->email }}</p>
            <p class="text-gray-400 text-xs mt-0.5">Bergabung {{ $user->created_at->format('d M Y') }}</p>
        </div>

        {{-- Action --}}
        @if(!$user->isAdmin())
        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
              onsubmit="return confirm('Hapus user {{ $user->name }}?')" class="flex-shrink-0">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors">
                Hapus
            </button>
        </form>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center text-gray-400">
        Tidak ada pengguna
    </div>
    @endforelse

    @if($users->hasPages())
    <div class="pt-2">{{ $users->links() }}</div>
    @endif
</div>

{{-- ============================================================ --}}
{{-- DESKTOP: Table layout (hidden on mobile) --}}
{{-- ============================================================ --}}
<div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <p class="text-gray-400 text-sm">{{ $users->total() }} pengguna terdaftar</p>
    </div>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Role</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bergabung</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 {{ $user->isAdmin() ? 'bg-yellow-400' : 'bg-green-600' }} rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="{{ $user->isAdmin() ? 'text-green-900' : 'text-white' }} font-bold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                    </div>
                </td>
                <td class="px-5 py-4 text-gray-600">{{ $user->email }}</td>
                <td class="px-5 py-4">
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $user->isAdmin() ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                        {{ $user->isAdmin() ? 'Admin' : 'User' }}
                    </span>
                </td>
                <td class="px-5 py-4 text-gray-500 text-sm">{{ $user->created_at->format('d M Y') }}</td>
                <td class="px-5 py-4">
                    @if(!$user->isAdmin())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors">
                            Hapus
                        </button>
                    </form>
                    @else
                    <span class="text-gray-300 text-xs">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-10 text-gray-400">Tidak ada pengguna</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
    <div class="p-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>
@endsection

