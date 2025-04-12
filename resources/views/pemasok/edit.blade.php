@extends('layout.admin')

@section('content')
<div class="container mt-4">
    <h2>{{ isset($pemasok) ? 'Edit Pemasok' : 'Tambah Pemasok' }}</h2>
    <a href="{{ route('pemasok.index') }}" class="btn btn-secondary mb-3">Kembali</a>

    <div class="card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($pemasok) ? route('pemasok.update', $pemasok->id) : route('pemasok.store') }}" method="POST">
                @csrf
                @if(isset($pemasok))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control"
                        value="{{ old('nama', $pemasok->nama ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" name="alamat" id="alamat" class="form-control"
                        value="{{ old('alamat', $pemasok->alamat ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="no_telp" class="form-label">No. Telepon</label>
                    <input type="text" name="no_telp" id="no_telp" class="form-control"
                        value="{{ old('no_telp', $pemasok->no_telp ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                        value="{{ old('email', $pemasok->email ?? '') }}">
                </div>

                <button type="submit" class="btn btn-success">
                    {{ isset($pemasok) ? 'Update' : 'Simpan' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
