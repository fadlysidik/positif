@extends('layout.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Daftar Penjualan -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Penjualan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No Faktur</th>
                                    <th>Tanggal</th>
                                    <th>Total Bayar</th>
                                    <th>Pelanggan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($penjualan as $item)
                                <tr>
                                    <td>{{ $item->no_faktur }}</td>
                                    <td>{{ $item->tgl_faktur }}</td>
                                    <td>{{ number_format($item->total_bayar, 2) }}</td>
                                    <td>{{ $item->pelanggan->nama }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" onclick="editPenjualan({{ $item }})">Edit</button>
                                        <form action="{{ route('penjualan.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Tambah/Edit Penjualan -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 id="form-title" class="m-0 font-weight-bold text-primary">Tambah Penjualan</h6>
                </div>
                <div class="card-body">
                    <form id="penjualanForm" action="{{ route('penjualan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" id="penjualan_id" name="id">
                        <div class="form-group">
                            <label>No Faktur</label>
                            <input type="text" class="form-control" id="no_faktur" name="no_faktur" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" class="form-control" id="tgl_faktur" name="tgl_faktur" required>
                        </div>
                        <div class="form-group">
                            <label>Total Bayar</label>
                            <input type="number" class="form-control" id="total_bayar" name="total_bayar" required>
                        </div>
                        <div class="form-group">
                            <label>Pelanggan</label>
                            <select class="form-control" id="pelanggan_id" name="pelanggan_id" required>
                                @foreach($pelanggan as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function editPenjualan(data) {
        document.getElementById('penjualan_id').value = data.id;
        document.getElementById('no_faktur').value = data.no_faktur;
        document.getElementById('tgl_faktur').value = data.tgl_faktur;
        document.getElementById('total_bayar').value = data.total_bayar;
        document.getElementById('pelanggan_id').value = data.pelanggan_id;
        document.getElementById('form-title').innerText = 'Edit Penjualan';
        document.getElementById('penjualanForm').action = `/penjualan/${data.id}`;
    }
</script>
@endsection