@extends('layout.admin')

@section('content')
    <div class="container mt-4">
        <h2>Daftar Barang</h2>
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('barang.create') }}" class="btn btn-primary">Tambah Barang</a>
            </div>
            <div class="col-md-6">
                <!-- Form Pencarian di sisi kanan -->
                <form method="GET" action="{{ route('barang.index') }}" class="d-flex justify-content-end">
                    <input type="text" name="search" class="form-control w-50" placeholder="Cari berdasarkan kode atau nama barang" value="{{ request()->search }}">
                    <button type="submit" class="btn btn-primary ml-2">Cari</button>
                </form>
            </div>
        </div>
        
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
