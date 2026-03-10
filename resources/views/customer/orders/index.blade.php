@extends('layouts.app')
@section('title', 'Riwayat Pesanan')

@section('content')
<div class="py-12 min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-black text-gray-900 mb-8">Riwayat Pesanan</h1>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl text-sm font-semibold">
            ✅ {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl text-sm font-semibold">
            ❌ {{ session('error') }}
        </div>
        @endif

        @if($orders->isEmpty())
        <div class="bg-white rounded-3xl p-16 text-center shadow-sm border border-gray-100">
            <div class="text-8xl mb-4">📦</div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">Belum ada pesanan</h3>
            <a href="{{ route('home') }}#produk" class="inline-block bg-green-700 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-600 mt-4">Pesan Sekarang</a>
        </div>
        @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                {{-- Header: Order ID + Status --}}
                <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400 text-xs">Order</span>
                        <span class="font-black text-gray-900">#{{ $order->id }}</span>
                        <span class="text-gray-300">·</span>
                        <span class="text-gray-400 text-xs">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                        @if($order->status === 'selesai') bg-green-100 text-green-700
                        @elseif($order->status === 'dibayar') bg-blue-100 text-blue-700
                        @elseif($order->status === 'diproses') bg-indigo-100 text-indigo-700
                        @elseif($order->status === 'dikirim') bg-orange-100 text-orange-700
                        @elseif($order->status === 'dibatalkan') bg-red-100 text-red-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                        {{ $order->status_label }}
                    </span>
                </div>

                {{-- Produk yang dipesan --}}
                <div class="px-5 py-4">
                    <div class="flex gap-3">
                        {{-- Foto produk (tumpuk max 3) --}}
                        <div class="flex -space-x-2 flex-shrink-0">
                            @foreach($order->orderItems->take(3) as $item)
                            <img src="{{ $item->product->image_url }}"
                                 class="w-14 h-14 rounded-xl object-cover border-2 border-white shadow-sm"
                                 onerror="this.src='https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=100&q=80'; this.onerror=null;"
                                 title="{{ $item->product->name }}">
                            @endforeach
                            @if($order->orderItems->count() > 3)
                            <div class="w-14 h-14 rounded-xl bg-gray-100 border-2 border-white flex items-center justify-center text-xs font-bold text-gray-500">
                                +{{ $order->orderItems->count() - 3 }}
                            </div>
                            @endif
                        </div>

                        {{-- Detail produk --}}
                        <div class="flex-1 min-w-0">
                            @foreach($order->orderItems as $item)
                            <p class="text-sm text-gray-800 font-semibold truncate">{{ $item->product->name }}</p>
                            <p class="text-xs text-gray-400">{{ $item->quantity }} pcs × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            @endforeach
                        </div>

                        {{-- Total harga --}}
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-gray-400 mb-0.5">Total</p>
                            <p class="font-black text-green-700 text-lg">{{ $order->formatted_total }}</p>
                            @if(($order->shipping_cost ?? 0) > 0)
                            <p class="text-xs text-gray-400">
                                incl. ongkir Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Rating yang sudah diberikan --}}
                @if($order->status === 'selesai' && $order->rating)
                <div class="px-5 pb-3 flex items-center gap-1.5">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $order->rating->rating ? 'text-yellow-400' : 'text-gray-200' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                    <span class="text-xs text-gray-400 ml-1">Sudah dinilai</span>
                </div>
                @endif

                {{-- Footer: transaksi + tombol aksi --}}
                <div class="border-t border-gray-100 px-5 py-3 flex justify-between items-center bg-gray-50">
                    <span class="text-gray-400 text-xs">
                        @if($order->transaction_id)
                            ID: {{ $order->transaction_id }}
                        @elseif($order->status === 'menunggu_pembayaran')
                            ⏳ Menunggu pembayaran
                        @else
                            —
                        @endif
                    </span>
                    <div class="flex items-center gap-2">
                        {{-- Tombol Beri Rating (hanya untuk pesanan selesai) --}}
                        @if($order->status === 'selesai')
                        <button type="button"
                                onclick="openRatingModal({{ $order->id }}, {{ $order->rating ? $order->rating->rating : 0 }}, '{{ addslashes($order->rating->review ?? '') }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all
                                    {{ $order->rating ? 'bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100' : 'bg-green-700 text-white hover:bg-green-600' }}">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ $order->rating ? 'Ubah Rating' : 'Beri Rating' }}
                        </button>
                        @endif

                        <a href="{{ route('orders.show', $order) }}"
                           class="text-green-700 font-semibold text-sm hover:text-green-500 flex items-center gap-1">
                            Lihat Detail
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    </div>
</div>

