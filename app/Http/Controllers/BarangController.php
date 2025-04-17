<?php

namespace App\Http\Controllers;

use App\Exports\BarangExport;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @class BarangController
 * @brief Mengelola data barang termasuk CRUD dan export laporan dalam format PDF/Excel.
 */
class BarangController extends Controller
{
    /**
     * @brief Menampilkan daftar barang dengan fitur pencarian.
     * 
     * @param Request $request Permintaan HTTP yang berisi parameter pencarian.
     * @return \Illuminate\View\View Tampilan daftar barang.
     */
    public function index(Request $request)
    {
        /**
         * @var string|null $search Kata kunci pencarian dari input.
         */
        $search = $request->input('search');

        /**
         * @var \Illuminate\Database\Eloquent\Collection $barang Daftar barang yang sudah difilter.
         */
        $barang = Barang::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('kode_barang', 'like', "%$search%")
                    ->orWhere('nama_barang', 'like', "%$search%");
            })
            ->with('produk') // Relasi ke produk
            ->get();

        return view('barang.index', compact('barang'));
    }

    /**
     * @brief Menampilkan form untuk menambahkan data barang baru.
     * 
     * @return \Illuminate\View\View Tampilan form tambah barang.
     */
    public function create()
    {
        /**
         * @var \Illuminate\Database\Eloquent\Collection $produk Daftar semua produk.
         */
        $produk = Produk::all();
        return view('barang.create', compact('produk'));
    }

    /**
     * @brief Menyimpan data barang baru ke database.
     * 
     * @param Request $request Data input dari form tambah barang.
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar barang.
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'nama_barang' => 'required|max:100',
            'satuan' => 'required|max:10',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'expired' => 'nullable|date',
        ]);

        /**
         * @var string|null $gambarPath Path penyimpanan gambar barang.
         */
        $gambarPath = $request->hasFile('gambar') ? $request->file('gambar')->store('barang_images', 'public') : null;

        Barang::create([
            'kode_barang' => $request->kode_barang,
            'produk_id' => $request->produk_id,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'gambar' => $gambarPath,
            'expired' => $request->expired ?? null,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * @brief Menampilkan form edit barang.
     * 
     * @param int $id ID barang yang akan diedit.
     * @return \Illuminate\View\View Tampilan form edit barang.
     */
    public function edit($id)
    {
        /**
         * @var Barang $barang Data barang yang akan diedit.
         * @var \Illuminate\Database\Eloquent\Collection $produk Daftar produk untuk dropdown.
         */
        $barang = Barang::findOrFail($id);
        $produk = Produk::all();
        return view('barang.create', compact('barang', 'produk'));
    }

    /**
     * @brief Memperbarui data barang di database.
     * 
     * @param Request $request Data input dari form edit barang.
     * @param int $id ID barang yang akan diperbarui.
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar barang.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'nama_barang' => 'required|max:100',
            'satuan' => 'required|max:10',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'expired' => 'nullable|date',
        ]);

        /**
         * @var Barang $barang Data barang yang akan diperbarui.
         */
        $barang = Barang::findOrFail($id);

        /**
         * @var string|null $gambarPath Path gambar yang diperbarui (jika ada).
         */
        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($barang->gambar);
            $gambarPath = $request->file('gambar')->store('barang_images', 'public');
        } else {
            $gambarPath = $barang->gambar;
        }

        $barang->update([
            'kode_barang' => $request->kode_barang,
            'produk_id' => $request->produk_id,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'gambar' => $gambarPath,
            'expired' => $request->expired ?? null,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * @brief Menghapus data barang dari database.
     * 
     * @param int $id ID barang yang akan dihapus.
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar barang.
     */
    public function destroy($id)
    {
        /**
         * @var Barang $barang Data barang yang akan dihapus.
         */
        $barang = Barang::findOrFail($id);

        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar);
        }

        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus.');
    }

    /**
     * @brief Menampilkan halaman laporan barang.
     * 
     * @return \Illuminate\View\View Tampilan laporan barang.
     */
    public function laporan()
    {
        /**
         * @var \Illuminate\Database\Eloquent\Collection $barang Data semua barang.
         */
        $barang = Barang::with('produk')->get();
        return view('barang.laporan', compact('barang'));
    }

    /**
     * @brief Mengekspor laporan barang ke format PDF.
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File PDF yang diunduh.
     */
    public function exportPDF()
    {
        /**
         * @var \Illuminate\Database\Eloquent\Collection $barang Data semua barang.
         */
        $barang = Barang::with('produk')->get();

        /**
         * @var \Barryvdh\DomPDF\PDF $pdf Objek PDF yang dibuat dari view.
         */
        $pdf = Pdf::loadView('barang.laporan_pdf', compact('barang'));
        return $pdf->download('laporan-barang.pdf');
    }

    /**
     * @brief Mengekspor laporan barang ke format Excel.
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse File Excel yang diunduh.
     */
    public function exportExcel()
    {
        return Excel::download(new BarangExport, 'laporan-barang.xlsx');
    }
}
