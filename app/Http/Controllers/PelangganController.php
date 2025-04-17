<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

/**
 * Class PelangganController
 * @package App\Http\Controllers
 *
 * Controller untuk mengelola data pelanggan (CRUD).
 */
class PelangganController extends Controller
{
    /**
     * Menampilkan daftar semua pelanggan.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        /// Ambil semua data pelanggan dari database
        $pelanggan = Pelanggan::all();

        /// Tampilkan ke view pelanggan.index
        return view('pelanggan.index', compact('pelanggan'));
    }

    /**
     * Menampilkan form untuk menambahkan pelanggan baru.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        /// Tampilkan form input pelanggan baru
        return view('pelanggan.create');
    }

    /**
     * Menyimpan data pelanggan baru ke database.
     *
     * @param Request $request Permintaan HTTP berisi data pelanggan.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        /// Validasi data input
        $request->validate([
            'nama' => 'required|string|max:50',
            'alamat' => 'required|string|max:100',
            'no_telp' => 'required|string|max:15',
            'email' => 'nullable|email|max:50|unique:pelanggan,email',
        ]);

        /// Ambil pelanggan terakhir untuk membuat kode otomatis
        $latestPelanggan = Pelanggan::latest()->first();

        /**
         * Ambil angka dari kode pelanggan terakhir dan increment
         * Jika belum ada data, mulai dari 1
         */
        $number = $latestPelanggan ? ((int) substr($latestPelanggan->kode_pelanggan, 4)) + 1 : 1;

        /// Format kode pelanggan: PLG-0001, PLG-0002, dst.
        $kodePelanggan = 'PLG-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        /// Simpan data pelanggan ke database
        Pelanggan::create([
            'kode_pelanggan' => $kodePelanggan,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'user_id' => auth()->id(), // Ambil ID user yang sedang login
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit data pelanggan.
     *
     * @param Pelanggan $pelanggan Objek model pelanggan yang akan diedit.
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Pelanggan $pelanggan)
    {
        /// Tampilkan form edit pelanggan
        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Memperbarui data pelanggan ke database.
     *
     * @param Request $request Permintaan HTTP berisi data pelanggan yang diubah.
     * @param Pelanggan $pelanggan Objek model pelanggan yang akan diperbarui.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        /// Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:200',
            'no_telp' => 'required|string|max:20',
            'email' => 'nullable|email|max:50|unique:pelanggan,email,' . $pelanggan->id,
        ]);

        /// Update data pelanggan di database
        $pelanggan->update($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Menghapus data pelanggan dari database.
     *
     * @param Pelanggan $pelanggan Objek model pelanggan yang akan dihapus.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Pelanggan $pelanggan)
    {
        /// Hapus pelanggan dari database
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
