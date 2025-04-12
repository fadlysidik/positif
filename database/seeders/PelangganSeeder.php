<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pelanggan')->insert([
            [
                'user_id' => 4,
                'nama' => 'Fatan',
                'kode_pelanggan' => 'PLG001',
                'alamat' => 'Selakopi',
                'no_telp' => '081234567890',
                'email' => 'fatanh@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'nama' => 'Nuy',
                'kode_pelanggan' => 'PLG002',
                'alamat' => 'Selakopi',
                'no_telp' => '081234567891',
                'email' => 'nuy@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'user_id' => 4,
            //     'nama' => 'Rina',
            //     'kode_pelanggan' => 'PLG003',
            //     'alamat' => 'Cianjur',
            //     'no_telp' => '081234567892',
            //     'email' => 'rina@example.com',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'user_id' => 4,
            //     'nama' => 'Dimas',
            //     'kode_pelanggan' => 'PLG004',
            //     'alamat' => 'Bandung',
            //     'no_telp' => '081234567893',
            //     'email' => 'dimas@example.com',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'user_id' => 4,
            //     'nama' => 'Siti',
            //     'kode_pelanggan' => 'PLG005',
            //     'alamat' => 'Jakarta',
            //     'no_telp' => '081234567894',
            //     'email' => 'siti@example.com',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'user_id' => 4,
            //     'nama' => 'Rizky',
            //     'kode_pelanggan' => 'PLG006',
            //     'alamat' => 'Bogor',
            //     'no_telp' => '081234567895',
            //     'email' => 'rizky@example.com',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'user_id' => 4,
            //     'nama' => 'Toni',
            //     'kode_pelanggan' => 'PLG007',
            //     'alamat' => 'Depok',
            //     'no_telp' => '081234567896',
            //     'email' => 'toni@example.com',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]
        ]);
    }
}
