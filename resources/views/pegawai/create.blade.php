@extends('layout.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Tambah Pegawai</h2>

    <form action="{{ route('pegawai.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nama" class="form-label">Nama Pegawai</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
            @error('nama') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="no_hp" class="form-label">No HP</label>
            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" required>
            @error('no_hp') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
            @error('alamat') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
