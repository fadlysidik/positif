@extends('layout.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Pegawai</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('pegawai.create') }}" class="btn btn-primary mb-3">+ Tambah Pegawai</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>No HP</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pegawai as $index => $p)
                <tr>
                    <td>{{ $pegawai->firstItem() + $index }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->no_hp }}</td>
                    <td>{{ $p->alamat }}</td>
                    <td>
                        <a href="{{ route('pegawai.edit', $p->id) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Yakin ingin menghapus pegawai ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data pegawai</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $pegawai->links() }}
</div>
@endsection