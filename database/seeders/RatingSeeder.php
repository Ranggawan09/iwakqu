<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderRating;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil produk apapun yang ada (tidak spesifik lele)
        $product = Product::where('is_active', true)->first();

        if (!$product) {
            $this->command->error('Tidak ada produk aktif. Silakan jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $reviews = [
            [
                'name' => 'Siti Rahayu',
                'rating' => 5,
                'review' => 'Ikannya segar banget! Bumbunya meresap dan dagingnya lembut. Pasti beli lagi, recommended!',
            ],
            [
                'name' => 'Budi Santoso',
                'rating' => 5,
                'review' => 'Kualitasnya juara. Sangat praktis tinggal goreng untuk lauk sarapan keluarga. Bumbu marinasinya enak!',
            ],
            [
                'name' => 'Dewi Lestari',
                'rating' => 5,
                'review' => 'Harga terjangkau tapi kualitas premium. Sudah berlangganan 3 bulan, tidak pernah mengecewakan!',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'rating' => 5,
                'review' => 'Pengiriman cepat dan packaging sangat aman. Ikannya masih segar saat sampai. Bumbu marinasinya autentik!',
            ],
            [
                'name' => 'Rina Melati',
                'rating' => 4,
                'review' => 'Rasanya enak dan praktis. Variasi ikannya banyak jadi bisa pilih-pilih. Overall mantap, akan pesan lagi!',
            ],
        ];

        foreach ($reviews as $index => $data) {
            // 1. Buat User fiktif
            $user = User::firstOrCreate(
            ['email' => 'customer' . ($index + 10) . '@iwakqu.com'],
            [
                'name' => $data['name'],
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
            );

            // 2. Buat Order (status selesai)
            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $user->name,
                'address' => 'Jl. Contoh Alamat No. ' . ($index + 1) . ', Kota Contoh',
                'phone' => '0812345678' . $index,
                'total_price' => $product->price * 2,
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
            ]);

            // 3. Buat OrderItem
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 2,
                'price' => $product->price,
            ]);

            // 4. Buat OrderRating
            OrderRating::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'rating' => $data['rating'],
                'review' => $data['review'],
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
            ]);
        }

        $this->command->info('Berhasil menambahkan 5 rating pelanggan IwakQu (4 bintang 5, 1 bintang 4).');
    }
}
