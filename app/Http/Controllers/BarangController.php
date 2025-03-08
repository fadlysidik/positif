<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Produk;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::with('produk')->get();
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        $produk = Produk::all();
        return view('barang.create', compact('produk'));
    }

    public function store(Request $request)
{
    $request->validate([
        'produk_id' => 'required|exists:produk,id',
        'nama_barang' => 'required|max:100',
        'satuan' => 'required|max:10',
        'harga_jual' => 'required|numeric',
        'stok' => 'required|integer',
        'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'expired' => 'required|date',
    ]);

    // Generate kode barang unik
    $date = date('Ymd');
    $lastBarang = Barang::whereDate('created_at', now()->toDateString())->latest()->first();
    $number = $lastBarang ? intval(substr($lastBarang->kode_barang, -4)) + 1 : 1;
    $kodeBarang = 'BRG-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

    // Simpan gambar
    $gambarPath = $request->file('gambar')->store('barang_images', 'public');

    Barang::create([
        'kode_barang' => $kodeBarang,
        'produk_id' => $request->produk_id,
        'nama_barang' => $request->nama_barang,
        'satuan' => $request->satuan,
        'harga_jual' => $request->harga_jual,
        'stok' => $request->stok,
        'gambar' => $gambarPath,
        'expired' => $request->expired,
        'user_id' => auth()->id(),
    ]);

    return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
}


    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $produk = Produk::all();
        return view('barang.create', compact('barang', 'produk'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'nama_barang' => 'required|max:100',
            'satuan' => 'required|max:10',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'expired' => 'required|date',
        ]);

        $barang = Barang::findOrFail($id);

        // Jika ada gambar baru, hapus yang lama dan simpan yang baru
        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($barang->gambar);
            $gambarPath = $request->file('gambar')->store('barang_images', 'public');
        } else {
            $gambarPath = $barang->gambar;
        }

        $barang->update([
            'produk_id' => $request->produk_id,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'gambar' => $gambarPath,
            'expired' => $request->expired,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        // Hapus gambar dari penyimpanan
        Storage::disk('public')->delete($barang->gambar);

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
