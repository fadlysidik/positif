<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PengajuanBarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SettingController;
use App\Models\DetailPenjualan;
use Illuminate\Support\Facades\Route;

// Halaman utama (redirect ke login)
Route::get('/', function () {
    return view('auth.login');
});

// **AUTHENTICATION**
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// **PROTECTED ROUTES** (Hanya untuk pengguna yang sudah login)
Route::middleware('auth')->group(function () {

    // **DASHBOARD BERDASARKAN ROLE**
    Route::middleware('role:admin')->get('/dashboard/admin', [DashboardController::class, 'index'])->name('dashboard.admin');
    Route::middleware('role:kasir')->get('/dashboard/kasir', [DashboardController::class, 'kasirDashboard'])->name('dashboard.kasir');
    Route::middleware('role:pemilik')->get('/dashboard/pemilik', [DashboardController::class, 'pemilikDashboard'])->name('dashboard.pemilik');

    // **PEMASOK**
    Route::resource('pemasok', PemasokController::class);

    // **BARANG**
    Route::resource('barang', BarangController::class);

    // **PEMBELIAN**
    Route::resource('pembelian', PembelianController::class);

    // **PRODUK**
    Route::prefix('produk')->group(function () {
        Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/create', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::post('/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    });

    // **PENJUALAN**
    Route::prefix('penjualan')->group(function () {
        Route::get('/', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/create', [PenjualanController::class, 'create'])->name('penjualan.create');
        Route::post('/store', [PenjualanController::class, 'store'])->name('penjualan.store');
        Route::get('/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::get('/{id}/pembayaran', [PenjualanController::class, 'pembayaran'])->name('penjualan.pembayaran');
        Route::post('/{id}/proses-pembayaran', [PenjualanController::class, 'prosesPembayaran'])->name('penjualan.proses_pembayaran');

        // **DETAIL PENJUALAN (AJAX)**
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

    // **PELANGGAN**
    Route::resource('pelanggan', PelangganController::class);

    // **PENGAJUAN BARANG**
    Route::resource('pengajuan_barang', PengajuanBarangController::class);
    Route::post('/pengajuan_barang', [PengajuanBarangController::class, 'store'])->middleware('web');
    Route::get('/pengajuan_barang/{id}', [PengajuanBarangController::class, 'show']);
    Route::get('/pengajuan_barang/export/excel', [PengajuanBarangController::class, 'exportExcel'])->name('pengajuan.export.excel');
    Route::get('/pengajuan_barang/export/pdf', [PengajuanBarangController::class, 'exportPDF'])->name('pengajuan.export.pdf');



    // **SETTINGS**
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/pengajuan_barang/status/{id}', [PengajuanBarangController::class, 'toggleStatus'])->name('pengajuan_barang.toggleStatus');

    // **LOGOUT**
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
