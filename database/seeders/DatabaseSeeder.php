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
                'name' => 'Gurame Marinasi Bumbu Bali',
                'description' => 'Ikan gurame segar pilihan yang dimarinasi dengan bumbu Bali autentik — serai, kunyit, jahe, dan cabai merah. Tekstur daging tebal dan lembut, aroma rempah kuat. Siap goreng atau bakar, cocok untuk makan siang dan malam bersama keluarga.',
                'price' => 65000,
                'image' => 'gurame.png',
                'stock' => 80,
                'is_active' => true,
            ],
            [
                'name' => 'Nila Marinasi Kecap Manis',
                'description' => 'Ikan nila merah pilihan yang direndam dalam marinasi kecap manis, bawang putih, dan rempah pilihan. Cita rasa manis gurih yang disukai semua kalangan, dari anak-anak hingga orang dewasa. Praktis, tinggal goreng hingga kecokelatan.',
                'price' => 45000,
                'image' => 'nila.png',
                'stock' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Patin Marinasi Asam Pedas',
                'description' => 'Ikan patin dengan daging putih lembut, dimarinasi dengan bumbu asam pedas khas Sumatera. Perpaduan asam jawa, cabai rawit, dan rempah pilihan menciptakan sensasi rasa asam segar dan pedas yang memanjakan lidah.',
                'price' => 55000,
                'image' => 'patin.png',
                'stock' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Bawal Marinasi Tamarind',
                'description' => 'Ikan bawal hitam berukuran besar, dimarinasi dengan tamarind (asam jawa), gula merah, dan rempah eksotis. Daging tebal dan padat dengan rasa manis asam yang kompleks. Luar biasa saat dibakar dengan arang untuk aroma smoky yang khas.',
                'price' => 70000,
                'image' => 'bawal.png',
                'stock' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Kakap Marinasi Saus Tiram',
                'description' => 'Ikan kakap merah premium yang dimarinasi dalam saus tiram, kecap, dan bawang putih. Daging kakap yang lembut dan manis berpadu sempurna dengan marinasi umami saus tiram. Cocok digoreng, dibakar, atau dikukus dengan hasil tetap juicy.',
                'price' => 75000,
                'image' => 'kakap.png',
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Bandeng Marinasi Bumbu Jawa',
                'description' => 'Ikan bandeng presto tanpa duri yang dimarinasi dengan bumbu Jawa klasik — lengkuas, salam, sereh, dan gula kelapa. Daging empuk hingga ke tulang, rasa gurih manis yang nostalgia. Idola keluarga Jawa dari generasi ke generasi.',
                'price' => 48000,
                'image' => 'bandeng.png',
                'stock' => 110,
                'is_active' => true,
            ],
            [
                'name' => 'Mujair Marinasi Pedas Manis',
                'description' => 'Ikan mujair segar dengan ukuran sedang, dimarinasi bumbu pedas manis yang menggugah selera. Kombinasi cabai, gula merah, dan rempah pilihan meresap sempurna ke dalam daging. Cocok untuk lauk nasi hangat sehari-hari yang praktis dan lezat.',
                'price' => 40000,
                'image' => 'mujair.png',
                'stock' => 130,
                'is_active' => true,
            ],
            [
                'name' => 'Lele Marinasi Bumbu Rempah',
                'description' => 'Ikan lele segar pilihan yang dimarinasi dengan bumbu rempah khas Nusantara. Tekstur daging lembut, aroma harum, dan cita rasa yang kaya. Siap masak, praktis untuk digoreng atau dibakar. Cocok untuk sajian keluarga yang lezat dan bergizi.',
                'price' => 38000,
                'image' => 'lele-marinasi.jpg',
                'stock' => 150,
                'is_active' => true,
            ],
            [
                'name' => 'Tongkol Marinasi Balado',
                'description' => 'Ikan tongkol segar berdaging padat, dimarinasi bumbu balado merah khas Minang yang kaya rasa. Perpaduan cabai merah, bawang, tomat, dan rempah pilihan menghasilkan bumbu yang melekat sempurna. Pedas, gurih, dan menggugah selera.',
                'price' => 42000,
                'image' => 'tongkol.png',
                'stock' => 100,
                'is_active' => true,
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
