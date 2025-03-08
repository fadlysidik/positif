@extends('layout.admin')

@section('content')
<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Detail Pembelian: {{ $pembelian->kode_masuk }}</h2>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">Barang</th>
                <th class="border p-2">Harga Beli</th>
                <th class="border p-2">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian->detailPembelian as $detail)
            <tr>
                <td class="border p-2">{{ $detail->barang->nama_barang }}</td>
                <td class="border p-2">Rp {{ number_format($detail->harga_beli, 2) }}</td>
                <td class="border p-2">{{ $detail->jumlah }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
