<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@iwakqu.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Sample user
        User::create([
            'name' => 'User Demo',
            'email' => 'user@iwakqu.id',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Products — 9 ikan marinasi pilihan
        $products = [
            [
                'name' => 'Gurame Marinasi Bumbu Bali [500gr]',
                'description' => 'Ikan gurame segar pilihan yang dimarinasi dengan bumbu Bali autentik — serai, kunyit, jahe, dan cabai merah. Tekstur daging tebal dan lembut, aroma rempah kuat. Siap goreng atau bakar, cocok untuk makan siang dan malam bersama keluarga.',
                'price' => 35000,
                'image' => 'gurame.png',
                'stock' => 80,
                'is_active' => false,
            ],
            [
                'name' => 'Nila Marinasi [500gr]',
                'description' => 'Ikan nila pilihan yang direndam dalam bumbu marinasi. Cita rasa manis gurih yang disukai semua kalangan, dari anak-anak hingga orang dewasa. Praktis, tinggal goreng hingga kecokelatan.',
                'price' => 25000,
                'image' => 'nila-marinasi.webp',
                'stock' => 5,
                'is_active' => true,
            ],
            [
                'name' => '(Pre Order) Nila Marinasi [500gr]',
                'description' => '[Pre Order] dikirim besok pagi isi 1-2 ekor',
                'price' => 25000,
                'image' => 'nila-marinasi.webp',
                'stock' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Nila Segar [500gr]',
                'description' => 'Ikan nila segar pilihan isi 1-2 ekor',
                'price' => 22000,
                'image' => 'nila.webp',
                'stock' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Patin Marinasi Asam Pedas [500gr]',
                'description' => 'Ikan patin dengan daging putih lembut, dimarinasi dengan bumbu asam pedas khas Sumatera. Perpaduan asam jawa, cabai rawit, dan rempah pilihan menciptakan sensasi rasa asam segar dan pedas yang memanjakan lidah.',
                'price' => 28000,
                'image' => 'patin.png',
                'stock' => 90,
                'is_active' => false,
            ],
            [
                'name' => 'Bawal Marinasi Tamarind [500gr]',
                'description' => 'Ikan bawal hitam berukuran besar, dimarinasi dengan tamarind (asam jawa), gula merah, dan rempah eksotis. Daging tebal dan padat dengan rasa manis asam yang kompleks. Luar biasa saat dibakar dengan arang untuk aroma smoky yang khas.',
                'price' => 38000,
                'image' => 'bawal.png',
                'stock' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'Mujair Marinasi Pedas Manis [300gr]',
                'description' => 'Ikan mujair segar dengan ukuran sedang, dimarinasi bumbu pedas manis yang menggugah selera. Kombinasi cabai, gula merah, dan rempah pilihan meresap sempurna ke dalam daging. Cocok untuk lauk nasi hangat sehari-hari yang praktis dan lezat.',
                'price' => 20000,
                'image' => 'mujair.png',
                'stock' => 130,
                'is_active' => false,
            ],
            [
                'name' => 'Lele Marinasi Bumbu Rempah [250gr]',
                'description' => 'Ikan lele segar pilihan yang dimarinasi dengan bumbu rempah khas Nusantara. Tekstur daging lembut, aroma harum, dan cita rasa yang kaya. Siap masak, praktis untuk digoreng atau dibakar. Cocok untuk sajian keluarga yang lezat dan bergizi.',
                'price' => 10700,
                'image' => 'lele-marinasi.webp',
                'stock' => 10,
                'is_active' => true,
            ],
            [
                'name' => '(Pre Order) Lele Marinasi Bumbu Rempah [250gr]',
                'description' => '[Pre Order] dikirim besok pagi isi 3-4 ekor',
                'price' => 10700,
                'image' => 'lele-marinasi.webp',
                'stock' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Lele Segar [250gr]',
                'description' => 'Ikan lele segar pilihan isi 3-4 ekor',
                'price' => 7500,
                'image' => 'lele.webp',
                'stock' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Tongkol Marinasi Balado [300gr]',
                'description' => 'Ikan tongkol segar berdaging padat, dimarinasi bumbu balado merah khas Minang yang kaya rasa. Perpaduan cabai merah, bawang, tomat, dan rempah pilihan menghasilkan bumbu yang melekat sempurna. Pedas, gurih, dan menggugah selera.',
                'price' => 22000,
                'image' => 'tongkol.png',
                'stock' => 100,
                'is_active' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Settings
        $this->call(SettingSeeder::class);
        $this->call(RatingSeeder::class);
    }
}
