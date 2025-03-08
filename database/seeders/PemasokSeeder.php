<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pemasok;

class PemasokSeeder extends Seeder
{
    public function run()
    {
        $pemasokData = [
            [
                'kode_pemasok' => 'PSK-0001',
                'nama' => 'PT Sumber Berkah',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
                'no_telp' => '081234567890',
                'email' => 'sumberberkah@gmail.com',
            ],
            [
                'kode_pemasok' => 'PSK-0002',
                'nama' => 'CV Maju Jaya',
                'alamat' => 'Jl. Mawar No. 5, Bandung',
                'no_telp' => '082345678901',
                'email' => 'majujaya@yahoo.com',
            ],
            [
                'kode_pemasok' => 'PSK-0003',
                'nama' => 'UD Sejahtera',
                'alamat' => 'Jl. Anggrek No. 3, Surabaya',
                'no_telp' => '083456789012',
                'email' => 'sejahtera@gmail.com',
            ],
        ];

        foreach ($pemasokData as $pemasok) {
            Pemasok::create($pemasok);
        }
    }
}
