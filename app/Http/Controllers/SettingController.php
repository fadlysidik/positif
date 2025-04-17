<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class SettingController
 * Controller ini menangani pengaturan aplikasi, termasuk pengaturan nama aplikasi, deskripsi, simbol mata uang, dan kuantitas peringatan.
 */
class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan aplikasi.
     *
     * @return \Illuminate\View\View Tampilan halaman pengaturan aplikasi
     */
    public function index()
    {
        // Mengembalikan tampilan halaman pengaturan
        return view('settings.index');
    }

    /**
     * Menyimpan pengaturan yang diperbarui oleh pengguna.
     *
     * @param \Illuminate\Http\Request $request Request yang berisi data pengaturan
     * @return \Illuminate\Http\RedirectResponse Redirect kembali dengan pesan sukses
     */
    public function store(Request $request)
    {
        // Validasi input pengaturan dari pengguna
        $request->validate([
            'app_name' => 'required|string|max:255', // Validasi nama aplikasi
            'app_description' => 'nullable|string', // Validasi deskripsi aplikasi (optional)
            'currency_symbol' => 'required|string|max:5', // Validasi simbol mata uang
            'warning_quantity' => 'required|numeric|min:1', // Validasi kuantitas peringatan minimal 1
        ]);

        // Mengarahkan kembali dengan pesan sukses setelah pengaturan berhasil disimpan
        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
