<!DOCTYPE html>
<html>
<head>
    <title>Laporan PDF</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .title { font-size: 18px; font-weight: bold; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

<div class="title">Laporan Penjualan & Pengeluaran</div>
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
    $tanggalSekarang = Carbon::now()->translatedFormat('l, d F Y');
@endphp

<p style="text-align: center; margin-top: 5px;">Dicetak pada: {{ $tanggalSekarang }}</p>

<table>
    <thead>
        <tr>
            <th>Periode</th>
            <th>Hari & Tanggal</th>
            <th>Pendapatan</th>
            <th>Pengeluaran</th>
            <th>Laba Bersih</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($laporan as $item)
            @php
                $tanggal = Carbon::parse($item['periode']);
                $tanggalLengkap = match(request()->get('filter', 'bulanan')) {
                    'harian' => $tanggal->translatedFormat('l, d F Y'),
                    'bulanan' => $tanggal->translatedFormat('F Y'),
                    'tahunan' => $tanggal->translatedFormat('Y'),
                    default => $item['periode']
                };
            @endphp
            <tr>
                <td>{{ $item['periode'] }}</td>
                <td>{{ $tanggalLengkap }}</td>
                <td>Rp {{ number_format($item['pendapatan'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item['pengeluaran'], 0, ',', '.') }}</td>
                <td>
                    Rp {{ number_format($item['pendapatan'] - $item['pengeluaran'], 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
