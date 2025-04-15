@extends('layout.kasir')

@section('content')
<style>
    @media print {
        @page {
            size: 58mm auto; /* 58mm lebar, panjang otomatis */
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
        }

        .struk-container {
            transform: scale;
            transform-origin: top left;    
            width: 29mm;
            padding: 5px;
        }

        .no-print {
            display: none !important;
        }
    }

    body {
        font-family: 'Courier New', Courier, monospace;
        font-size: 10px;
    }

    .struk-container {
        width: 58mm;
        margin: auto;
        padding: 5px;
    }

    .text-center {
        text-align: center;
    }

    .line {
        border-top: 1px dashed #000;
        margin: 5px 0;
    }

    .bold {
        font-weight: bold;
    }

    .item-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    .total-label {
        font-weight: bold;
        margin-top: 8px;
        border-top: 1px solid #000;
        padding-top: 4px;
    }
</style>

<div class="struk-container">
    <div class="text-center bold">
        {{ config('app.name', 'POSITIF') }}
    </div>
    <div class="text-center">
        {{ date('D, d/m/Y H:i', strtotime($penjualan->tgl_faktur)) }}
    </div>

    <div class="line"></div>

    @foreach ($penjualan->detailPenjualan as $index => $detail)
        <div class="item-row">
            <div>{{ $index + 1 }}. {{ $detail->barang->nama_barang ?? '-' }}</div>
            <div>Rp{{ number_format($detail->sub_total, 0, ',', '.') }}</div>
        </div>
    @endforeach

    <div class="line"></div>

    <div class="item-row total-label">
        <div>TOTAL</div>
        <div>Rp{{ number_format($penjualan->total_bayar, 0, ',', '.') }}</div>
    </div>

    <div class="line"></div>

    <div class="text-center" style="font-size: 9px;">
        #{{ $penjualan->no_faktur }}#<br>
        Terima kasih atas kunjungan Anda
    </div>
</div>

<div class="text-center mt-3 no-print">
    <button class="btn btn-primary" onclick="window.print()">Cetak Struk</button>
</div>
@endsection
