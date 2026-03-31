<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [];

        for ($i = 1; $i <= 10; $i++) {
            $products[] = [
                'desc' => 'Produk ke-' . $i . ' kualitas bagus dan menarik',
                'img_filename' => 'product-' . $i . '.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('products')->insert($products);
    }
}