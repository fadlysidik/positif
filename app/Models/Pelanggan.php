<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $fillable = ['kode_pelanggan', 'nama', 'alamat', 'no_telp', 'email', 'user_id'];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class,);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
