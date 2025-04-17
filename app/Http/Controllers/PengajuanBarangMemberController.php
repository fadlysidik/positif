<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBarang;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class PengajuanBarangMemberController
 * 
 * Controller untuk mengelola pengajuan barang yang dilakukan oleh member/pelanggan.
 * Hanya menangani data milik user yang sedang login.
 * 
 * @package App\Http\Controllers
 */
class PengajuanBarangMemberController extends Controller
{
    /**
     * Menampilkan daftar pengajuan barang yang dilakukan oleh member saat ini.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        /** @var \App\Models\User $user User yang sedang login */
        $user = Auth::user();

        /** @var \Illuminate\Database\Eloquent\Collection $pengajuan Daftar pengajuan barang oleh user */
        $pengajuan = $user->pengajuanBarang()->latest()->get();

        return view('pengajuan_barang.member.index', compact('pengajuan'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan barang baru oleh member.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('pengajuan_barang.member.create');
    }

    /**
     * Menyimpan data pengajuan barang baru yang dibuat oleh member.
     *
     * @param Request $request Permintaan HTTP yang berisi data form pengajuan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        /** @var \App\Models\User $user User yang sedang login */
        $user = Auth::user();

        /** @var \App\Models\Pelanggan|null $pelanggan Relasi pelanggan dari user */
        $pelanggan = $user->pelanggan;

        // Jika user belum memiliki relasi ke data pelanggan
        if (!$pelanggan) {
            return redirect()->back()->withErrors(['error' => 'Akun Anda belum terhubung dengan data pelanggan!']);
        }

        // Buat pengajuan barang baru
        PengajuanBarang::create([
            'kode_pengajuan' => 'PGJ-' . strtoupper(uniqid()), ///< Kode unik pengajuan barang
            'pelanggan_id' => $pelanggan->id, ///< ID pelanggan yang mengajukan
            'user_id' => $user->id, ///< ID user yang login
            'nama_barang' => $request->nama_barang, ///< Nama barang yang diajukan
            'jumlah' => $request->jumlah, ///< Jumlah yang diajukan
            'deskripsi' => $request->deskripsi, ///< Deskripsi tambahan (opsional)
            'tgl_pengajuan' => Carbon::now()->toDateString(), ///< Tanggal pengajuan
            'status' => false, ///< Status default (false = pending)
        ]);

        return redirect()->route('pengajuan_barang.member.index')->with('success', 'Pengajuan berhasil ditambahkan!');
    }
}
