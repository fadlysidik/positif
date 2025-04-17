<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class DashboardController
 * Mengelola tampilan dashboard berdasarkan peran pengguna
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard untuk Admin.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /// Total jumlah barang
        $totalBarang = Barang::count();

        /// Total nilai pembelian dari seluruh data
        $totalPembelian = Pembelian::sum('total');

        /// Total nilai penjualan dari seluruh data
        $totalPenjualan = Penjualan::sum('total_bayar');

        /// Total jumlah pelanggan
        $totalPelanggan = Pelanggan::count();

        /// Ambil data total penjualan per bulan
        $penjualanData = Penjualan::select(
            DB::raw('MONTH(tgl_faktur) as bulan'),
            DB::raw('SUM(total_bayar) as total')
        )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        /// Ambil data total pembelian per bulan
        $pembelianData = Pembelian::select(
            DB::raw('MONTH(tanggal_masuk) as bulan'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        /// Ubah angka bulan menjadi nama bulan
        $bulanPenjualan = $penjualanData->pluck('bulan')->map(function ($bulan) {
            return date("F", mktime(0, 0, 0, $bulan, 1));
        });

        /// Ambil jumlah penjualan
        $jumlahPenjualan = $penjualanData->pluck('total');

        /// Ambil jumlah pembelian
        $jumlahPembelian = $pembelianData->pluck('total');

        /// Hitung keuntungan = Penjualan - Pembelian
        $jumlahKeuntungan = [];
        foreach ($jumlahPenjualan as $key => $penjualan) {
            $pembelian = $jumlahPembelian[$key] ?? 0;
            $jumlahKeuntungan[] = $penjualan - $pembelian;
        }

        /// Total pendapatan (bisa diartikan sebagai total penjualan)
        $jumlahPendapatan = $jumlahPenjualan;

        return view('dashboard.admin', compact(
            'totalBarang',
            'totalPembelian',
            'totalPenjualan',
            'totalPelanggan',
            'bulanPenjualan',
            'jumlahPenjualan',
            'jumlahKeuntungan',
            'jumlahPendapatan'
        ));
    }

    /**
     * Tampilkan dashboard untuk Kasir.
     *
     * @return \Illuminate\View\View
     */
    public function kasirDashboard()
    {
        return view('dashboard.kasir');
    }

    /**
     * Tampilkan dashboard untuk Pemilik.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function pemilikDashboard(Request $request)
    {
        /// Total barang
        $totalBarang = Barang::count();

        /// Total pembelian
        $totalPembelian = Pembelian::sum('total');

        /// Total penjualan
        $totalPenjualan = Penjualan::sum('total_bayar');

        /// Total pelanggan
        $totalPelanggan = Pelanggan::count();

        /// Laba bersih = total penjualan - pembelian
        $labaBersih = $totalPenjualan - $totalPembelian;

        /// Filter data: bulanan (default), harian, atau tahunan
        $filter = $request->get('filter', 'bulanan');

        /// Ambil data berdasarkan filter
        if ($filter == 'harian') {
            $penjualanData = Penjualan::select(
                DB::raw('DATE(tgl_faktur) as label'),
                DB::raw('SUM(total_bayar) as total')
            )
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();

            $pembelianData = Pembelian::select(
                DB::raw('DATE(tanggal_masuk) as label'),
                DB::raw('SUM(total) as total')
            )
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        } elseif ($filter == 'tahunan') {
            $penjualanData = Penjualan::select(
                DB::raw('YEAR(tgl_faktur) as label'),
                DB::raw('SUM(total_bayar) as total')
            )
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();

            $pembelianData = Pembelian::select(
                DB::raw('YEAR(tanggal_masuk) as label'),
                DB::raw('SUM(total) as total')
            )
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        } else {
            $penjualanData = Penjualan::select(
                DB::raw("DATE_FORMAT(tgl_faktur, '%Y-%m') as label"),
                DB::raw('SUM(total_bayar) as total')
            )
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();

            $pembelianData = Pembelian::select(
                DB::raw("DATE_FORMAT(tanggal_masuk, '%Y-%m') as label"),
                DB::raw('SUM(total) as total')
            )
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        }

        /// Daftar label bulan singkat
        $months = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ];

        /// Inisialisasi array bulanan
        $monthlyDataPenjualan = array_fill_keys($months, 0);
        $monthlyDataPembelian = array_fill_keys($months, 0);

        /// Isi array penjualan
        foreach ($penjualanData as $data) {
            $month = date('M', strtotime($data->label));
            $monthlyDataPenjualan[$month] = $data->total;
        }

        /// Isi array pembelian
        foreach ($pembelianData as $data) {
            $month = date('M', strtotime($data->label));
            $monthlyDataPembelian[$month] = $data->total;
        }

        /// Label dan nilai untuk chart
        $labels = array_keys($monthlyDataPenjualan);
        $totalsPenjualan = array_values($monthlyDataPenjualan);
        $totalsPembelian = array_values($monthlyDataPembelian);

        return view('dashboard.pemilik', compact(
            'totalBarang',
            'totalPembelian',
            'totalPenjualan',
            'totalPelanggan',
            'labaBersih',
            'labels',
            'totalsPenjualan',
            'totalsPembelian',
            'filter'
        ));
    }

    /**
     * Tampilkan dashboard untuk Member.
     *
     * @return \Illuminate\View\View
     */
    public function memberDashboard()
    {
        /// Ambil user yang sedang login
        $user = auth()->user();

        /// Total pengajuan barang oleh member
        $totalPengajuan = $user->pengajuanBarang()->count();

        /// Pengajuan yang disetujui
        $pengajuanDisetujui = $user->pengajuanBarang()->where('status', 'disetujui')->count();

        /// Pengajuan yang ditolak
        $pengajuanDitolak = $user->pengajuanBarang()->where('status', 'ditolak')->count();

        /// 5 pengajuan terbaru
        $pengajuanTerbaru = $user->pengajuanBarang()->latest()->take(5)->get();

        return view('dashboard.member', compact(
            'totalPengajuan',
            'pengajuanDisetujui',
            'pengajuanDitolak',
            'pengajuanTerbaru'
        ));
    }
}
