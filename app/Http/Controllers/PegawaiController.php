<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PegawaiController extends Controller
{
    /**
     * Menampilkan daftar semua pegawai dengan paginasi.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Logging info saat halaman index pegawai diakses
        Log::info('Akses ke halaman daftar pegawai');

        // Ambil data pegawai dari database, urutkan dari yang terbaru, paginasi 10 data per halaman
        $pegawai = Pegawai::latest()->paginate(10);

        // Debug jumlah data yang diambil
        Log::debug('Data pegawai yang diambil', ['count' => $pegawai->count()]);

        // Tampilkan view index dengan data pegawai
        return view('pegawai.index', compact('pegawai'));
    }

    /**
     * Menampilkan form untuk menambahkan pegawai baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Logging akses form tambah pegawai
        Log::info('Akses ke form tambah pegawai');

        // Tampilkan view form create pegawai
        return view('pegawai.create');
    }

    /**
     * Menyimpan data pegawai baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Logging semua data request yang diterima saat submit form
        Log::info('Request untuk menyimpan pegawai baru', $request->all());

        // Validasi inputan user
        $request->validate([
            'nama' => 'required|string|max:255|unique:pegawai,nama', // Nama wajib unik
            'no_hp' => 'required|string|max:20',                      // Nomor HP wajib diisi
            'alamat' => 'required|string|max:255',                   // Alamat wajib diisi
        ]);

        // Simpan data ke dalam database dan tangkap model hasilnya
        $pegawai = Pegawai::create($request->all());

        // Logging hasil penyimpanan ke log
        Log::debug('Pegawai berhasil disimpan', ['id' => $pegawai->id]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit untuk pegawai tertentu.
     *
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\View\View
     */
    public function edit(Pegawai $pegawai)
    {
        // Logging akses form edit berdasarkan ID pegawai
        Log::info('Akses ke form edit pegawai', ['id' => $pegawai->id]);

        // Tampilkan view edit dan kirimkan data pegawai ke dalam view
        return view('pegawai.edit', compact('pegawai'));
    }

    /**
     * Memperbarui data pegawai berdasarkan input dari form edit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Pegawai $pegawai)
    {
        // Logging request update pegawai dengan data yang dikirim
        Log::info('Request update pegawai', ['id' => $pegawai->id, 'data' => $request->all()]);

        // Validasi input dari form edit
        $request->validate([
            'nama' => 'required|string|max:255|unique:pegawai,nama,' . $pegawai->id, // Cek unik kecuali diri sendiri
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
        ]);

        // Lakukan update data pegawai
        $pegawai->update($request->all());

        // Logging data setelah update
        Log::debug('Pegawai diperbarui', ['id' => $pegawai->id]);

        // Redirect ke index dengan pesan sukses
        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diperbarui.');
    }

    /**
     * Menghapus pegawai dari database.
     *
     * @param  \App\Models\Pegawai  $pegawai
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Pegawai $pegawai)
    {
        // Logging sebelum data dihapus
        Log::warning('Menghapus pegawai', ['id' => $pegawai->id, 'nama' => $pegawai->nama]);

        // Hapus data pegawai dari database
        $pegawai->delete();

        // Logging konfirmasi penghapusan
        Log::info('Pegawai berhasil dihapus', ['id' => $pegawai->id]);

        // Redirect kembali ke index dengan pesan sukses
        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }
}
