<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'user_id');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'user_id');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'user_id');
    }
    public function pengajuanBarang()
    {
        return $this->hasMany(\App\Models\PengajuanBarang::class, 'user_id');
    }
    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class);
    }
}
