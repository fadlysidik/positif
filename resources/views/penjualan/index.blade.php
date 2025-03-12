@extends('layout.kasir')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Daftar Penjualan</h4>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Total Bayar</th>
                <th>Nama Pelanggan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan as $item)
            <tr>
                <td>{{ $item->no_faktur }}</td>
                <td>{{ date('d-m-Y H:i', strtotime($item->tgl_faktur)) }}</td>
                <td>Rp{{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                <td>{{ $item->pelanggan ? $item->pelanggan->nama : 'Tidak Diketahui' }}</td>
                <td>
                    <a href="#" class="btn btn-success btn-sm">Struk</a>
                    <a href="{{ route('penjualan.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $penjualan->links() }}
    </div>
</div>
@endsection