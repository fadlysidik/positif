<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Barang;
use App\Models\Pelanggan;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PenjualanController extends Controller
{
    /**
     * Menampilkan daftar penjualan dan pelanggan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var array $data Data untuk ditampilkan pada halaman penjualan */
        $data['penjualan'] = Penjualan::with('pelanggan')->paginate(10);
        $data['pelanggan'] = Pelanggan::with('penjualan')->paginate(10);
        return view('penjualan.index')->with($data);
    }

    /**
     * Menampilkan form untuk membuat penjualan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        /** @var \Illuminate\Support\Collection $pelanggan Daftar semua pelanggan */
        $pelanggan = Pelanggan::all();
        /** @var \Illuminate\Support\Collection $barang Daftar semua barang */
        $barang = Barang::all();
        return view('penjualan.create', compact('pelanggan', 'barang'));
    }

    /**
     * Menyimpan data penjualan baru ke database.
     *
     * @param Request $request Request dari form penjualan
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('Memulai proses penyimpanan penjualan.', ['user_id' => auth()->id()]);

        $request->validate([
            'pelanggan_id' => 'nullable',
            'barang_id' => 'required|array',
            'jumlah' => 'required|array',
            'harga_jual' => 'required|array',
            'jumlah_tunai' => 'required|numeric',
            'kembalian' => 'required|numeric',
        ]);

        Log::info('Validasi berhasil.', ['data' => $request->all()]);

        // Periksa stok sebelum transaksi
        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::find($barangId);
            if (!$barang) {
                Log::error("Barang dengan ID {$barangId} tidak ditemukan.");
                return response()->json(['message' => 'Barang tidak ditemukan.'], 404);
            }
            if ($barang->stok < $request->jumlah[$index]) {
                Log::warning("Stok barang {$barang->nama_barang} tidak mencukupi.", ['stok' => $barang->stok]);
                return response()->json([
                    'message' => "Stok barang {$barang->nama_barang} tidak mencukupi! (Stok tersedia: {$barang->stok})"
                ], 400);
            }
        }

        // Generate nomor faktur unik
        $latestPenjualan = Penjualan::latest()->first();
        $number = $latestPenjualan ? ((int) substr($latestPenjualan->no_faktur, 3)) + 1 : 1;
        $noFaktur = 'FKT-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        while (Penjualan::where('no_faktur', $noFaktur)->exists()) {
            $number++;
            $noFaktur = 'FKT-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }

        Log::info('Nomor faktur berhasil dibuat.', ['no_faktur' => $noFaktur]);

        // Simpan data penjualan
        $penjualan = Penjualan::create([
            'no_faktur' => $noFaktur,
            'tgl_faktur' => now(),
            'total_bayar' => 0,
            'pelanggan_id' => $request->pelanggan_id ?? null,
            'user_id' => auth()->id(),
        ]);

        Log::info('Data penjualan berhasil disimpan.', ['penjualan_id' => $penjualan->id]);

        // Simpan detail penjualan & update stok
        $totalBayar = 0;
        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::find($barangId);
            $jumlah = $request->jumlah[$index];
            $subTotal = $request->harga_jual[$index] * $jumlah;
            $totalBayar += $subTotal;

            DetailPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'barang_id' => $barangId,
                'harga_jual' => $request->harga_jual[$index],
                'jumlah' => $jumlah,
                'sub_total' => $subTotal,
            ]);

            $barang->decrement('stok', $jumlah);

            Log::info("Detail penjualan berhasil ditambahkan.", [
                'penjualan_id' => $penjualan->id,
                'barang_id' => $barangId,
                'jumlah' => $jumlah,
                'stok_sisa' => $barang->stok
            ]);
        }

        $penjualan->update([
            'total_bayar' => $totalBayar
        ]);

        try {
            /** @var \Mike42\Escpos\Printer $printer Printer thermal POS */
            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);

            // Header
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("POSITIF\n");
            $printer->setEmphasis(false);
            $printer->text("Jl. Merdeka No. 123, Bandung\n");
            $printer->text("No Faktur: {$penjualan->no_faktur}\n");
            $printer->text(Date('d/m/Y H:i') . "\n");

            $printer->text("Kasir: " . ($penjualan->user->name ?? '-') . "\n");
            $printer->text(str_repeat("-", 32) . "\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Pelanggan: " . ($penjualan->pelanggan->nama ?? '-') . "\n");
            $printer->text(str_repeat("-", 32) . "\n");

            // Item
            foreach ($penjualan->detailPenjualan as $index => $detail) {
                $nama = $detail->barang->nama_barang ?? 'Barang';
                $jumlah = $detail->jumlah;
                $harga = number_format($detail->harga_jual, 0, ',', '.');
                $sub = number_format($detail->sub_total, 0, ',', '.');

                $printer->text(sprintf("%d. %s\n", $index + 1, $nama));
                $printer->text(sprintf("   %dx%s = Rp%s\n", $jumlah, $harga, $sub));
            }

            $printer->text(str_repeat("-", 32) . "\n");

            $printer->setEmphasis(true);
            $printer->text(sprintf("TOTAL   : Rp%s\n", number_format($penjualan->total_bayar, 0, ',', '.')));
            $printer->text(sprintf("BAYAR   : Rp%s\n", number_format($request->jumlah_tunai, 0, ',', '.')));
            $printer->text(sprintf("KEMBALI : Rp%s\n", number_format($request->kembalian, 0, ',', '.')));
            $printer->setEmphasis(false);

            $printer->text(str_repeat("-", 32) . "\n");
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima kasih atas kunjungan Anda\n");
            $printer->pulse();
            $printer->cut();
            $printer->close();
        } catch (\Exception $e) {
            Log::error('Gagal mencetak struk: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Penjualan berhasil ditambahkan.',
            'redirect' => route('penjualan.index')
        ]);
    }

    /**
     * Menampilkan detail dari penjualan berdasarkan ID.
     *
     * @param int $id ID penjualan
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $data['penjualan'] = Penjualan::with(['detailPenjualan', 'detailPenjualan.barang', 'pelanggan'])->findOrFail($id);
        $data['pelanggan'] = $data['penjualan']->pelanggan;

        return view('penjualan.show')->with($data);
    }

    /**
     * Menampilkan halaman pembayaran penjualan.
     *
     * @param int $id ID penjualan
     * @return \Illuminate\View\View
     */
    public function pembayaran($id)
    {
        $penjualan = Penjualan::with('detailPenjualan.barang')->findOrFail($id);
        return view('penjualan.pembayaran', compact('penjualan'));
    }

    /**
     * Memproses pembayaran dan mengubah status menjadi lunas.
     *
     * @param Request $request
     * @param int $id ID penjualan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function prosesPembayaran(Request $request, $id)
    {
        $request->validate([
            'bayar' => 'required|numeric|min:' . Penjualan::findOrFail($id)->total_bayar
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update(['status' => 'Lunas']);

        return redirect()->route('penjualan.struk', $id)->with('success', 'Pembayaran berhasil!');
    }

    /**
     * Menampilkan halaman struk penjualan.
     *
     * @param int $id ID penjualan
     * @return \Illuminate\View\View
     */
    public function cetakStruk($id)
    {
        $penjualan = Penjualan::with(['detailPenjualan.barang', 'pelanggan'])->findOrFail($id);
        return view('penjualan.struk', compact('penjualan'));
    }

    /**
     * Mencari barang berdasarkan barcode.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cariBarang(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $barang = Barang::where('kode_barang', $request->barcode)->first();

        if ($barang) {
            return response()->json([
                'success' => true,
                'barang' => $barang
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Barang tidak ditemukan'
        ], 404);
    }
}
