<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

/**
 * Class LaporanController
 * @package App\Http\Controllers
 *
 * Controller ini digunakan untuk mengelola laporan penjualan dan pembelian,
 * serta ekspor data dalam format PDF dan Excel.
 */
class LaporanController extends Controller
{
    /**
     * Menampilkan laporan untuk pemilik berdasarkan filter waktu (harian, bulanan, tahunan).
     *
     * @param Request $request Permintaan HTTP yang berisi parameter filter.
     * @return \Illuminate\Contracts\View\View
     */
    public function laporanOwner(Request $request)
    {
        // Ambil filter dari request, default 'bulanan'
        $filter = $request->get('filter', 'bulanan');

        // Format waktu berdasarkan filter
        $format = match ($filter) {
            'harian' => '%Y-%m-%d',
            'tahunan' => '%Y',
            default => '%Y-%m',
        };

        // Ambil data penjualan dari database
        $penjualan = DB::table('penjualan')
            ->selectRaw("DATE_FORMAT(tgl_faktur, '$format') as periode, SUM(total_bayar) as total")
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        // Ambil data pembelian dari database
        $pembelian = DB::table('pembelian')
            ->selectRaw("DATE_FORMAT(tanggal_masuk, '$format') as periode, SUM(total) as total")
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        // Array laporan sementara
        $laporanArray = [];

        // Masukkan data penjualan ke array laporan
        foreach ($penjualan as $pj) {
            $laporanArray[$pj->periode] = [
                'periode' => $pj->periode,
                'pendapatan' => $pj->total,
                'pengeluaran' => 0,
            ];
        }

        // Masukkan data pembelian ke array laporan
        foreach ($pembelian as $pb) {
            if (isset($laporanArray[$pb->periode])) {
                $laporanArray[$pb->periode]['pengeluaran'] = $pb->total;
            } else {
                $laporanArray[$pb->periode] = [
                    'periode' => $pb->periode,
                    'pendapatan' => 0,
                    'pengeluaran' => $pb->total,
                ];
            }
        }

        // Ubah array menjadi collection dan urutkan
        $laporan = collect($laporanArray)->sortKeys();

        return view('laporan.owner', compact('laporan', 'filter'));
    }

    /**
     * Mengekspor laporan ke dalam file PDF.
     *
     * @param Request $request Permintaan HTTP yang berisi parameter filter.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPDF(Request $request)
    {
        // Ambil data laporan dari helper
        $laporan = $this->getLaporanData($request);

        // Generate PDF dari view
        $pdf = Pdf::loadView('laporan.owner_pdf', compact('laporan'));

        return $pdf->download('laporan-penjualan-pengeluaran.pdf');
    }

    /**
     * Mengekspor laporan ke dalam file Excel.
     *
     * @param Request $request Permintaan HTTP yang berisi parameter filter.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(Request $request)
    {
        // Ambil data laporan dari helper
        $laporan = $this->getLaporanData($request);

        return Excel::download(new LaporanExport($laporan), 'laporan-penjualan-pengeluaran.xlsx');
    }

    /**
     * Mengambil data laporan dari database berdasarkan filter.
     * Method ini digunakan untuk keperluan ekspor PDF dan Excel.
     *
     * @param Request $request Permintaan HTTP yang berisi parameter filter.
     * @return \Illuminate\Support\Collection
     */
    private function getLaporanData(Request $request)
    {
        // Ambil filter dari request
        $filter = $request->get('filter', 'bulanan');

        // Tentukan format berdasarkan filter
        $format = match ($filter) {
            'harian' => '%Y-%m-%d',
            'tahunan' => '%Y',
            default => '%Y-%m',
        };

        // Ambil data penjualan
        $penjualan = DB::table('penjualan')
            ->selectRaw("DATE_FORMAT(tgl_faktur, '$format') as periode, SUM(total_bayar) as total")
            ->groupBy('periode')
            ->get();

        // Ambil data pembelian
        $pembelian = DB::table('pembelian')
            ->selectRaw("DATE_FORMAT(tanggal_masuk, '$format') as periode, SUM(total) as total")
            ->groupBy('periode')
            ->get();

        // Susun data ke dalam array
        $laporanArray = [];

        foreach ($penjualan as $pj) {
            $laporanArray[$pj->periode] = [
                'periode' => $pj->periode,
                'pendapatan' => $pj->total,
                'pengeluaran' => 0,
            ];
        }

        foreach ($pembelian as $pb) {
            if (isset($laporanArray[$pb->periode])) {
                $laporanArray[$pb->periode]['pengeluaran'] = $pb->total;
            } else {
                $laporanArray[$pb->periode] = [
                    'periode' => $pb->periode,
                    'pendapatan' => 0,
                    'pengeluaran' => $pb->total,
                ];
            }
        }

        // Kembalikan hasil sebagai koleksi terurut
        return collect($laporanArray)->sortKeys();
    }
}
