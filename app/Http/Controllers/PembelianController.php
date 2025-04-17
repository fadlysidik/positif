<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Pemasok;
use App\Models\Barang;

/**
 * Class PembelianController
 * @package App\Http\Controllers
 *
 * Controller untuk mengelola transaksi pembelian barang (CRUD).
 */
class PembelianController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi pembelian.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        /// Ambil semua data pembelian dengan relasi pemasok dan user
        $pembelians = Pembelian::with('pemasok', 'user')->get();

        /// Tampilkan ke view pembelian.index
        return view('pembelian.index', compact('pembelians'));
    }

    /**
     * Menampilkan form untuk menambahkan transaksi pembelian baru.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        /// Ambil data pemasok dan barang untuk form input pembelian
        $pemasoks = Pemasok::all();
        $barangs = Barang::all(); // Pastikan Anda mengambil data barang

        /// Tampilkan form input pembelian
        return view('pembelian.create', compact('pemasoks', 'barangs'));
    }

    /**
     * Menyimpan data transaksi pembelian baru ke database.
     *
     * @param Request $request Permintaan HTTP berisi data pembelian.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        /// Validasi data input
        $request->validate([
            'tanggal_masuk' => 'required|date',
            'pemasok_id' => 'required|exists:pemasok,id',
            'barang_id' => 'required|exists:barang,id',
            'harga_beli' => 'required|numeric',
            'jumlah' => 'required|integer|min:1',
        ]);

        /**
         * Generate kode masuk otomatis dengan format PB-YYYYMMDD-XXX,
         * di mana YYYYMMDD adalah tanggal hari ini dan XXX adalah nomor urut pembelian pada hari tersebut.
         */
        $tanggal = date('Ymd', strtotime($request->tanggal_masuk)); // Ambil tanggal hari ini dalam format Ymd
        $lastKode = Pembelian::whereDate('tanggal_masuk', $request->tanggal_masuk)
            ->orderBy('kode_masuk', 'desc')
            ->first();

        /// Tentukan nomor urut pembelian
        $newNumber = $lastKode ? str_pad((int) substr($lastKode->kode_masuk, -3) + 1, 3, '0', STR_PAD_LEFT) : '001';
        $kodeMasuk = "PB-{$tanggal}-{$newNumber}"; // Format kode masuk

        /// Simpan data pembelian ke database
        $pembelian = Pembelian::create([
            'kode_masuk' => $kodeMasuk,
            'tanggal_masuk' => $request->tanggal_masuk,
            'total' => 0, // Akan diperbarui setelah detail pembelian ditambahkan
            'pemasok_id' => $request->pemasok_id,
            'user_id' => auth()->id(),
        ]);

        /// Ambil barang berdasarkan ID
        $barang = Barang::findOrFail($request->barang_id);

        /// Cek apakah barang sudah ada dalam detail_pembelian untuk pembelian ini
        $detailPembelian = DetailPembelian::where('pembelian_id', $pembelian->id)
            ->where('barang_id', $barang->id)
            ->first();

        if ($detailPembelian) {
            /// Jika barang sudah ada, update jumlah dan harga beli
            $detailPembelian->jumlah += $request->jumlah;
            $detailPembelian->harga_beli = $request->harga_beli; // Update harga beli
            $detailPembelian->sub_total = $detailPembelian->jumlah * $request->harga_beli; // Update sub_total
            $detailPembelian->save();
        } else {
            /// Jika barang belum ada, buat detail pembelian baru
            $detailPembelian = DetailPembelian::create([
                'pembelian_id' => $pembelian->id,
                'barang_id' => $barang->id,
                'harga_beli' => $request->harga_beli,
                'jumlah' => $request->jumlah,
                'sub_total' => $request->jumlah * $request->harga_beli, // Hitung sub_total
            ]);
        }

        /// Update total transaksi pembelian
        $pembelian->total = DetailPembelian::where('pembelian_id', $pembelian->id)->sum('sub_total');
        $pembelian->save();

        /// **Update stok barang**
        $barang->stok += $request->jumlah; // Update stok barang
        $barang->harga_jual = $request->harga_beli * 1.2; // Contoh: harga jual = harga beli + 20%
        $barang->save();

        return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil.');
    }

    /**
     * Menampilkan detail transaksi pembelian berdasarkan ID.
     *
     * @param int $id ID dari transaksi pembelian yang akan ditampilkan.
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        /// Ambil data pembelian beserta detail pembelian dan barang terkait
        $pembelian = Pembelian::with('detailPembelian.barang')->findOrFail($id);

        /// Ambil semua barang untuk pemilihan dalam form
        $barangs = Barang::all();

        /// Tampilkan detail pembelian
        return view('pembelian.show', compact('pembelian', 'barangs'));
    }

    /**
     * Menghapus transaksi pembelian dari database.
     *
     * @param int $id ID dari transaksi pembelian yang akan dihapus.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        /// Cari dan hapus transaksi pembelian berdasarkan ID
        Pembelian::findOrFail($id)->delete();

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus.');
    }
}
