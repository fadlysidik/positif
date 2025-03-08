<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $produk = [
            ['nama_produk' => 'Makanan'],
            ['nama_produk' => 'Minuman'],
            ['nama_produk' => 'Sepatu'],
            ['nama_produk' => 'Baju'],
            ['nama_produk' => 'Jaket'],
            ['nama_produk' => 'Outer'],
            ['nama_produk' => 'Aksesoris'],
            ['nama_produk' => 'Elektronik'],
            ['nama_produk' => 'Alat Tulis'],
            ['nama_produk' => 'Kesehatan'],
            ['nama_produk' => 'Perabotan'],
        ];

        Produk::insert($produk);
    }
}
