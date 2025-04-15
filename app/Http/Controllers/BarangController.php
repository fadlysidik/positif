<?php

namespace App\Http\Controllers;

use App\Exports\BarangExport;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'expired' => 'nullable|date',
        ]);

        $gambarPath = $request->hasFile('gambar') ? $request->file('gambar')->store('barang_images', 'public') : null;

        Barang::create([
            'kode_barang' => $request->kode_barang,
            'produk_id' => $request->produk_id,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'gambar' => $gambarPath,
            'expired' => $request->expired ?? null,
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
            'expired' => 'nullable|date',
        ]);

        $barang = Barang::findOrFail($id);

        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($barang->gambar);
            $gambarPath = $request->file('gambar')->store('barang_images', 'public');
        } else {
            $gambarPath = $barang->gambar;
        }

        $barang->update([
            'kode_barang' => $request->kode_barang,
            'produk_id' => $request->produk_id,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'gambar' => $gambarPath,
            'expired' => $request->expired ?? null,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        // Hanya hapus gambar jika ada
        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar);
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function laporan()
    {
        $barang = Barang::with('produk')->get();
        return view('barang.laporan', compact('barang'));
    }
    public function exportPDF()
    {
        $barang = Barang::with('produk')->get();
        $pdf = Pdf::loadView('barang.laporan_pdf', compact('barang'));
        return $pdf->download('laporan-barang.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new BarangExport, 'laporan-barang.xlsx');
    }
}
