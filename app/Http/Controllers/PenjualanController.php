<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Pelanggan;
use App\Models\User;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::with(['pelanggan', 'user'])->get();
        $pelanggan = Pelanggan::all();
        $users = User::all();
        return view('penjualan.index', compact('penjualan', 'pelanggan', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_faktur' => 'required|unique:penjualan,no_faktur|max:50',
            'tgl_faktur' => 'required|date',
            'total_bayar' => 'required|numeric',
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'user_id' => 'required|exists:users,id',
        ]);

        Penjualan::create($request->all());

        return redirect()->back()->with('success', 'Penjualan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        return response()->json($penjualan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'no_faktur' => 'required|max:50|unique:penjualan,no_faktur,' . $id,
            'tgl_faktur' => 'required|date',
            'total_bayar' => 'required|numeric',
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update($request->all());

        return redirect()->back()->with('success', 'Penjualan berhasil diperbarui');
    }

    public function destroy($id)
    {
        Penjualan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Penjualan berhasil dihapus');
    }
}
