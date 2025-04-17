<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            PemasokSeeder::class,
            ProdukSeeder::class,
            // BarangSeeder::class,
            PelangganSeeder::class,
            PegawaiSeeder::class,
        ]);
    }
}
