@extends('layout.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Pengajuan Barang</h2>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalPengajuan">
        Tambah Pengajuan
    </button>
    <div class="mb-3">
        <a href="{{ route('pengajuan.export.excel') }}" class="btn btn-success">Export Excel</a>
        <a href="{{ route('pengajuan.export.pdf') }}" class="btn btn-danger">Export PDF</a>
    </div>
    

    <table class="table table-bordered" id="pengajuanTable">
        <thead>
            <tr>
                <th>Kode Pengajuan</th>
                <th>Pelanggan</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengajuan as $item)
            <tr>
                <td>{{ $item->kode_pengajuan }}</td>
                <td>{{ $item->pelanggan->nama }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ $item->deskripsi }}</td>
                <td>
                    <input type="checkbox" class="toggle-status" data-id="{{ $item->id }}" 
                        {{ $item->status ? 'checked' : '' }}>
                </td>
                <td>
                    <button class="btn btn-sm btn-warning btn-edit" 
                        data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#modalPengajuan">Edit</button>
                    <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->id }}">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Form Pengajuan -->
<div class="modal fade" id="modalPengajuan" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Form Pengajuan Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPengajuan">
                    @csrf
                    <input type="hidden" id="pengajuan_id" name="pengajuan_id"> <!-- ID untuk Edit -->
                
                    <div class="mb-3">
                        <label for="pelanggan_id" class="form-label">Pelanggan</label>
                        <select id="pelanggan_id" name="pelanggan_id" class="form-control">
                            @foreach ($pelanggan as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" id="nama_barang" name="nama_barang" class="form-control" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" id="jumlah" name="jumlah" class="form-control" min="1" required>
                    </div>
                
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
                <div id="notif" class="mt-3"></div>                                
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '.btn-edit', function () {
        let id = $(this).data('id');

        $.get("/pengajuan_barang/" + id, function (data) {
            $('#pengajuan_id').val(data.id);
            $('#pelanggan_id').val(data.pelanggan_id);
            $('#nama_barang').val(data.nama_barang);
            $('#jumlah').val(data.jumlah);
            $('#deskripsi').val(data.deskripsi);
            $('#modalPengajuan').modal('show');
        });
    });

    // SUBMIT FORM (TAMBAH / EDIT)
    $('#formPengajuan').submit(function (e) {
        e.preventDefault();

        let id = $('#pengajuan_id').val();
        let url = id ? "/pengajuan_barang/" + id : "/pengajuan_barang";
        let method = id ? "PUT" : "POST";

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function (response) {
                alert(response.success);
                $('#modalPengajuan').modal('hide');
                refreshTable();
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert("Terjadi kesalahan!");
            }
        });
    });

    // TOGGLE STATUS PENGAJUAN (Switch On/Off)
    $(document).on('change', '.toggle-status', function () {
        var id = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0; // Cek apakah checkbox aktif atau tidak

        $.ajax({
            url: "/pengajuan_barang/status/" + id, // Endpoint ubah status
            type: "POST",
            data: {
                status: status
            },
            success: function (response) {
                alert(response.success);
                refreshTable(); // Perbarui tabel setelah status berubah
            },
            error: function () {
                alert("Terjadi kesalahan saat mengubah status!");
            }
        });
    });

    // HAPUS DATA PENGAJUAN BARANG
    $(document).on('click', '.btn-delete', function () {
        let id = $(this).data('id');

        if (confirm('Apakah Anda yakin ingin menghapus pengajuan ini?')) {
            $.ajax({
                url: "/pengajuan_barang/" + id,
                type: "DELETE",
                success: function (response) {
                    alert(response.success); // Notifikasi sukses
                    refreshTable(); // Hapus baris tanpa reload halaman
                },
                error: function (xhr) {
                    console.log(xhr.responseText); // Debugging jika ada error
                    alert("Terjadi kesalahan saat menghapus pengajuan!");
                }
            });
        }
    });

    // REFRESH TABEL PENGAJUAN (Tanpa Reload Halaman)
    function refreshTable() {
        $.ajax({
            url: "/pengajuan_barang",
            type: "GET",
            success: function (response) {
                var tbody = "";
                $.each(response.pengajuan, function (index, item) {
                    tbody += "<tr>";
                    tbody += "<td>" + item.kode_pengajuan + "</td>";
                    tbody += "<td>" + item.pelanggan.nama + "</td>";
                    tbody += "<td>" + item.barang.nama_barang + "</td>";
                    tbody += "<td>" + item.jumlah + "</td>";
                    tbody += "<td>" + item.deskripsi + "</td>";
                    tbody += "<td><input type='checkbox' class='toggle-status' data-id='" + item.id + "'" + (item.status ? "checked" : "") + "></td>";
                    tbody += "<td><button class='btn btn-sm btn-warning btn-edit' data-id='" + item.id + "' data-bs-toggle='modal' data-bs-target='#modalPengajuan'>Edit</button>";
                    tbody += "<button class='btn btn-sm btn-danger btn-delete' data-id='" + item.id + "'>Hapus</button></td>";
                    tbody += "</tr>";
                });
                $('#pengajuanTable tbody').html(tbody);
            }
        });
    }
});

</script>
@endsection
