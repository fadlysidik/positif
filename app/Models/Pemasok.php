<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    use HasFactory;

    protected $table = 'pemasok';
    protected $fillable = ['kode_pemasok', 'nama', 'alamat', 'no_telp', 'email'];

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'pemasok_id');
    }
}
