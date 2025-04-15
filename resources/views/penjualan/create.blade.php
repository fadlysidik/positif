@extends('layout.kasir')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Bagian Menu -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between mb-3">
                <h4 class="mb-3">Menu</h4>
                <div class="input-group" style="width: 300px;">
                    <input type="text" id="barcode-scanner" class="form-control" placeholder="Scan barcode..." autofocus>
                    <button class="btn btn-outline-secondary" type="button" id="clear-scan">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                @foreach($barang as $item)
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm border-0 h-100 d-flex flex-column">
                        <div class="card-body text-center d-flex flex-column">
                            <img src="{{ asset('storage/'.$item->gambar) }}" class="img-fluid img-menu rounded mb-2" alt="{{ $item->nama_barang }}">
                            <p class="mb-1 fw-bold flex-grow-1">{{ $item->nama_barang }}</p>
                            <p class="mb-1 fw-bold flex-grow-1">Stok : {{ $item->stok }}</p>
                            <p class="mb-1 text-primary fw-bold flex-grow-1">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                            <button class="btn btn-primary btn-sm mt-auto w-100"
                                onclick="tambahKeKeranjang('{{ $item->id }}', '{{ $item->nama_barang }}', '{{ $item->harga_jual }}', '{{ $item->stok }}')">
                                <i class="bi bi-cart-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Bagian Keranjang -->
        <div class="col-md-4">
            <h4 class="mb-3">Cart</h4>
            <div class="border p-3 bg-light rounded">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="keranjang-body">
                        <tr>
                            <td colspan="4" class="text-center text-muted">Keranjang kosong</td>
                        </tr>
                    </tbody>
                </table>
                <h5>Total: Rp <span id="total">0</span></h5>

                <hr>

                <!-- Form Input Pembayaran -->
                <div class="mb-3">
                    <label for="pelanggan_id" class="form-label">Pelanggan</label>
                    <select id="pelanggan_id" class="form-control">
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach ($pelanggan as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kasir</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="jumlah_tunai" class="form-label">Jumlah Tunai</label>
                    <input type="number" class="form-control" id="jumlah_tunai"  placeholder="Masukkan jumlah uang bayar" oninput="hitungKembalian()">
                </div>

                <h5>Kembalian: Rp <span id="kembalian">0</span></h5>

                <div class="d-grid gap-2 mt-3">
                    <button class="btn btn-danger" onclick="clearKeranjang()">
                        <i class="bi bi-trash"></i> Clear
                    </button>
                    <button class="btn btn-success" onclick="prosesPembayaran()">
                        <i class="bi bi-credit-card"></i> Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .img-menu {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    #barcode-scanner {
        font-size: 16px;
        letter-spacing: 2px;
    }
    .table-sm td, .table-sm th {
        padding: 0.3rem;
    }
</style>

<script>
    let keranjang = [];

    // Fungsi untuk menambahkan item ke keranjang
    function tambahKeKeranjang(id, nama, harga, stok) {
        let index = keranjang.findIndex(item => item.id === id);

        if (index !== -1) {
            if (keranjang[index].qty >= stok) {
                Swal.fire({
                    icon: "error",
                    title: "Stok Tidak Cukup",
                    text: `Stok ${nama} hanya tersisa ${stok}!`,
                });
                return;
            }
            keranjang[index].qty++;
            keranjang[index].subtotal = keranjang[index].qty * harga;
        } else {
            if (stok <= 0) {
                Swal.fire({
                    icon: "error",
                    title: "Stok Habis",
                    text: `Stok ${nama} sudah habis!`,
                });
                return;
            }
            keranjang.push({
                id,
                nama,
                harga: parseFloat(harga),
                qty: 1,
                subtotal: parseFloat(harga),
                stok: parseInt(stok)
            });
        }
        renderKeranjang();
        updateTotal();
    }

    // Fungsi untuk mengurangi item dari keranjang
    function kurangiDariKeranjang(id) {
        let index = keranjang.findIndex(item => item.id === id);
        if (index !== -1) {
            keranjang[index].qty--;
            keranjang[index].subtotal = keranjang[index].qty * keranjang[index].harga;
            if (keranjang[index].qty <= 0) {
                keranjang.splice(index, 1);
            }
        }
        renderKeranjang();
        updateTotal();
    }

    // Fungsi untuk merender keranjang
    function renderKeranjang() {
        let tbody = document.getElementById("keranjang-body");
        tbody.innerHTML = "";

        if (keranjang.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Keranjang kosong</td></tr>`;
        } else {
            keranjang.forEach(item => {
                tbody.innerHTML += `
                <tr>
                    <td>${item.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="kurangiDariKeranjang('${item.id}')">-</button>
                        ${item.qty}
                        <button class="btn btn-sm btn-success" onclick="tambahKeKeranjang('${item.id}', '${item.nama}', ${item.harga}, ${item.stok})">+</button>
                    </td>
                    <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="hapusItem('${item.id}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });
        }
    }

    // Fungsi untuk menghapus item dari keranjang
    function hapusItem(id) {
        keranjang = keranjang.filter(item => item.id !== id);
        renderKeranjang();
        updateTotal();
    }

    // Fungsi untuk menghitung total
    function updateTotal() {
        let total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
        document.getElementById("total").innerText = total.toLocaleString('id-ID');
        hitungKembalian();
    }

    // Fungsi untuk menghitung kembalian
    function hitungKembalian() {
        let total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
        let jumlahTunai = parseFloat(document.getElementById("jumlah_tunai").value) || 0;
        let kembalian = jumlahTunai - total;

        document.getElementById("kembalian").innerText = 
            kembalian >= 0 ? kembalian.toLocaleString('id-ID') : "0";
    }

    // Fungsi untuk mengosongkan keranjang
    function clearKeranjang() {
        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus semua item di keranjang?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                keranjang = [];
                renderKeranjang();
                document.getElementById("jumlah_tunai").value = "";
                document.getElementById("kembalian").innerText = "0";
                updateTotal();
                Swal.fire({
                    icon: "success",
                    title: "Keranjang Dikosongkan",
                    text: "Semua item telah dihapus dari keranjang.",
                });
            }
        });
    }

    // Fungsi untuk proses pembayaran
    function prosesPembayaran() {
        if (keranjang.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "Keranjang Kosong",
                text: "Tambahkan barang terlebih dahulu!",
            });
            return;
        }

        let jumlahTunai = parseFloat(document.getElementById("jumlah_tunai").value) || 0;
        let total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);

        if (jumlahTunai < total) {
            Swal.fire({
                icon: "error",
                title: "Pembayaran Gagal",
                text: "Jumlah tunai kurang dari total pembayaran!",
            });
            return;
        }

        let dataTransaksi = {
            pelanggan_id: document.getElementById("pelanggan_id").value || null,
            barang_id: keranjang.map(item => item.id),
            jumlah: keranjang.map(item => item.qty),
            harga_jual: keranjang.map(item => item.harga),
            jumlah_tunai: jumlahTunai,
            kembalian: jumlahTunai - total,
        };

        console.log(dataTransaksi)

        fetch("{{ route('penjualan.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify(dataTransaksi)
        })
        .then(response => response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.message || "Gagal memproses pembayaran");
            }
            return data;
        }))
        .then(data => {
            Swal.fire({
                icon: "success",
                title: "Pembayaran Berhasil",
                text: "Transaksi berhasil disimpan!",
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = data.redirect || "{{ route('penjualan.index') }}";
            });
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                icon: "error",
                title: "Terjadi Kesalahan",
                text: error.message,
            });
        });
    }

    // Fungsi untuk handle scan barcode
    document.addEventListener('DOMContentLoaded', function() {
        const barcodeInput = document.getElementById('barcode-scanner');
        const clearScanBtn = document.getElementById('clear-scan');
        let scanTimeout;

        // Fokus otomatis ke input barcode
        barcodeInput.focus();

        barcodeInput.addEventListener('input', function(e) {
            clearTimeout(scanTimeout);
            
            // Tunggu 500ms setelah input terakhir untuk memastikan scan selesai
            scanTimeout = setTimeout(() => {
                if (barcodeInput.value.length > 0) {
                    handleBarcodeScan(barcodeInput.value);
                    barcodeInput.value = '';
                }
            }, 500);
        });

        clearScanBtn.addEventListener('click', function() {
            barcodeInput.value = '';
            barcodeInput.focus();
        });

        function handleBarcodeScan(barcode) {
            // Gunakan route biasa untuk mencari barang
            fetch("{{ route('penjualan.cariBarang') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ barcode: barcode })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Barang tidak ditemukan');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.barang) {
                    const barang = data.barang;
                    tambahKeKeranjang(
                        barang.id, 
                        barang.nama_barang, 
                        barang.harga_jual, 
                        barang.stok
                    );
                    
                    // Feedback suara
                    playBeepSound();
                } else {
                    showBarcodeError('Kode barcode tidak valid');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showBarcodeError(error.message);
            });
        }

        // function showBarcodeError(message) {
        //     Swal.fire({
        //         icon: 'error',
        //         title: 'Scan Gagal',
        //         text: message,
        //         timer: 1500,
        //         showConfirmButton: false
        //     });
        //     playErrorSound();
        // }
    });
</script>
@endsection