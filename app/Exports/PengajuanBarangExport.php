<?php

namespace App\Exports;

use App\Models\PengajuanBarang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengajuanBarangExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return PengajuanBarang::with(['pelanggan', 'barang'])->get();
    }

    public function headings(): array
    {
        return [
            'Kode Pengajuan',
            'Tanggal Pengajuan',
            'Pelanggan',
            'Barang',
            'Jumlah',
            'Deskripsi',
            'Status'
        ];
    }

    public function map($pengajuan): array
    {
        return [
            $pengajuan->kode_pengajuan,
            $pengajuan->tgl_pengajuan,
            $pengajuan->pelanggan->nama,
            $pengajuan->barang->nama_barang,
            $pengajuan->jumlah,
            $pengajuan->deskripsi,
            $pengajuan->status ? 'Terpenuhi' : 'Belum Terpenuhi'
        ];
    }
}
