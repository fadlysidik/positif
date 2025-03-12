<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {

    // Dashboard berdasarkan role
    Route::middleware('role:admin')->get('/dashboard.admin', [DashboardController::class, 'index'])->name('dashboard.admin');
    Route::middleware('role:kasir')->get('/dashboard.kasir', [DashboardController::class, 'kasirDashboard'])->name('dashboard.kasir');
    Route::middleware('role:pemilik')->get('/dashboard.pemilik', [DashboardController::class, 'pemilikDashboard'])->name('dashboard.pemilik');

    //pemasok
    Route::resource('pemasok', PemasokController::class);

    //barang
    Route::resource('barang', BarangController::class);

    //pembelian
    Route::resource('pembelian', PembelianController::class);


    //produk
    Route::prefix('produk')->group(function () {
        Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/create', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::post('/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    });

    //penjualan
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
    Route::post('/penjualan/store', [PenjualanController::class, 'store'])->name('penjualan.store');
    Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
    Route::get('/penjualan/{id}/pembayaran', [PenjualanController::class, 'pembayaran'])->name('penjualan.pembayaran');
    Route::post('/penjualan/{id}/proses-pembayaran', [PenjualanController::class, 'prosesPembayaran'])->name('penjualan.proses_pembayaran');
    Route::get('/penjualan/{id}/struk', [PenjualanController::class, 'struk'])->name('penjualan.struk');

    //pelanggan
    Route::resource('pelanggan', PelangganController::class);

    //settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
