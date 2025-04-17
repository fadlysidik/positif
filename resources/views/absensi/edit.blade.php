@extends('layout.admin')

@section('content')
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="container mt-4">
    <h4 class="mb-3">Edit Absensi Pegawai</h4>
    <a href="{{route('absensi.index')}}" class="btn btn-secondary mb-3">Kembali</a>

    <form action="{{ route('absensi.update', $absensi->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="nama" class="form-label">Nama Pegawai</label>
                <select name="nama" class="form-control" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($pegawai as $peg)
                        <option value="{{ $peg->nama }}" {{ $absensi->pegawai->nama == $peg->nama ? 'selected' : '' }}>
                            {{ $peg->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-control" id="status" required>
                    @foreach(['Hadir', 'Izin', 'Sakit', 'Alpha'] as $status)
                        <option value="{{ $status }}" {{ $absensi->status == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="jam_masuk" class="form-label">Jam Masuk</label>
                <input type="time" name="jam_masuk" id="jam_masuk" class="form-control"
                    value="{{ $absensi->jam_masuk }}">
            </div>

            <div class="col-md-2">
                <label for="jam_keluar" class="form-label">Jam Keluar</label>
                <input type="time" name="jam_keluar" id="jam_keluar" class="form-control"
                    value="{{ $absensi->jam_keluar }}">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100">Update</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const statusSelect = document.getElementById('status');
    const jamMasuk = document.getElementById('jam_masuk');
    const jamKeluar = document.getElementById('jam_keluar');

    function toggleInputs() {
        if (statusSelect.value === 'Hadir') {
            jamMasuk.disabled = false;
            jamKeluar.disabled = false;
        } else {
            jamMasuk.disabled = true;
            jamKeluar.disabled = true;
            jamMasuk.value = '';
            jamKeluar.value = '';
        }
    }

    statusSelect.addEventListener('change', toggleInputs);
    window.addEventListener('load', toggleInputs);
</script>
@endpush
