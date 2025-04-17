@extends('layout.kasir')
@section('content')

<div class="container mt-4">
    <h2 class="fw-bold">Laporan Penjualan & Pengeluaran</h2>

    <div class="d-flex justify-content-end mb-3 gap-2">
        <a href="{{ route('laporan.export.pdf', ['filter' => $filter]) }}" class="btn btn-danger">
            Import PDF
        </a>
        <a href="{{ route('laporan.export.excel', ['filter' => $filter]) }}" class="btn btn-success">
            Export Excel
        </a>
    </div>
    
    <div class="card shadow">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Periode</th>
                        <th>Hari & Tanggal</th>
                        <th>Pendapatan</th>
                        <th>Pengeluaran</th>
                        <th>Laba Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        use Carbon\Carbon;
                        Carbon::setLocale('id');
                    @endphp

                    @forelse ($laporan as $item)
                        @php
                            $tanggal = Carbon::parse($item['periode']);
                            $tanggalLengkap = match ($filter) {
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
                            <td class="fw-bold text-success">
                                Rp {{ number_format($item['pendapatan'] - $item['pengeluaran'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
