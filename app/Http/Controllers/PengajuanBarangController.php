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

/**
 * Class PengajuanBarangController
 * 
 * Mengelola proses pengajuan barang dari pelanggan seperti menambah, mengedit, menghapus, dan mengekspor data pengajuan.
 * 
 * @package App\Http\Controllers
 */
class PengajuanBarangController extends Controller
{
    /**
     * Menampilkan daftar pengajuan barang, serta melakukan auto-update untuk status expired.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Ambil semua pengajuan dengan status pending (0)
        $pengajuanPending = PengajuanBarang::where('status', 0)->get();

        // Loop untuk mengecek apakah pengajuan sudah melebihi batas waktu
        foreach ($pengajuanPending as $pengajuan) {
            $tglPengajuan = Carbon::parse($pengajuan->tgl_pengajuan); // Konversi tanggal pengajuan
            $batasWaktu = $tglPengajuan->addDays(5); // Batas 5 hari

            // Jika lewat batas waktu, update status menjadi 2 (ditolak otomatis)
            if (Carbon::now()->greaterThan($batasWaktu)) {
                $pengajuan->update([
                    'status' => 2
                ]);
            }
        }

        // Ambil semua data pengajuan beserta relasi pelanggan dan user
        $pengajuan = PengajuanBarang::with(['pelanggan.user'])->latest()->get();

        // Ambil semua data pelanggan
        $pelanggan = Pelanggan::all();

        // Kirim ke view
        return view('pengajuan_barang.index', compact('pengajuan', 'pelanggan'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan barang baru.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        // Ambil data pelanggan untuk dropdown
        $pelanggan = Pelanggan::all();
        return view('pengajuan_barang.create', compact('pelanggan'));
    }

    /**
     * Menyimpan data pengajuan barang baru.
     *
     * @param Request $request Permintaan HTTP berisi data pengajuan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi data yang dikirim dari form
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        // Simpan data ke dalam tabel pengajuan_barang
        PengajuanBarang::create([
            'tgl_pengajuan' => now(),
            'pelanggan_id' => $request->pelanggan_id,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'deskripsi' => $request->deskripsi,
            'status' => 0, // default pending
            'user_id' => Auth::id(), // pengaju
        ]);

        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan barang berhasil ditambahkan!');
    }

    /**
     * Mengupdate data pengajuan barang berdasarkan ID.
     *
     * @param Request $request Data baru dari form edit
     * @param int $id ID pengajuan yang akan diupdate
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi data baru
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        // Temukan data pengajuan yang akan diperbarui
        $pengajuan = PengajuanBarang::findOrFail($id);

        // Update data
        $pengajuan->update([
            'pelanggan_id' => $request->pelanggan_id,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan barang berhasil diperbarui!');
    }

    /**
     * Menghapus pengajuan barang berdasarkan ID.
     *
     * @param int $id ID pengajuan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Temukan data pengajuan lalu hapus
        $pengajuan = PengajuanBarang::findOrFail($id);
        $pengajuan->delete();

        return redirect()->back()->with('success', 'Pengajuan barang berhasil dihapus!');
    }

    /**
     * Mengubah status pengajuan barang menjadi disetujui.
     *
     * @param int $id ID pengajuan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($id)
    {
        $pengajuan = PengajuanBarang::findOrFail($id);

        // Update status (misal 1 berarti disetujui)
        $pengajuan->update(['status' => true]);

        return redirect()->route('pengajuan_barang.index')->with('success', 'Pengajuan disetujui.');
    }

    /**
     * Menampilkan detail pengajuan barang sebagai response JSON.
     *
     * @param int $id ID pengajuan
     * @return \Illuminate\Http\JsonResponse
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
     * Mengekspor data pengajuan barang ke file Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        return Excel::download(new PengajuanBarangExport, 'pengajuan_barang.admin.xlsx');
    }

    /**
     * Mengekspor data pengajuan barang ke file PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPDF()
    {
        // Ambil semua pengajuan beserta relasi pelanggan dan barang
        $pengajuan = PengajuanBarang::with(['pelanggan', 'barang'])->get();

        // Generate PDF menggunakan view
        $pdf = PDF::loadView('pengajuan_barang.pdf', compact('pengajuan'))->setPaper('a4', 'landscape');

        return $pdf->download('pengajuan_barang.admin.pdf');
    }
}
