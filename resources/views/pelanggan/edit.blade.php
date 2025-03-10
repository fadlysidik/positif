@extends('layout.admin')

@section('content')
<div class="container">
    <h2>Edit Pelanggan</h2>
    <a href="{{route('pelanggan.index')}}" class="btn btn-secondary mb-3">Kembali</a>
    <form action="{{ route('pelanggan.update', $pelanggan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $pelanggan->nama }}" required>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" required>{{ $pelanggan->alamat }}</textarea>
        </div>
        <div class="mb-3">
            <label>No. Telepon</label>
            <input type="text" name="no_telp" class="form-control" value="{{ $pelanggan->no_telp }}" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $pelanggan->email }}">
        </div>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection