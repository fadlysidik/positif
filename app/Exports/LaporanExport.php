<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanExport implements FromCollection, WithHeadings
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function collection()
    {
        return collect($this->laporan)->map(function ($item) {
            return [
                $item['periode'],
                $item['pendapatan'],
                $item['pengeluaran'],
                $item['pendapatan'] - $item['pengeluaran'],
            ];
        });
    }

    public function headings(): array
    {
        return ['Periode', 'Pendapatan', 'Pengeluaran', 'Laba Bersih'];
    }
}
