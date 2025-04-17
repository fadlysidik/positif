<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasok;

/**
 * Class PemasokController
 * @package App\Http\Controllers
 *
 * Controller untuk mengelola data pemasok (CRUD).
 */
class PemasokController extends Controller
{
    /**
     * Menampilkan daftar semua pemasok.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        /// Ambil semua data pemasok dari database
        $pemasok = Pemasok::all();

        /// Tampilkan ke view pemasok.index
        return view('pemasok.index', compact('pemasok'));
    }

    /**
     * Menampilkan form untuk menambahkan pemasok baru.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        /// Tampilkan form input pemasok baru
        return view('pemasok.create');
    }

    /**
     * Menyimpan data pemasok baru ke database.
     *
     * @param Request $request Permintaan HTTP berisi data pemasok.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        /// Validasi data input
        $request->validate([
            'nama' => 'required|string|max:50',
            'alamat' => 'required|string|max:100',
            'no_telp' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        /**
         * Generate kode pemasok otomatis dengan format PMS-YYYYMMDD-XXXX,
         * di mana YYYYMMDD adalah tanggal hari ini dan XXXX adalah nomor urut pemasok pada hari tersebut.
         */
        $date = now()->format('Ymd'); // Ambil tanggal hari ini dalam format Ymd
        $last = Pemasok::whereDate('created_at', today())->orderByDesc('id')->first();
        $number = $last ? intval(substr($last->kode_pemasok, -4)) + 1 : 1; // Nomor urut pemasok
        $kode_pemasok = 'PMS-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT); // Format kode pemasok

        /// Simpan data pemasok ke database
        Pemasok::create([
            'kode_pemasok' => $kode_pemasok,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
        ]);

        return redirect()->route('pemasok.index')->with('success', 'Pemasok berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit data pemasok.
     *
     * @param Pemasok $pemasok Objek model pemasok yang akan diedit.
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Pemasok $pemasok)
    {
        /// Tampilkan form edit pemasok
        return view('pemasok.edit', compact('pemasok'));
    }

    /**
     * Memperbarui data pemasok di database.
     *
     * @param Request $request Permintaan HTTP berisi data pemasok yang diubah.
     * @param Pemasok $pemasok Objek model pemasok yang akan diperbarui.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Pemasok $pemasok)
    {
        /// Validasi input
        $request->validate([
            'nama' => 'required|string|max:50',
            'alamat' => 'required|string|max:100',
            'no_telp' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        /// Update data pemasok di database
        $pemasok->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
        ]);

        return redirect()->route('pemasok.index')->with('success', 'Pemasok berhasil diperbarui.');
    }

    /**
     * Menghapus data pemasok dari database.
     *
     * @param Pemasok $pemasok Objek model pemasok yang akan dihapus.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Pemasok $pemasok)
    {
        /// Hapus pemasok dari database
        $pemasok->delete();

        return redirect()->route('pemasok.index')->with('success', 'Pemasok berhasil dihapus.');
    }
}
