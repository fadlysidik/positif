@extends('layout.kasir')

@section('content')
<div class="container mt-4">
    <a href="{{ route('dashboard.kasir') }}" class="btn btn-secondary">Kembali</a>
    <h4 class="mb-3">Daftar Penjualan</h4>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Total Bayar</th>
                <th>Nama Pelanggan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan as $item)
            <tr>
                <td>{{ $item->no_faktur }}</td>
                <td>{{ date('d-m-Y H:i', strtotime($item->tgl_faktur)) }}</td>
                <td>Rp{{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                <td>{{ $item->pelanggan ? $item->pelanggan->nama : 'Tidak Diketahui' }}</td>
                <td>
                    <button class="btn btn-success btn-sm btn-struk"
                        data-id="{{ $item->id }}"
                        data-faktur="{{ $item->no_faktur }}"
                        data-tanggal="{{ date('d-m-Y H:i', strtotime($item->tgl_faktur)) }}"
                        data-total="Rp{{ number_format($item->total_bayar, 0, ',', '.') }}"
                        data-pelanggan="{{ $item->pelanggan ? $item->pelanggan->nama : 'Tidak Diketahui' }}">
                        Struk
                    </button>
                    <a href="{{ route('penjualan.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $penjualan->links() }}
    </div>
</div>

<!-- Tambahkan SweetAlert & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".btn-struk").forEach(button => {
            button.addEventListener("click", function () {
                let id = this.dataset.id;
                let faktur = this.dataset.faktur;
                let tanggal = this.dataset.tanggal;
                let total = this.dataset.total;
                let pelanggan = this.dataset.pelanggan;

                // Ambil data barang menggunakan AJAX
                $.ajax({
                    url: `/penjualan/${id}/detail`, // Pastikan rute ini ada di Laravel
                    type: "GET",
                    success: function(response) {
                        let barangList = "<table class='table table-bordered'><tr><th>Barang</th><th>Qty</th><th>Subtotal</th></tr>";

                        response.forEach(item => {
                            barangList += `<tr>
                                <td>${item.nama_barang}</td>
                                <td>${item.jumlah}</td>
                                <td>Rp ${item.sub_total.toLocaleString('id-ID')}</td>
                            </tr>`;
                        });

                        barangList += "</table>";

                        let strukHtml = `
                            <div id="struk-container">
                                <h3 class="text-center">Struk Penjualan</h3>
                                <p><strong>No Faktur:</strong> ${faktur}</p>
                                <p><strong>Tanggal:</strong> ${tanggal}</p>
                                <p><strong>Pelanggan:</strong> ${pelanggan}</p>
                                ${barangList}
                                <h4><strong>Total:</strong> ${total}</h4>
                            </div>
                        `;

                        Swal.fire({
                            title: "Struk Penjualan",
                            html: strukHtml,
                            icon: "info",
                            showCancelButton: true,
                            confirmButtonText: "Cetak Struk",
                            cancelButtonText: "Tutup"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                cetakStruk(strukHtml);
                            }
                        });
                    },
                    error: function() {
                        Swal.fire("Gagal!", "Tidak dapat mengambil data barang.", "error");
                    }
                });
            });
        });
    });

    function cetakStruk(content) {
    let printWindow = window.open('', '', 'width=400,height=600');
    printWindow.document.write(`
        <html>
        <head>
            <title>Struk Penjualan</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; }
                h3 { margin-bottom: 10px; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                table, th, td { border: 1px solid black; padding: 8px; text-align: left; }
            </style>
        </head>
        <body>
            ${content}
            <script>
                window.onload = function() {
                    window.print();
                    window.onafterprint = function() {
                        window.close();
                    };
                };
            <\/script> <!-- Escape tanda "<\/script>" untuk menghindari error -->
        </body>
        </html>
    `);
    printWindow.document.close();
}

</script>

@endsection
