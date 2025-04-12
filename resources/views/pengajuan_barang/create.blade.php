@extends('layout.admin')

@section('content')
<div class="container mt-4">
    <h2>Form Pengajuan Barang</h2>
    <a href="{{ route('pengajuan_barang.index') }}" class="btn btn-secondary mb-3">← Kembali</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pengajuan_barang.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="pelanggan_id" class="form-label">Pilih Pelanggan</label>
            <select name="pelanggan_id" id="pelanggan_id" class="form-select">
                @foreach ($pelanggan as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Ajukan Pengajuan</button>
    </form>
</div>
@endsection
