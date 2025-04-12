<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class LaporanController extends Controller
{
    public function laporanOwner(Request $request)
    {
        $filter = $request->get('filter', 'bulanan');
        $format = match ($filter) {
            'harian' => '%Y-%m-%d',
            'tahunan' => '%Y',
            default => '%Y-%m',
        };

        $penjualan = DB::table('penjualan')
            ->selectRaw("DATE_FORMAT(tgl_faktur, '$format') as periode, SUM(total_bayar) as total")
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        $pembelian = DB::table('pembelian')
            ->selectRaw("DATE_FORMAT(tanggal_masuk, '$format') as periode, SUM(total) as total")
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        // Gabungkan data berdasarkan periode
        $laporan = collect();

        foreach ($penjualan as $pj) {
            $laporan[$pj->periode] = [
                'periode' => $pj->periode,
                'pendapatan' => $pj->total,
                'pengeluaran' => 0,
            ];
        }

        foreach ($pembelian as $pb) {
            if (isset($laporan[$pb->periode])) {
                $laporan[$pb->periode]['pengeluaran'] = $pb->total;
            } else {
                $laporan[$pb->periode] = [
                    'periode' => $pb->periode,
                    'pendapatan' => 0,
                    'pengeluaran' => $pb->total,
                ];
            }
        }

        $laporan = $laporan->sortKeys();

        return view('laporan.owner', compact('laporan', 'filter'));
    }
    public function exportPDF(Request $request)
    {
        $laporan = $this->getLaporanData($request);
        $pdf = Pdf::loadView('laporan.owner_pdf', compact('laporan'));
        return $pdf->download('laporan-penjualan-pengeluaran.pdf');
    }

    public function exportExcel(Request $request)
    {
        $laporan = $this->getLaporanData($request);
        return Excel::download(new LaporanExport($laporan), 'laporan-penjualan-pengeluaran.xlsx');
    }

    // Helper untuk ambil data laporan
    private function getLaporanData(Request $request)
    {
        $filter = $request->get('filter', 'bulanan');
        $format = match ($filter) {
            'harian' => '%Y-%m-%d',
            'tahunan' => '%Y',
            default => '%Y-%m',
        };

        $penjualan = DB::table('penjualan')
            ->selectRaw("DATE_FORMAT(tgl_faktur, '$format') as periode, SUM(total_bayar) as total")
            ->groupBy('periode')
            ->get();

        $pembelian = DB::table('pembelian')
            ->selectRaw("DATE_FORMAT(tanggal_masuk, '$format') as periode, SUM(total) as total")
            ->groupBy('periode')
            ->get();

        $laporan = collect();

        foreach ($penjualan as $pj) {
            $laporan[$pj->periode] = [
                'periode' => $pj->periode,
                'pendapatan' => $pj->total,
                'pengeluaran' => 0,
            ];
        }

        foreach ($pembelian as $pb) {
            if (isset($laporan[$pb->periode])) {
                $laporan[$pb->periode]['pengeluaran'] = $pb->total;
            } else {
                $laporan[$pb->periode] = [
                    'periode' => $pb->periode,
                    'pendapatan' => 0,
                    'pengeluaran' => $pb->total,
                ];
            }
        }

        return $laporan->sortKeys();
    }
}
