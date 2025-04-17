<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;

class AbsensiExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Absensi::with('pegawai')->get()->map(function ($absensi) {
            return [
                'Tanggal' => $absensi->tanggal,
                'Nama Pegawai' => $absensi->pegawai->nama,
                'Jam Masuk' => $absensi->jam_masuk ?? '-',
                'Jam Keluar' => $absensi->jam_keluar ?? '-',
                'Status' => $absensi->status
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Pegawai',
            'Jam Masuk',
            'Jam Keluar',
            'Status'
        ];
    }
}
