<?php

namespace Tests\Feature;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    // use RefreshDatabase;

    // Test non-member (admin) login
    public function testAdminLoginSuccessfully()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $credentials = [
            'email' => 'admin@example.com',
            'password' => 'password',
        ];
        
        $response = $this->post('/login', $credentials);
        $response->assertStatus(302);
        $response->assertRedirect('/dashboard/admin');
        $this->assertAuthenticatedAs($user);
    }

    // Test member login with complete data
    public function testMemberLoginSuccessfully()
    {
        $user = User::create([
            'name' => 'User Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);

        Pelanggan::create([
            'user_id' => $user->id,
            'nama' => 'Fatan',
            'kode_pelanggan' => 'PLG001',
            'alamat' => 'Selakopi',
            'no_telp' => '081234567890',
            'email' => 'fatanh@example.com',
        ]);

        $credentials = [
            'email' => 'member@example.com',
            'password' => 'password',
        ];
        
        $response = $this->post('/login', $credentials);
        $response->assertStatus(302);
        $response->assertRedirect('/dashboard/member');
        $this->assertAuthenticatedAs($user);
    }


    // Test invalid credentials
    public function testLoginWithInvalidCredentials()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin1@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $credentials = [
            'email' => 'admin1@example.com',
            'password' => 'wrongpassword',
        ];
        
        $response = $this->post('/login', $credentials);
        $response->assertStatus(302);
        $response->assertRedirect('/');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}