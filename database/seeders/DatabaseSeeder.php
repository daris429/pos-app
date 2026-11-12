<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin dan Kasir
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pos.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Kasir Utama',
            'email' => 'kasir@pos.test',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'is_active' => true,
        ]);

        // 2. Kategori dan Produk Contoh
        $categories = [
            'Makanan' => ['Indomie Goreng', 'Biskuit Khong Guan', 'Roti Tawar', 'Minyak Goreng 2L'],
            'Minuman' => ['Kopi Kapal Api', 'Teh Botol Sosro', 'Air Mineral 600ml', 'Susu UHT 1L'],
            'ATK' => ['Buku Tulis A5', 'Pulpen Hitam', 'Pensil 2B', 'Penghapus'],
            'Bumbu Dapur' => ['Garam Dapur', 'Gula Pasir 1kg', 'Kecap Manis', 'Saus Sambal'],
            'Pembersih' => ['Sabun Cuci Tangan', 'Deterjen 800g', 'Pembersih Lantai', 'Shampoo 160ml']
        ];

        $i = 1;
        foreach ($categories as $catName => $products) {
            $cat = Category::create(['name' => $catName, 'description' => 'Kategori ' . $catName]);
            foreach ($products as $pName) {
                Product::create([
                    'category_id' => $cat->id,
                    'sku' => 'PRD-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'name' => $pName,
                    'unit' => 'pcs',
                    'purchase_price' => 5000,
                    'selling_price' => 7000,
                    'stock' => rand(2, 50),
                    'min_stock' => 5,
                    'is_active' => true,
                ]);
                $i++;
            }
        }

        // 3. Pengaturan Toko
        $settings = [
            ['key' => 'store_name', 'value' => 'Astacode POS Store', 'type' => 'string', 'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Jl. Pendidikan No. 123, Jakarta', 'type' => 'string', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '081234567890', 'type' => 'string', 'group' => 'store'],
            ['key' => 'receipt_footer', 'value' => 'Terima kasih telah berbelanja!', 'type' => 'string', 'group' => 'receipt'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}