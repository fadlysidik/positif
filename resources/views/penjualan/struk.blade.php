@extends('layout.kasir')

@section('content')
<div class="container text-center">
    <h3>Struk Penjualan</h3>
    <p>No Faktur: {{ $penjualan->no_faktur }}</p>
    <p>Tanggal: {{ $penjualan->tgl_faktur }}</p>
    <p>Pelanggan: {{ $penjualan->pelanggan->nama }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualan->detailPenjualan as $detail)
            <tr>
                <td>{{ $detail->barang->nama }}</td>
                <td>{{ $detail->jumlah }}</td>
                <td>Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <h4>Total: Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</h4>

    <button class="btn btn-primary" onclick="window.print()">Cetak Struk</button>
</div>
@endsection