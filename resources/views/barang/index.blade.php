@extends('layout.admin')

@section('content')
    <div class="container mt-4">
        <h2>Daftar Barang</h2>
        <a href="{{ route('barang.create') }}" class="btn btn-primary mb-3">Tambah Barang</a>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Produk</th>
                    <th>Satuan</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Expired</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barang as $item)
                    <tr>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->produk->nama_produk }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ number_format($item->harga_jual) }}</td>
                        <td>{{ $item->stok }}</td>
                        <td>{{ $item->expired }}</td>
                        <td>
                            @if($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}" alt="" width="50">
                            @else
                                <span class="text-muted">Tidak ada gambar</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('barang.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('barang.destroy', $item->id) }}" method="POST" class="d-inline">
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
