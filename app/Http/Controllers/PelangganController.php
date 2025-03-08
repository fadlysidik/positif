<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pemasok;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Pelanggan::all();
        return view('pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:50',
        'alamat' => 'required|string|max:100',
        'no_telp' => 'required|string|max:15',
        'email' => 'nullable|email|max:50|unique:pelanggan,email',
    ]);

    // Generate kode pelanggan otomatis
    $latestPelanggan = Pelanggan::latest()->first();
    $number = $latestPelanggan ? ((int) substr($latestPelanggan->kode_pelanggan, 4)) + 1 : 1;
    $kodePelanggan = 'PLG-' . str_pad($number, 4, '0', STR_PAD_LEFT);

    Pelanggan::create([
        'kode_pelanggan' => $kodePelanggan,
        'nama' => $request->nama,
        'alamat' => $request->alamat,
        'no_telp' => $request->no_telp,
        'email' => $request->email,
    ]);

    return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
}

    

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'kode_pelanggan' => 'required|string|max:50|unique:pelanggan,kode_pelanggan,' . $pelanggan->id,
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:200',
            'no_telp' => 'required|string|max:20',
            'email' => 'nullable|email|max:50|unique:pelanggan,email,' . $pelanggan->id,
        ]);

        $pelanggan->update($request->all());

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
