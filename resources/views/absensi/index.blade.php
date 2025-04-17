@extends('layout.admin')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Form Absensi Pegawai</h4>

    <div class="mb-3">
        <a href="{{ route('absensi.exportExcel') }}" class="btn btn-success">Export Excel</a>
        <a href="{{ route('absensi.exportPdf') }}" class="btn btn-danger">Import PDF</a>
    </div>

    <form action="{{ route('absensi.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama pegawai atau status" value="{{ request()->search }}">
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </form>

    <form action="{{ route('absensi.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="nama" class="form-label">Nama Pegawai</label>
                <select name="nama" class="form-control select2" required>
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($pegawai as $peg)
                        <option value="{{ $peg->nama }}">{{ $peg->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpha">Alpha</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="jam_masuk" class="form-label">Jam Masuk</label>
                <input type="time" name="jam_masuk" id="jam_masuk" class="form-control" required>
            </div>

            <div class="col-md-2">
                <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" id="waktu_selesai" class="form-control">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Simpan Absensi</button>
            </div>
        </div>
    </form>

    <div class="card mt-4">
        <div class="card-header">Riwayat Absensi</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Pegawai</th>
                        <th>Jam Masuk</th>
                        <th>Waktu Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absensi as $absen)
                        <tr>
                            <td>{{ $absen->tanggal }}</td>
                            <td>{{ $absen->pegawai ? $absen->pegawai->nama : 'Tidak Ditemukan' }}</td>
                            <td>{{ $absen->jam_masuk ?? '-' }}</td>
                            <td>
                                @if($absen->status !== 'Hadir')
                                @if($absen->waktu_selesai)
                                    <span class="badge bg-secondary">
                                        {{ \Carbon\Carbon::parse($absen->waktu_selesai)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            @else
                                @if(is_null($absen->waktu_selesai))
                                    <form action="{{ route('absensi.updateWaktuSelesai', $absen->id) }}" method="POST" onsubmit="return confirm('Tandai waktu selesai sekarang?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-info">Tandai Sekarang</button>
                                    </form>
                                @else
                                    <span class="badge bg-success">
                                        {{ \Carbon\Carbon::parse($absen->waktu_selesai)->format('H:i') }}
                                    </span>
                                    <br>
                                    <small class="text-muted">Selesai bekerja</small>
                                @endif
                            @endif                            
                            </td>                            
                            <td>{{ $absen->status }}</td>
                            <td>
                                <a href="{{ route('absensi.edit', $absen->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('absensi.destroy', $absen->id) }}" method="POST" class="d-inline" onsubmit="return confirmDelete()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $absensi->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    const statusSelect = document.querySelector('select[name="status"]');
    const jamMasukInput = document.getElementById('jam_masuk');
    const jamKeluarInput = document.getElementById('waktu_selesai');

    function toggleJamFields() {
        if (statusSelect.value === 'Hadir') {
            jamMasukInput.disabled = false;
            jamKeluarInput.disabled = false;
        } else {
            jamMasukInput.disabled = true;
            jamKeluarInput.disabled = true;
            jamMasukInput.value = '';
            jamKeluarInput.value = '';
        }
    }

    statusSelect.addEventListener('change', toggleJamFields);
    window.addEventListener('load', toggleJamFields);

    function confirmDelete() {
        return confirm("Apakah Anda yakin ingin menghapus data absensi ini?");
    }
</script>
@endpush

@endsection
