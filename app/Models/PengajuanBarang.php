<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PengajuanBarang extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_barang';

    protected $fillable = [
        'kode_pengajuan',
        'tgl_pengajuan',
        'pelanggan_id',
        'nama_barang',
        'jumlah',
        'deskripsi',
        'status',
        'user_id',
        'approved_by',
        'tgl_disetujui'
    ];

    protected $casts = [
        'status' => 'boolean',
        'tgl_pengajuan' => 'datetime',
        'tgl_disetujui' => 'datetime',
    ];

    /**
     * Generate kode pengajuan otomatis sebelum membuat record baru.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->kode_pengajuan = 'PGJ-' . strtoupper(Str::random(6));
        });
    }


    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            1 => 'Disetujui',
            2 => 'Ditolak Otomatis',
            default => 'Menunggu',
        };
    }

    public function getFormattedTglPengajuanAttribute()
    {
        return $this->tgl_pengajuan ? $this->tgl_pengajuan->format('d-m-Y') : '-';
    }

    public function getFormattedTglDisetujuiAttribute()
    {
        return $this->tgl_disetujui ? $this->tgl_disetujui->format('d-m-Y') : '-';
    }
}
