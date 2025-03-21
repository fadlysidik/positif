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
        $pengajuan = PengajuanBarang::with(['pelanggan', 'barang'])->latest()->get();
        $pelanggan = Pelanggan::all();
        $barang = Barang::all();

        return view('pengajuan_barang.index', compact('pengajuan', 'pelanggan', 'barang'));
    }

    /**
     * Menyimpan data pengajuan barang baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        PengajuanBarang::create([
            'tgl_pengajuan' => now(),
            'pelanggan_id' => $request->pelanggan_id,
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'deskripsi' => $request->deskripsi,
            'status' => 0,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['success' => 'Pengajuan barang berhasil ditambahkan!']);
    }

    /**
     * Mengupdate data pengajuan barang.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->update([
            'pelanggan_id' => $request->pelanggan_id,
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json(['success' => 'Pengajuan barang berhasil diperbarui!']);
    }

    /**
     * Menghapus pengajuan barang.
     */
    public function destroy($id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->delete();

        return response()->json(['success' => 'Pengajuan barang berhasil dihapus!']);
    }
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
            'success' => 'Status pengajuan barang diperbarui!',
            'status' => $pengajuan->status ? 'Terpenuhi' : 'Belum Terpenuhi'
        ]);
    }

    public function show($id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        return response()->json($pengajuan);
    }

    public function exportExcel()
    {
        return Excel::download(new PengajuanBarangExport, 'pengajuan_barang.xlsx');
    }

    public function exportPDF()
    {
        $pengajuan = PengajuanBarang::with(['pelanggan', 'barang'])->get();
        $pdf = PDF::loadView('pengajuan_barang.pdf', compact('pengajuan'))->setPaper('a4', 'landscape');
        return $pdf->download('pengajuan_barang.pdf');
    }
}
