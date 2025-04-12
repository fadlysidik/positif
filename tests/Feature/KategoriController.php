<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Produk;

class KategoriController extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function testStoreSuccesfully()
    {
        $data = [
            'jns_brg_kode' => '234',
            'jn_brg_name' => 'test barang',
        ];
        $login = [
            'email' => 'admin@gmail.com',
            'password' => 'admin123',
        ];
        $response = $this->post('login', $data);

        $response = $this->post('/produk', $data);

        $response->assertStatus(200);
    }
}
