<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Barang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::with('pelanggan')->paginate(10);
        return view('penjualan.index', compact('penjualan'));
    }


    public function create()
    {
        $pelanggan = Pelanggan::all();  // Ambil semua pelanggan
        $barang = Barang::all();        // Ambil semua barang
        return view('penjualan.create', compact('pelanggan', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required',
            'barang_id' => 'required|array',
            'jumlah' => 'required|array',
            'harga_jual' => 'required|array',
        ]);

        // Generate nomor faktur otomatis
        $latestPenjualan = Penjualan::latest()->first();
        $number = $latestPenjualan ? ((int) substr($latestPenjualan->no_faktur, 3)) + 1 : 1;
        $noFaktur = 'FKT-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        // Simpan data penjualan
        $penjualan = Penjualan::create([
            'no_faktur' => $noFaktur,
            'tgl_faktur' => now(),
            'total_bayar' => 0, // Akan dihitung setelah detail dimasukkan
            'pelanggan_id' => $request->pelanggan_id,
            'user_id' => auth()->id(),
        ]);

        // Simpan detail penjualan
        $totalBayar = 0;
        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::find($barangId);
            $subTotal = $request->harga_jual[$index] * $request->jumlah[$index];
            $totalBayar += $subTotal;

            DetailPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'barang_id' => $barangId,
                'harga_jual' => $request->harga_jual[$index],
                'jumlah' => $request->jumlah[$index],
                'sub_total' => $subTotal,
            ]);
        }

        // Update total bayar pada tabel penjualan
        $penjualan->update([
            'total_bayar' => $totalBayar
        ]);

        return response()->json(['message' => 'Transaksi berhasil disimpan!'], 200);
    }


    public function show($id)
    {
        // Ambil penjualan berdasarkan ID dan sertakan relasi detail penjualan dan barang
        $penjualan = Penjualan::with(['detailPenjualan', 'detailPenjualan.barang'])->findOrFail($id);

        return view('penjualan.show', compact('penjualan'));
    }

    public function pembayaran($id)
    {
        $penjualan = Penjualan::with('detailPenjualan.barang')->findOrFail($id);
        return view('penjualan.pembayaran', compact('penjualan'));
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $request->validate([
            'bayar' => 'required|numeric|min:' . Penjualan::findOrFail($id)->total_bayar
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update(['status' => 'Lunas']);

        return redirect()->route('penjualan.struk', $id)->with('success', 'Pembayaran berhasil!');
    }

    public function struk($id)
    {
        $penjualan = Penjualan::with('detailPenjualan.barang')->findOrFail($id);
        return view('penjualan.struk', compact('penjualan'));
    }
}
