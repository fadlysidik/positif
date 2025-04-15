<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Barang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PenjualanController extends Controller
{
    public function index()
    {
        $data['penjualan'] = Penjualan::with('pelanggan')->paginate(10);
        $data['pelanggan'] = Pelanggan::with('penjualan')->paginate(10);
        return view('penjualan.index')->with($data);
    }


    public function create()
    {
        $pelanggan = Pelanggan::all();
        $barang = Barang::all();
        return view('penjualan.create', compact('pelanggan', 'barang'));
    }

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

        // Periksa stok sebelum memproses transaksi
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

        // Pastikan nomor faktur unik
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

        // Simpan detail penjualan & kurangi stok barang
        $totalBayar = 0;
        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::find($barangId);
            $jumlah = $request->jumlah[$index];
            $subTotal = $request->harga_jual[$index] * $jumlah;
            $totalBayar += $subTotal;

            // Simpan detail penjualan
            DetailPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'barang_id' => $barangId,
                'harga_jual' => $request->harga_jual[$index],
                'jumlah' => $jumlah,
                'sub_total' => $subTotal,
            ]);

            // Kurangi stok barang
            $barang->decrement('stok', $jumlah);

            Log::info("Detail penjualan berhasil ditambahkan.", [
                'penjualan_id' => $penjualan->id,
                'barang_id' => $barangId,
                'jumlah' => $jumlah,
                'stok_sisa' => $barang->stok
            ]);
        }

        // Update total bayar di tabel penjualan
        $penjualan->update([
            'total_bayar' => $totalBayar
        ]);

        try {
            $connector = new WindowsPrintConnector("POS-58");
            $printer = new Printer($connector);

            // Header Struk
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("POSITIF\n");
            $printer->setEmphasis(false);
            $printer->text("Jl. Merdeka No. 123, Bandung\n"); // ← Alamat toko di sini
            $printer->text("No Faktur: {$penjualan->no_faktur}\n");
            $printer->text(date('d/m/Y H:i') . "\n");

            // Kasir
            $printer->text("Kasir: " . ($penjualan->user->name ?? '-') . "\n");
            $printer->text(str_repeat("-", 32) . "\n");

            // Pelanggan
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Pelanggan: " . ($penjualan->pelanggan->nama ?? '-') . "\n");
            $printer->text(str_repeat("-", 32) . "\n");

            // Detail item
            foreach ($penjualan->detailPenjualan as $index => $detail) {
                $nama = $detail->barang->nama_barang ?? 'Barang';
                $jumlah = $detail->jumlah;
                $harga = number_format($detail->harga_jual, 0, ',', '.');
                $sub = number_format($detail->sub_total, 0, ',', '.');

                $printer->text(sprintf("%d. %s\n", $index + 1, $nama));
                $printer->text(sprintf("   %dx%s = Rp%s\n", $jumlah, $harga, $sub));
            }

            $printer->text(str_repeat("-", 32) . "\n");

            // Total, Bayar, Kembali
            $printer->setEmphasis(true);
            $printer->text(sprintf("TOTAL   : Rp%s\n", number_format($penjualan->total_bayar, 0, ',', '.')));

            $jumlah_tunai = $request->jumlah_tunai;
            $kembali = $request->kembalian;

            $printer->text(sprintf("BAYAR   : Rp%s\n", number_format($jumlah_tunai, 0, ',', '.')));
            $printer->text(sprintf("KEMBALI : Rp%s\n", number_format($kembali, 0, ',', '.')));
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



        Log::info('Total bayar diperbarui.', ['penjualan_id' => $penjualan->id, 'total_bayar' => $totalBayar]);

        return response()->json([
            'success' => true,
            'message' => 'Penjualan berhasil ditambahkan.',
            'redirect' => route('penjualan.index') // Ganti dengan route tujuan
        ]);
    }
    public function show($id)
    {
        // dd($id);
        // Ambil penjualan berdasarkan ID dan sertakan relasi detail penjualan dan barang

        $data['penjualan'] = Penjualan::with(['detailPenjualan', 'detailPenjualan.barang', 'pelanggan'])->findOrFail($id);
        $data['pelanggan'] = $data['penjualan']->pelanggan;



        return view('penjualan.show')->with($data);
    }

    public function pembayaran($id)
    {
        $penjualan = Penjualan::with('detailPenjualan.barang')->findOrFail($id);
        return view('penjualan.pembayaran', compact('penjualan'));
    }

    public function prosesPembayaran(Request $request, $id)
    {
        $request->validate([
            'bayar' => 'required|numeric|min:' . Penjualan::findOrFail($id)->total_bayar
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update(['status' => 'Lunas']);

        return redirect()->route('penjualan.struk', $id)->with('success', 'Pembayaran berhasil!');
    }
    public function cetakStruk($id)
    {
        $penjualan = Penjualan::with(['detailPenjualan.barang', 'pelanggan'])->findOrFail($id);
        return view('penjualan.struk', compact('penjualan'));
    }
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
