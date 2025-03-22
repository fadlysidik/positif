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
        return view('dashboard.kasir'); // Tampilan dashboard kasir
    }

    // Untuk Pemilik
    public function pemilikDashboard()
    {
        $totalBarang = Barang::count();
        $totalPembelian = Pembelian::sum('total');
        $totalPenjualan = Penjualan::sum('total_bayar');
        $totalPelanggan = Pelanggan::count();
        $labaBersih = $totalPenjualan - $totalPembelian;

        // Grafik Penjualan Bulanan
        $penjualanData = Penjualan::select(
            DB::raw('MONTH(tgl_faktur) as bulan'),
            DB::raw('SUM(total_bayar) as total')
        )
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        $bulanPenjualan = $penjualanData->pluck('bulan')->map(function ($bulan) {
            return date("F", mktime(0, 0, 0, $bulan, 1));
        });

        $jumlahPenjualan = $penjualanData->pluck('total');

        return view('dashboard.pemilik', compact(
            'totalBarang',
            'totalPembelian',
            'totalPenjualan',
            'totalPelanggan',
            'labaBersih',
            'bulanPenjualan',
            'jumlahPenjualan'
        ));
    }

    // Untuk Member
    public function memberDashboard()
    {
        return view('dashboard.member');
    }
}
