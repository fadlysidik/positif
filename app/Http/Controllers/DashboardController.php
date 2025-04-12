<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Untuk Admin
    public function index()
    {
        $totalBarang = Barang::count();
        $totalPembelian = Pembelian::sum('total');
        $totalPenjualan = Penjualan::sum('total_bayar');
        $totalPelanggan = Pelanggan::count();

        // Ambil data penjualan per bulan
        $penjualanData = Penjualan::select(
            DB::raw('MONTH(tgl_faktur) as bulan'),
            DB::raw('SUM(total_bayar) as total')
        )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        // Ambil data pembelian per bulan
        $pembelianData = Pembelian::select(
            DB::raw('MONTH(tanggal_masuk) as bulan'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        // Konversi angka bulan ke nama bulan
        $bulanPenjualan = $penjualanData->pluck('bulan')->map(function ($bulan) {
            return date("F", mktime(0, 0, 0, $bulan, 1));
        });

        $jumlahPenjualan = $penjualanData->pluck('total');
        $jumlahPembelian = $pembelianData->pluck('total');

        // Hitung keuntungan: Penjualan - Pembelian
        $jumlahKeuntungan = [];
        foreach ($jumlahPenjualan as $key => $penjualan) {
            $pembelian = $jumlahPembelian[$key] ?? 0; // Jika tidak ada pembelian, gunakan 0
            $jumlahKeuntungan[] = $penjualan - $pembelian;
        }

        // Pendapatan di sini bisa dianggap sebagai total penjualan
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

    // Untuk Kasir
    public function kasirDashboard()
    {
        return view('dashboard.kasir');
    }

    // Untuk Owner
    public function pemilikDashboard(Request $request)
    {
        $totalBarang = Barang::count();
        $totalPembelian = Pembelian::sum('total');
        $totalPenjualan = Penjualan::sum('total_bayar');
        $totalPelanggan = Pelanggan::count();
        $labaBersih = $totalPenjualan - $totalPembelian;

        $filter = $request->get('filter', 'bulanan');

        // Data Penjualan dan Pembelian berdasarkan filter
        if ($filter == 'harian') {
            // Data harian
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
            // Data tahunan
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
        } else { // default bulanan
            // Data bulanan
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

        // Siapkan array bulan dan total penjualan/pembelian yang diinisialisasi dengan 0
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

        $monthlyDataPenjualan = array_fill_keys($months, 0);
        $monthlyDataPembelian = array_fill_keys($months, 0);

        // Mengisi data yang ada ke dalam array $monthlyData
        foreach ($penjualanData as $data) {
            $month = date('M', strtotime($data->label)); // Ambil bulan dari label
            $monthlyDataPenjualan[$month] = $data->total; // Set total penjualan untuk bulan tersebut
        }

        foreach ($pembelianData as $data) {
            $month = date('M', strtotime($data->label)); // Ambil bulan dari label
            $monthlyDataPembelian[$month] = $data->total; // Set total pembelian untuk bulan tersebut
        }

        // Ambil data label dan totals untuk dikirim ke view
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

    // Untuk Member
    public function memberDashboard()
    {
        $user = auth()->user();

        $totalPengajuan = $user->pengajuanBarang()->count();
        $pengajuanDisetujui = $user->pengajuanBarang()->where('status', 'disetujui')->count();
        $pengajuanDitolak = $user->pengajuanBarang()->where('status', 'ditolak')->count();
        $pengajuanTerbaru = $user->pengajuanBarang()->latest()->take(5)->get();

        return view('dashboard.member', compact(
            'totalPengajuan',
            'pengajuanDisetujui',
            'pengajuanDitolak',
            'pengajuanTerbaru'
        ));
    }
}
