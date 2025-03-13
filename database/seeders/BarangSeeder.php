<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $satuan = ['pcs', 'lusin', 'kodi', 'kg', 'gram', 'pasang', 'botol'];
        $now = Carbon::now();

        for ($i = 1; $i <= 10; $i++) {
            DB::table('barang')->insert([
                'kode_barang' => 'BRG-' . strtoupper(Str::random(6)),
                'produk_id' => $i,
                'nama_barang' => 'Barang ' . $i,
                'satuan' => $satuan[array_rand($satuan)],
                'harga_jual' => rand(10000, 50000),
                'stok' => rand(10, 100),
                'gambar' => null,
                'expired' => $now->addDays(rand(30, 365)),
                'user_id' => 1, // Ubah sesuai user yang ada
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
