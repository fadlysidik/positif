<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Pemasok;
use App\Models\Barang;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelians = Pembelian::with('pemasok', 'user')->get();
        return view('pembelian.index', compact('pembelians'));
    }

    public function create()
{
    $pemasoks = Pemasok::all();
    $barangs = Barang::all(); // Pastikan Anda mengambil data barang
    return view('pembelian.create', compact('pemasoks', 'barangs'));
}


public function store(Request $request)
{
    $request->validate([
        'tanggal_masuk' => 'required|date',
        'pemasok_id' => 'required|exists:pemasok,id',
        'barang_id' => 'required|exists:barang,id',
        'harga_beli' => 'required|numeric',
        'jumlah' => 'required|integer|min:1',
    ]);

    // Generate kode masuk otomatis
    $tanggal = date('Ymd', strtotime($request->tanggal_masuk));
    $lastKode = Pembelian::whereDate('tanggal_masuk', $request->tanggal_masuk)
        ->orderBy('kode_masuk', 'desc')
        ->first();

    if ($lastKode) {
        $lastNumber = (int) substr($lastKode->kode_masuk, -3);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '001';
    }

    $kodeMasuk = "PB-{$tanggal}-{$newNumber}";

    // Simpan data pembelian
    $pembelian = Pembelian::create([
        'kode_masuk' => $kodeMasuk,
        'tanggal_masuk' => $request->tanggal_masuk,
        'total' => 0, // Akan diperbarui setelah detail pembelian ditambahkan
        'pemasok_id' => $request->pemasok_id,
        'user_id' => auth()->id(),
    ]);

    // Simpan detail pembelian
    $barang = Barang::findOrFail($request->barang_id);
    $subTotal = $request->jumlah * $request->harga_beli;

    DetailPembelian::create([
        'pembelian_id' => $pembelian->id,
        'barang_id' => $barang->id,
        'harga_beli' => $request->harga_beli,
        'jumlah' => $request->jumlah,
        'sub_total' => $subTotal,
    ]);

    // Update total di tabel pembelian
    $pembelian->total = DetailPembelian::where('pembelian_id', $pembelian->id)->sum('sub_total');
    $pembelian->save();

    return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil.');
}



    public function show($id)
    {
        $pembelian = Pembelian::with('detailPembelian.barang')->findOrFail($id);
        $barangs = Barang::all();
        return view('pembelian.show', compact('pembelian', 'barangs'));
    }

    public function destroy($id)
    {
        Pembelian::findOrFail($id)->delete();
        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus.');
    }
}
