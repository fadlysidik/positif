<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $fillable = ['kode_barang', 'produk_id', 'nama_barang', 'satuan', 'harga_jual', 'stok', 'gambar', 'expired', 'user_id'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'barang_id');
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'barang_id');
    }
}
