<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    public function run()
    {
        // Menambahkan beberapa data pegawai
        Pegawai::create([
            'nama' => 'Budi Santoso',
            'no_hp' => '04252856685',
            'alamat' => 'Jl. Raya No. 1, Jakarta',
        ]);

        Pegawai::create([
            'nama' => 'Siti Aisyah',
            'no_hp' => '074512635652',
            'alamat' => 'Jl. Merdeka No. 12, Surabaya',
        ]);

        Pegawai::create([
            'nama' => 'Joko Prasetyo',
            'no_hp' => '0568564154526',
            'alamat' => 'Jl. Pahlawan No. 34, Bandung',
        ]);

        Pegawai::create([
            'nama' => 'Rina Sari',
            'no_hp' => '087744566826',
            'alamat' => 'Jl. Sudirman No. 7, Yogyakarta',
        ]);
    }
}
