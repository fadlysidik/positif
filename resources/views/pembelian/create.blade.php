@extends('layout.admin')

@section('content')
<div class="container mt-4">
    <h2>Tambah Pembelian</h2>
    <a href="{{ route('pembelian.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pembelian.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="barang_id" class="form-label">Nama Barang</label>
            <select name="barang_id" id="barang_id" class="form-control select2" required>
                <option value="">Pilih Barang</option>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}" data-harga="{{ $barang->harga_beli }}">
                        {{ $barang->nama_barang }}
                    </option>
                @endforeach
            </select>
        </div>
              
        <div class="mb-3">
            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="pemasok_id" class="form-label">Pemasok</label>
            <select name="pemasok_id" id="pemasok_id" class="form-control" required>
                <option value="">Pilih Pemasok</option>
                @foreach($pemasoks as $pemasok)
                    <option value="{{ $pemasok->id }}">{{ $pemasok->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="harga_beli" class="form-label">Harga Beli</label>
            <input type="number" name="harga_beli" id="harga_beli" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">jumlah</label>
            <input type="number" name="jumlah" id="jumlah" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@push('scripts')
<script>
    console.log('test')
    $(document).ready(function() {
        $('#barang_id').select2();
    });
</script>
@endpush

@endsection
