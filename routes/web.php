<?php

use Illuminate\Support\Facades\Route;
use App\Models\DetailPenjualan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PengajuanBarangController;
use App\Http\Controllers\PengajuanBarangMemberController;

// ===============================
// HALAMAN UTAMA
// ===============================
Route::get('/', function () {
    return view('auth.login');
});

// ===============================
// AUTHENTICATION
// ===============================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ===============================
// ROUTE YANG BUTUH LOGIN
// ===============================
Route::middleware('auth')->group(function () {

    // ===========================
    // DASHBOARD PER ROLE
    // ===========================
    Route::middleware('role:admin')->get('/dashboard/admin', [DashboardController::class, 'index'])->name('dashboard.admin');
    Route::middleware('role:kasir')->get('/dashboard/kasir', [DashboardController::class, 'kasirDashboard'])->name('dashboard.kasir');
    Route::middleware('role:pemilik')->get('/dashboard/pemilik', [DashboardController::class, 'pemilikDashboard'])->name('dashboard.pemilik');
    Route::middleware('role:member')->get('/dashboard/member', [DashboardController::class, 'memberDashboard'])->name('dashboard.member');

    // ===========================
    // PEMASOK
    // ===========================
    Route::resource('pemasok', PemasokController::class);

    // ===========================
    // BARANG
    // ===========================
    Route::resource('barang', BarangController::class);

    // ===========================
    // PEMBELIAN
    // ===========================
    Route::resource('pembelian', PembelianController::class);

    // ===========================
    // PRODUK
    // ===========================
    Route::prefix('produk')->group(function () {
        Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/create', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::post('/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    });

    // ===========================
    // PENJUALAN
    // ===========================
    Route::prefix('penjualan')->group(function () {
        Route::get('/', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/create', [PenjualanController::class, 'create'])->name('penjualan.create');
        Route::post('/store', [PenjualanController::class, 'store'])->name('penjualan.store');
        Route::get('{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::get('/{id}/pembayaran', [PenjualanController::class, 'pembayaran'])->name('penjualan.pembayaran');
        Route::post('/{id}/proses-pembayaran', [PenjualanController::class, 'prosesPembayaran'])->name('penjualan.proses_pembayaran');
        Route::get('/penjualan/{id}/struk', [PenjualanController::class, 'cetakStruk'])->name('penjualan.struk');


        // Detail penjualan via AJAX
        Route::get('/{id}/detail', function ($id) {
            $details = DetailPenjualan::where('penjualan_id', $id)
                ->with('barang')
                ->get()
                ->map(function ($detail) {
                    return [
                        'nama_barang' => $detail->barang->nama_barang,
                        'jumlah' => $detail->jumlah,
                        'sub_total' => $detail->sub_total
                    ];
                });
            return response()->json($details);
        });
    });

    // ===========================
    // PELANGGAN
    // ===========================
    Route::resource('pelanggan', PelangganController::class);

    // ===========================
    // SETTING
    // ===========================
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');

    // ===========================
    // 📋 PENGAJUAN BARANG (ADMIN)
    // ===========================
    Route::middleware('role:admin')->prefix('pengajuan_barang')->group(function () {
        Route::get('/', [PengajuanBarangController::class, 'index'])->name('pengajuan_barang.index');
        Route::post('/status/{id}', [PengajuanBarangController::class, 'updateStatus'])->name('pengajuan_barang.updateStatus');
        Route::delete('/{id}', [PengajuanBarangController::class, 'destroy'])->name('pengajuan_barang.destroy');
        Route::get('/pengajuan-barang/create', [PengajuanBarangController::class, 'create'])->name('pengajuan_barang.create');
        Route::post('/store', [PengajuanBarangController::class, 'store'])->name('pengajuan_barang.store');


        // Export
        Route::get('/export-excel', [PengajuanBarangController::class, 'exportExcel'])->name('pengajuan_barang.exportExcel');
        Route::get('/export-pdf', [PengajuanBarangController::class, 'exportPDF'])->name('pengajuan_barang.exportPDF');
    });

    // ===========================
    // PENGAJUAN BARANG (MEMBER)
    // ===========================
    Route::middleware('role:member')->prefix('pengajuan_member')->group(function () {
        Route::get('/', [PengajuanBarangMemberController::class, 'index'])->name('pengajuan_barang.member.index');
        Route::get('/create', [PengajuanBarangMemberController::class, 'create'])->name('pengajuan_barang.member.create');
        Route::post('/', [PengajuanBarangMemberController::class, 'store'])->name('pengajuan_barang.member.store');
    });



    // ===========================
    // Laporan
    // ===========================
    Route::get('/laporan-owner', [LaporanController::class, 'laporanOwner'])->name('laporan.owner');
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPDF'])->name('laporan.export.pdf');
    Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');




    // ===========================
    // LOGOUT
    // ===========================
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
