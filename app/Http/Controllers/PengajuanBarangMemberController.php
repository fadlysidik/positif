<?php

namespace App\Http\Controllers;

use App\Models\PengajuanBarang;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanBarangMemberController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pengajuan = $user->pengajuanBarang()->latest()->get();

        return view('pengajuan_barang.member.index', compact('pengajuan'));
    }

    public function create()
    {
        return view('pengajuan_barang.member.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            return redirect()->back()->withErrors(['error' => 'Akun Anda belum terhubung dengan data pelanggan!']);
        }


        PengajuanBarang::create([
            'kode_pengajuan' => 'PGJ-' . strtoupper(uniqid()),
            'pelanggan_id' => $pelanggan->id,
            'user_id' => $user->id,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'deskripsi' => $request->deskripsi,
            'tgl_pengajuan' => Carbon::now()->toDateString(),
            'status' => false,
        ]);

        return redirect()->route('pengajuan_barang.member.index')->with('success', 'Pengajuan berhasil ditambahkan!');
    }
}
