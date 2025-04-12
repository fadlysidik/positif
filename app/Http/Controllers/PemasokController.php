<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasok;

class PemasokController extends Controller
{
    public function index()
    {
        $pemasok = Pemasok::all();
        return view('pemasok.index', compact('pemasok'));
    }

    public function create()
    {
        return view('pemasok.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'alamat' => 'required|string|max:100',
            'no_telp' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        // Generate kode pemasok
        $date = now()->format('Ymd');
        $last = Pemasok::whereDate('created_at', today())->orderByDesc('id')->first();
        $number = $last ? intval(substr($last->kode_pemasok, -4)) + 1 : 1;
        $kode_pemasok = 'PMS-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        Pemasok::create([
            'kode_pemasok' => $kode_pemasok,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
        ]);

        return redirect()->route('pemasok.index')->with('success', 'Pemasok berhasil ditambahkan.');
    }

    public function edit(Pemasok $pemasok)
    {
        return view('pemasok.edit', compact('pemasok'));
    }

    public function update(Request $request, Pemasok $pemasok)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'alamat' => 'required|string|max:100',
            'no_telp' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        $pemasok->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
        ]);

        return redirect()->route('pemasok.index')->with('success', 'Pemasok berhasil diperbarui.');
    }

    public function destroy(Pemasok $pemasok)
    {
        $pemasok->delete();
        return redirect()->route('pemasok.index')->with('success', 'Pemasok berhasil dihapus.');
    }
}
