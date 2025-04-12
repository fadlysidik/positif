@extends('layout.admin')

@section('content')
<div class="container">
    <h2>Daftar Member</h2>
    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">Tambah Member</a>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pelanggan</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No. Telepon</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pelanggan as $key => $p)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $p->kode_pelanggan }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->alamat }}</td>
                    <td>{{ $p->no_telp }}</td>
                    <td>{{ $p->email }}</td>
                    <td>
                        <a href="{{ route('pelanggan.edit', $p->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $p->id }})">Hapus</button>
                    
                        <form id="delete-form-{{ $p->id }}" action="{{ route('pelanggan.destroy', $p->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        title: "Sukses!",
        text: "{{ session('success') }}",
        icon: "success",
        timer: 3000,
        showConfirmButton: false
    });
    
    function confirmDelete(id) {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endif

@endsection