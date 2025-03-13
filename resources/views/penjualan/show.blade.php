@extends('layout.kasir')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Detail Penjualan</h4>
            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card-body" id="struk">
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
</div>
@endsection