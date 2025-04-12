@extends('layout.kasir')

@section('content')
<style>
    @media print {
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
        }

        .struk-container {
            width: 80mm;
            margin: auto;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            float: right;
        }

        .no-print {
            display: none;
        }

        .line {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .bold {
            font-weight: bold;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
        }

        .total-label {
            font-weight: bold;
            margin-top: 5px;
        }
    }
</style>

<div class="struk-container">
    <div class="text-center">
        <div class="bold">{{ config('app.name', 'POS Toko') }}</div>
        <div>{{ date('D, d/m/Y H:i', strtotime($penjualan->tgl_faktur)) }}</div>
    </div>

    <div class="line"></div>

    @foreach ($penjualan->detailPenjualan as $index => $detail)
        <div class="item-row">
            <div>{{ $index + 1 }}. {{ $detail->barang->nama_barang ?? '-' }}</div>
            <div>Rp{{ number_format($detail->sub_total, 0, ',', '.') }}</div>
        </div>
    @endforeach

    <div class="line"></div>

    @php
        // Misalnya kamu ingin menambahkan pajak, bisa hitung di sini
        $pajak = 0; // Atau sesuai kebutuhan, misal: $penjualan->total_bayar * 0.1;
    @endphp

    @if ($pajak > 0)
        <div class="item-row">
            <div>Tax</div>
            <div>Rp{{ number_format($pajak, 0, ',', '.') }}</div>
        </div>
    @endif

    <div class="item-row total-label">
        <div>TOTAL:</div>
        <div>Rp{{ number_format($penjualan->total_bayar, 0, ',', '.') }}</div>
    </div>

    <div class="line"></div>

    <div class="text-center" style="font-size: 10px;">
        #{{ $penjualan->no_faktur }}#<br>
        Terima kasih atas kunjungan Anda
    </div>
</div>

<div class="text-center mt-3 no-print">
    <button class="btn btn-primary" onclick="window.print()">Cetak Struk</button>
</div>
@endsection
