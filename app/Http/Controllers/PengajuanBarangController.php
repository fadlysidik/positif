<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBarang;
use App\Models\Pelanggan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\PengajuanBarangExport;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;

class PengajuanBarangController extends Controller
{
    /**
     * Menampilkan daftar pengajuan barang.
     */
    public function index()
    {
        $pengajuan = PengajuanBarang::with(['pelanggan'])->latest()->get();
        $pelanggan = Pelanggan::all();


        return view('pengajuan_barang.index', compact('pengajuan', 'pelanggan'));
    }

    /**
     * Menyimpan data pengajuan barang baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        PengajuanBarang::create([
            'tgl_pengajuan' => now(),
            'pelanggan_id' => $request->pelanggan_id,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'deskripsi' => $request->deskripsi,
            'status' => 0,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan barang berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit pengajuan barang.
     */
    // public function edit($id)
    // {
    //     $pengajuan = PengajuanBarang::findOrFail($id);
    //     $pelanggan = Pelanggan::all();

    //     return view('pengajuan_barang.edit', compact('pengajuan', 'pelanggan'));
    // }

    /**
     * Mengupdate data pengajuan barang.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->update([
            'pelanggan_id' => $request->pelanggan_id,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan barang berhasil diperbarui!');
    }

    /**
     * Menghapus pengajuan barang.
     */
    public function destroy($id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->delete();

        return redirect()->back()->with('success', 'Pengajuan barang berhasil dihapus!');
    }

    /**
     * Mengubah status pengajuan barang.
     */
    public function toggleStatus(Request $request, $id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->status = $request->status;

        if ($pengajuan->status) {
            $pengajuan->approved_by = Auth::id();
            $pengajuan->tgl_disetujui = now();
        } else {
            $pengajuan->approved_by = null;
            $pengajuan->tgl_disetujui = null;
        }

        $pengajuan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan barang diperbarui!',
            'status' => $pengajuan->status
        ]);
    }

    /**
     * Menampilkan detail pengajuan barang.
     */
    public function show($id)
    {
        $pengajuan = PengajuanBarang::with('pelanggan')->find($id);

        if (!$pengajuan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($pengajuan);
    }

    /**
     * Export data ke Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new PengajuanBarangExport, 'pengajuan_barang.xlsx');
    }

    /**
     * Export data ke PDF.
     */
    public function exportPDF()
    {
        $pengajuan = PengajuanBarang::with(['pelanggan', 'barang'])->get();
        $pdf = PDF::loadView('pengajuan_barang.pdf', compact('pengajuan'))->setPaper('a4', 'landscape');
        return $pdf->download('pengajuan_barang.pdf');
    }
}
