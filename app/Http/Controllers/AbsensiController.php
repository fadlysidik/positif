<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

/**
 * Class AbsensiController
 * Controller ini menangani semua operasi terkait data absensi.
 */
class AbsensiController extends Controller
{
    /**
     * Menampilkan daftar absensi dengan fitur pencarian dan pagination.
     *
     * @param Request $request Request HTTP yang berisi parameter pencarian
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mulai dengan query relasi absensi dan pegawai
        $query = Absensi::with('pegawai');

        // Filter berdasarkan nama pegawai atau status jika ada input pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            })->orWhere('status', 'like', "%{$search}%");
        }

        // Ambil data absensi terbaru dengan pagination
        $absensi = $query->latest()->paginate(5);

        // Ambil semua data pegawai
        $pegawai = Pegawai::all();

        return view('absensi.index', compact('absensi', 'pegawai'));
    }

    /**
     * Menyimpan data absensi baru ke database.
     *
     * @param Request $request Data dari form absensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        // dd($request->all());

        // Validasi inputan sesuai dengan status kehadiran
        $request->validate([
            'nama' => 'required|exists:pegawai,nama',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpha',
            'jam_masuk' => $request->status == 'Hadir' ? 'required|date_format:H:i' : 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
        ]);

        // Cari ID pegawai berdasarkan nama
        $pegawai = Pegawai::where('nama', $request->nama)->first();

        // Jika pegawai tidak ditemukan
        if (!$pegawai) {
            return back()->with('error', 'Pegawai tidak ditemukan.');
        }

        // Simpan data absensi baru
        Absensi::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => now()->toDateString(),
            'jam_masuk' => $request->jam_masuk,
            'waktu_selesai' => $request->waktu_selesai,
            'status' => $request->status
        ]);

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    /**
     * Menampilkan form edit absensi berdasarkan ID.
     *
     * @param int $id ID absensi yang akan diedit
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        $pegawai = Pegawai::all(); // Data pegawai untuk dropdown

        return view('absensi.edit', compact('absensi', 'pegawai'));
    }

    /**
     * Memperbarui data absensi berdasarkan ID.
     *
     * @param Request $request Request yang berisi data baru
     * @param int $id ID absensi yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|exists:pegawai,nama',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpha',
            'jam_masuk' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
        ]);

        $absensi = Absensi::findOrFail($id);
        $pegawai = Pegawai::where('nama', $request->nama)->first();

        if (!$pegawai) {
            return back()->with('error', 'Pegawai tidak ditemukan.');
        }

        // Update data absensi
        $absensi->update([
            'pegawai_id' => $pegawai->id,
            'tanggal' => now()->toDateString(),
            'jam_masuk' => $request->jam_masuk,
            'waktu_selesai' => $request->waktu_selesai,
            'status' => $request->status,
        ]);

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil diperbarui.');
    }

    /**
     * Menghapus data absensi berdasarkan ID.
     *
     * @param int $id ID absensi yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return back()->with('success', 'Absensi berhasil dihapus.');
    }

    /**
     * Mengekspor data absensi ke dalam file Excel.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        return Excel::download(new AbsensiExport, 'absensi.xlsx');
    }

    /**
     * Mengekspor data absensi ke dalam file PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        $absensi = Absensi::with('pegawai')->get();
        $pdf = Pdf::loadView('absensi.pdf', compact('absensi'));
        return $pdf->download('absensi.pdf');
    }

    /**
     * Mengunggah dan memproses file PDF (opsional).
     *
     * @param Request $request File yang diunggah
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importPdf(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->storeAs('public/pdfs', 'absensi.pdf');

        // Contoh placeholder jika parsing PDF ingin dilakukan
        // $data = PdfParser::parse($path);

        return back()->with('success', 'PDF berhasil diunggah.');
    }

    public function updateWaktuSelesai($id)
    {
        $absensi = Absensi::findOrFail($id);

        if ($absensi->status !== 'Hadir') {
            return back()->with('info', 'Hanya absensi dengan status Hadir yang dapat ditandai selesai.');
        }

        if ($absensi->waktu_selesai) {
            return back()->with('info', 'Waktu selesai sudah diisi.');
        }

        $absensi->update([
            'waktu_selesai' => now()->format('H:i'),
        ]);

        return back()->with('success', 'Waktu selesai berhasil diperbarui.');
    }
}
