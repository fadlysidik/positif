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
use Carbon\Carbon;

class PengajuanBarangController extends Controller
{
    /**
     * Menampilkan daftar pengajuan barang.
     */

    public function index()
    {
        // Ambil semua pengajuan yang masih pending (status = 0)
        $pengajuanPending = PengajuanBarang::where('status', 0)->get();

        foreach ($pengajuanPending as $pengajuan) {
            $tglPengajuan = Carbon::parse($pengajuan->tgl_pengajuan);
            $batasWaktu = $tglPengajuan->addDays(5);

            if (Carbon::now()->greaterThan($batasWaktu)) {
                $pengajuan->update([
                    'status' => 2 // misalnya 2 untuk "ditolak otomatis"
                ]);
            }
        }

        $pengajuan = PengajuanBarang::with(['pelanggan.user'])->latest()->get();

        $pelanggan = Pelanggan::all();

        return view('pengajuan_barang.index', compact('pengajuan', 'pelanggan'));
    }

    public function create()
    {
        $pelanggan = Pelanggan::all();
        return view('pengajuan_barang.create', compact('pelanggan'));
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
            'pelanggan_id' => $request->pelanggan_id, // bisa null
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'deskripsi' => $request->deskripsi,
            'status' => 0,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan barang berhasil ditambahkan!');
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
    public function updateStatus($id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->update(['status' => true]);

        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan disetujui.');
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
        return Excel::download(new PengajuanBarangExport, 'pengajuan_barang.admin.xlsx');
    }

    /**
     * Export data ke PDF.
     */
    public function exportPDF()
    {
        $pengajuan = PengajuanBarang::with(['pelanggan', 'barang'])->get();
        $pdf = PDF::loadView('pengajuan_barang.pdf', compact('pengajuan'))->setPaper('a4', 'landscape');
        return $pdf->download('pengajuan_barang.admin.pdf');
    }
}
