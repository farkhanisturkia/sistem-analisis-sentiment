<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $positive = [
            'produk ini sangat bagus',
            'saya sangat suka produk ini',
            'kualitasnya mantap',
            'pelayanan sangat memuaskan',
            'recommended banget',
            'barang sesuai deskripsi',
            'pengiriman cepat',
            'sangat puas',
            'luar biasa bagus',
            'top banget'
        ];

        $negative = [
            'produk ini jelek',
            'sangat mengecewakan',
            'tidak sesuai harapan',
            'kualitas buruk',
            'pengiriman lama',
            'tidak puas',
            'barang rusak',
            'sangat jelek',
            'pelayanan buruk',
            'kapok beli lagi'
        ];

        $neutral = [
            'produk biasa saja',
            'tidak ada yang spesial',
            'standar saja',
            'cukup oke',
            'lumayan',
            'tidak buruk',
            'biasa aja sih',
            'ya begitulah',
            'netral saja',
            'sesuai harga'
        ];

        $comments = [];

        for ($i = 0; $i < 100; $i++) {

            $labelRand = rand(1, 3);

            if ($labelRand === 1) {
                $label = 'positif';
                $comment = $positive[array_rand($positive)];
                $rating = rand(4, 5);
            } elseif ($labelRand === 2) {
                $label = 'negatif';
                $comment = $negative[array_rand($negative)];
                $rating = rand(1, 2);
            } else {
                $label = 'netral';
                $comment = $neutral[array_rand($neutral)];
                $rating = rand(3, 3);
            }

            $comments[] = [
                'user_id' => rand(1, 10),
                'product_id' => rand(1, 10),
                'comment' => $comment,
                'label' => $label,
                'rating' => $rating,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('comments')->insert($comments);
    }
}