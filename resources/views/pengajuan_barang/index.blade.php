@extends('layout.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Pengajuan Barang Member</h2>

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('pengajuan_barang.create') }}" class="btn btn-primary">+ Ajukan Barang</a>
        <a href="{{ route('pengajuan_barang.exportExcel') }}" class="btn btn-success">Export Excel</a>
        <a href="{{ route('pengajuan_barang.exportPDF') }}" class="btn btn-danger">Export PDF</a>
    </div>

    {{-- Alert Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>Kode</th>
                <th>Nama Pelanggan</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Deskripsi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengajuan as $item)
                <tr>
                    <td>{{ $item->kode_pengajuan }}</td>
                    <td>{{ $item->pelanggan->user->name ?? '-' }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td>{{ $item->tgl_pengajuan }}</td>
                    <td>
                        @if ($item->status)
                            <span class="badge bg-success">Disetujui</span>
                        @else
                            <span class="badge bg-warning text-dark">Menunggu</span>
                        @endif
                    </td>
                    <td>
                        @if ($item->status == 0)
                            <form action="{{ route('pengajuan_barang.updateStatus', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui pengajuan ini?')">
                                    Setujui
                                </button>
                            </form>
                            <form action="{{ route('pengajuan_barang.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pengajuan ini?')">
                                    Hapus
                                </button>
                            </form>
                        @endif

                        {{-- 
                        <form action="{{ route('pengajuan_barang.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                Hapus
                            </button>
                        </form>
                        --}}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pengajuan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
