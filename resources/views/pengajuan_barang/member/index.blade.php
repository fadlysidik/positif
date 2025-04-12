@extends('layout.member')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Pengajuan Barang Saya</h2>
    <a href="{{ route('pengajuan_barang.member.create') }}" class="btn btn-primary mb-3">+ Ajukan Barang</a>


    <!-- Tabel Pengajuan -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama Pelanggan</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Deskripsi</th>
                <th>Status</th>
            </tr>
        </thead>    
        <tbody>
            @forelse ($pengajuan as $item)
                <tr>
                    <td>{{ Auth::user()->name ?? '-' }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td>
                        @if ($item->status == 1)
                            <span class="badge bg-success">Disetujui</span>
                        @elseif ($item->status == 2)
                            <span class="badge bg-danger">Ditolak Otomatis</span>
                        @else
                            <span class="badge bg-warning text-dark">Menunggu</span>
                        @endif
                    </td>
                    
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada pengajuan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection