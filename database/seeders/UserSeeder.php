<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Kasir User',
                'email' => 'kasir@example.com',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ],
            [
                'name' => 'Owner User',
                'email' => 'owner@example.com',
                'password' => Hash::make('password'),
                'role' => 'pemilik',
            ],
            [
                'name' => 'User Member',
                'email' => 'member@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