{{-- ── Rating Modal ─────────────────────────────────────────────────── --}}
<div id="rating-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeRatingModal()"></div>

    {{-- Modal card --}}
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-7 z-10 animate-fade-in">
        {{-- Close button --}}
        <button onclick="closeRatingModal()" class="absolute top-4 right-4 text-gray-300 hover:text-gray-500 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <div class="text-center mb-6">
            <div class="text-4xl mb-2">⭐</div>
            <h2 class="text-xl font-black text-gray-900">Beri Penilaian</h2>
            <p class="text-gray-400 text-sm mt-1">Bagaimana pengalaman berbelanja Anda?</p>
        </div>

        <form id="rating-form" method="POST" action="">
            @csrf

            {{-- Bintang interaktif --}}
            <div class="flex justify-center gap-2 mb-6">
                @for($i = 1; $i <= 5; $i++)
                <button type="button" data-star="{{ $i }}"
                        onclick="setRating({{ $i }})"
                        class="star-btn text-4xl text-gray-200 hover:text-yellow-400 transition-all hover:scale-110 active:scale-95 focus:outline-none">
                    ★
                </button>
                @endfor
            </div>
            <input type="hidden" name="rating" id="rating-input" value="">

            {{-- Label bintang --}}
            <p id="rating-label" class="text-center text-sm font-semibold text-gray-400 mb-5">Pilih jumlah bintang</p>

            {{-- Ulasan --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ulasan <span class="font-normal text-gray-400">(opsional)</span></label>
                <textarea name="review" id="review-input" rows="3"
                          placeholder="Ceritakan pengalaman berbelanja Anda..."
                          class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none resize-none transition-colors"></textarea>
            </div>

            <button type="submit" id="rating-submit"
                    class="w-full bg-green-700 text-white py-3 rounded-xl font-bold text-sm hover:bg-green-600 active:scale-[.99] transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                Kirim Penilaian
            </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes fade-in {
        from { opacity: 0; transform: scale(0.95) translateY(8px); }
        to   { opacity: 1; transform: scale(1)    translateY(0); }
    }
    .animate-fade-in { animation: fade-in .2s ease-out both; }
</style>
@endpush

@push('scripts')
<script>
const ratingLabels = ['', 'Sangat Buruk 😞', 'Buruk 😕', 'Cukup 😐', 'Bagus 😊', 'Sangat Bagus 🤩'];
let currentRating = 0;

function openRatingModal(orderId, existingRating, existingReview) {
    const form    = document.getElementById('rating-form');
    const modal   = document.getElementById('rating-modal');

    // Set form action ke route storeRating
    form.action = '/orders/' + orderId + '/rating';

    // Reset & isi ulang jika sudah ada rating sebelumnya
    setRating(existingRating || 0);
    document.getElementById('review-input').value = existingReview || '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeRatingModal() {
    const modal = document.getElementById('rating-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function setRating(value) {
    currentRating = value;
    const stars   = document.querySelectorAll('.star-btn');
    const input   = document.getElementById('rating-input');
    const label   = document.getElementById('rating-label');
    const submit  = document.getElementById('rating-submit');

    stars.forEach((btn, idx) => {
        btn.classList.toggle('text-yellow-400', idx < value);
        btn.classList.toggle('text-gray-200',   idx >= value);
    });

    input.value       = value;
    label.textContent = value > 0 ? ratingLabels[value] : 'Pilih jumlah bintang';
    label.className   = value > 0
        ? 'text-center text-sm font-bold text-yellow-600 mb-5'
        : 'text-center text-sm font-semibold text-gray-400 mb-5';
    submit.disabled   = value === 0;
}

// Hover preview
document.querySelectorAll('.star-btn').forEach(btn => {
    btn.addEventListener('mouseenter', () => {
        const hoverVal = parseInt(btn.dataset.star);
        document.querySelectorAll('.star-btn').forEach((b, idx) => {
            b.classList.toggle('text-yellow-300', idx < hoverVal);
            b.classList.toggle('text-yellow-400', idx < currentRating && idx >= hoverVal);
            b.classList.toggle('text-gray-200', idx >= hoverVal && idx >= currentRating);
        });
    });
    btn.addEventListener('mouseleave', () => setRating(currentRating));
});

// Tutup modal dengan Escape key
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRatingModal(); });
</script>
@endpush
