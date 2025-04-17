<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

/**
 * Controller untuk mengelola data Produk.
 */
class ProdukController extends Controller
{
    /**
     * Menampilkan semua data produk.
     *
     * @return \Illuminate\View\View Halaman daftar produk
     */
    public function index()
    {
        /** @var \Illuminate\Database\Eloquent\Collection $produk Daftar semua produk */
        $produk = Produk::all();

        return view('produk.index', compact('produk'));
    }

    /**
     * Menampilkan form untuk menambah produk baru.
     *
     * @return \Illuminate\View\View Halaman form tambah produk
     */
    public function create()
    {
        return view('produk.create');
    }

    /**
     * Menyimpan data produk baru ke database.
     *
     * @param Request $request Data permintaan HTTP dari form tambah produk
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman index produk
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_produk' => 'required|max:50'
        ]);

        /**
         * Menyimpan data produk baru ke database.
         * @var Produk $produk
         */
        Produk::create($request->all());

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit produk.
     *
     * @param int $id ID produk yang akan diedit
     * @return \Illuminate\View\View Halaman form edit produk
     */
    public function edit($id)
    {
        /**
         * @var Produk $produk Data produk berdasarkan ID
         */
        $produk = Produk::findOrFail($id);

        return view('produk.create', compact('produk'));
    }

    /**
     * Memperbarui data produk yang ada.
     *
     * @param Request $request Data permintaan HTTP dari form edit
     * @param int $id ID produk yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman index produk
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama_produk' => 'required|max:50'
        ]);

        /**
         * @var Produk $produk Data produk yang akan diperbarui
         */
        $produk = Produk::findOrFail($id);

        // Update data produk
        $produk->update($request->all());

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus data produk berdasarkan ID.
     *
     * @param int $id ID produk yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman index produk
     */
    public function destroy($id)
    {
        /**
         * @var Produk $produk Data produk yang akan dihapus
         */
        $produk = Produk::findOrFail($id);

        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
