@extends('layout.kasir')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detail Penjualan</h4>
            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <td><strong>No Faktur</strong></td>
                    <td>: {{ $penjualan->no_faktur }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal</strong></td>
                    <td>: {{ date('d-m-Y H:i', strtotime($penjualan->tgl_faktur)) }}</td>
                </tr>
                <tr>
                    <td><strong>Pelanggan</strong></td>
                    <td>: {{ $penjualan->pelanggan ? $penjualan->pelanggan->nama : 'Umum' }}</td>
                </tr>
                <tr>
                    <td><strong>Total Bayar</strong></td>
                    <td>: Rp{{ number_format($penjualan->total_bayar, 0, ',', '.') }}</td>
                </tr>
            </table>

            <h5 class="mt-4">Barang yang Dibeli</h5>
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->detailPenjualan as $detail)
                    <tr>
                        <td>{{ $detail->barang->nama_barang }}</td>
                        <td class="text-end">Rp{{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $detail->jumlah }}</td>
                        <td class="text-end">Rp{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <button onclick="printStruk()" class="btn btn-primary mt-3">Cetak Struk</button>

    <!-- Area struk tersembunyi -->
    <div id="print-area" style="display: none;">
        <div style="font-family: 'Courier New', monospace; font-size: 12px; width: 58mm; margin: auto;">
            <div style="text-align: center;">
                <i class="fas fa-smile"><sup>*</sup></i> 
                <strong>POSITIF</strong><br>
                Jl. InsyaAllah Lulus UjiKom No. 123<br>
                Telp: 0812-3456-7890
            </div>
            <hr>

            <table style="width:100%;">
                <tr>
                    <td>No Faktur</td>
                    <td style="text-align:right;">{{ $penjualan->no_faktur }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td style="text-align:right;">{{ date('d/m/Y H:i', strtotime($penjualan->tgl_faktur)) }}</td>
                </tr>
                <tr>
                    <td>Pelanggan</td>
                    <td style="text-align:right;">{{ $penjualan->pelanggan->nama ?? 'Umum' }}</td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td style="text-align:right;">{{ $penjualan->user->name ?? '-' }}</td>
                </tr>
            </table>

            <hr>

            @foreach ($penjualan->detailPenjualan as $detail)
                <div>
                    {{ $detail->barang->nama_barang ?? '-' }}<br>
                    {{ $detail->jumlah }} x Rp{{ number_format($detail->harga_jual, 0, ',', '.') }}
                    <span style="float:right;">
                        Rp{{ number_format($detail->sub_total, 0, ',', '.') }}
                    </span>
                </div>
            @endforeach

            <div style="clear:both;"></div>
            <hr>
            <div>
                <strong>TOTAL:</strong>
                <span style="float:right;">
                    <strong>Rp{{ number_format($penjualan->total_bayar, 0, ',', '.') }}</strong>
                </span>
            </div>
            <div style="clear:both;"></div>
            <hr>
            <div style="text-align: center;">
                <p>Terima kasih atas kunjungan Anda</p>
            </div>
        </div>
    </div>
</div>

<script>
function printStruk() {
    const printContents = document.getElementById('print-area').innerHTML;
    const originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // agar halaman kembali seperti semula setelah print
}
</script>
@endsection
