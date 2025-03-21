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

        return view('dashboard.admin', compact(
            'totalBarang',
            'totalPembelian',
            'totalPenjualan',
            'totalPelanggan',
            'bulanPenjualan',
            'jumlahPenjualan'
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
}
