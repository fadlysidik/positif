<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengajuan Barang</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Data Pengajuan Barang</h2>
    <table>
        <thead>
            <tr>
                <th>Kode Pengajuan</th>
                <th>Tanggal Pengajuan</th>
                <th>Pelanggan</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Deskripsi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengajuan as $item)
            <tr>
                <td>{{ $item->kode_pengajuan }}</td>
                <td>{{ $item->tgl_pengajuan }}</td>
                <td>{{ $item->pelanggan->nama }}</td>
                <td>{{ $item->barang->nama_barang }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ $item->deskripsi }}</td>
                <td>{{ $item->status ? 'Terpenuhi' : 'Belum Terpenuhi' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
