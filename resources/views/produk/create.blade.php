@extends('layout.admin')

@section('content')
<div class="container mt-4">
    <h2>{{ isset($produk) ? 'Edit Produk' : 'Tambah Produk' }}</h2>

    <a href="{{ route('produk.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($produk) ? route('produk.update', $produk->id) : route('produk.store') }}" method="POST">
        @csrf
        @if(isset($produk))
            @method('POST')
        @endif

        <div class="mb-3">
            <label for="nama_produk" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" required>
        </div>

        <button type="submit" class="btn btn-success">{{ isset($produk) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection
