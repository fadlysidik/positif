@extends('layout.admin')

@section('content')
<div class="container mt-4">
    <h2>{{ isset($barang) ? 'Edit Barang' : 'Tambah Barang' }}</h2>

    <a href="{{ route('barang.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ isset($barang) ? route('barang.update', $barang->id) : route('barang.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($barang))
        @method('PUT')
        @endif

        <div class="mb-3">
            <label for="produk_id" class="form-label">Produk</label>
            <select class="form-control" id="produk_id" name="produk_id" required>
                <option value="">Pilih Produk</option>
                @foreach ($produk as $item)
                <option value="{{ $item->id }}"
                    {{ old('produk_id', $barang->produk_id ?? '') == $item->id ? 'selected' : '' }}>
                    {{ $item->nama_produk }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                value="{{ old('nama_barang', $barang->nama_barang ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="satuan" class="form-label">Satuan</label>
            <select class="form-control" id="satuan" name="satuan" required>
                <option value="">Pilih Satuan</option>
                <option value="pcs" {{ old('satuan', $barang->satuan ?? '') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                <option value="lusin" {{ old('satuan', $barang->satuan ?? '') == 'lusin' ? 'selected' : '' }}>Lusin</option>
                <option value="kodi" {{ old('satuan', $barang->satuan ?? '') == 'kodi' ? 'selected' : '' }}>Kodi</option>
                <option value="kg" {{ old('satuan', $barang->satuan ?? '') == 'kg' ? 'selected' : '' }}>Kilogram</option>
                <option value="gram" {{ old('satuan', $barang->satuan ?? '') == 'gram' ? 'selected' : '' }}>Pasang</option>
                <option value="gram" {{ old('satuan', $barang->satuan ?? '') == 'gram' ? 'selected' : '' }}>Botol</option>
            </select>
        </div>


        <div class="mb-3">
            <label for="harga_jual" class="form-label">Harga Jual</label>
            <input type="number" step="0.01" class="form-control" id="harga_jual" name="harga_jual"
                value="{{ old('harga_jual', $barang->harga_jual ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok"
                value="{{ old('stok', $barang->stok ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="expired" class="form-label">Tanggal Expired</label>
            <input type="datetime-local" class="form-control" id="expired" name="expired"
                value="{{ old('expired', isset($barang->expired) ? date('Y-m-d\TH:i', strtotime($barang->expired)) : '') }}">
        </div>

        <div class="mb-3">
            <label for="gambar" class="form-label">Upload Gambar</label>
            <input type="file" class="form-control" id="gambar" name="gambar">
            @if(isset($barang) && $barang->gambar)
            <img src="{{ asset('storage/' . $barang->gambar) }}" alt="Gambar Barang" width="100" class="mt-2">
            @endif
        </div>

        <button type="submit" class="btn btn-success">{{ isset($barang) ? 'Update' : 'Simpan' }}</button>
    </form>
</div>
@endsection