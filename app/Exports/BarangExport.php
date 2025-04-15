<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Barang::with('produk')->get()->map(function ($item) {
            return [
                'Kode Barang' => $item->kode_barang,
                'Nama Barang' => $item->nama_barang,
                'Kategori' => $item->produk->nama ?? '-',
                'Stok' => $item->stok,
                'Harga' => $item->harga_jual,
            ];
        });
    }

    public function headings(): array
    {
        return ['Kode Barang', 'Nama Barang', 'Kategori', 'Stok', 'Harga'];
    }
}
