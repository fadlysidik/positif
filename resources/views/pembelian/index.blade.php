@extends('layout.admin')

@section('content')
<div class="container mt-4">
    <h2>Daftar Pembelian</h2>
    <a href="{{ route('pembelian.create') }}" class="btn btn-primary mb-3">Tambah Pembelian</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Pemasok</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelians as $pembelian)
                <tr>
                    <td>{{ $pembelian->kode_masuk }}</td>
                    <td>{{ $pembelian->tanggal_masuk }}</td>
                    <td>{{ $pembelian->pemasok->nama }}</td>
                    <td>Rp {{ number_format($pembelian->total, 2) }}</td>
                    <td>
                        <a href="{{ route('pembelian.show', $pembelian->id) }}" class="btn btn-info btn-sm">Detail</a>
                        <form action="{{ route('pembelian.destroy', $pembelian->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
