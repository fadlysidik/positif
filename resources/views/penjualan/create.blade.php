@extends('layout.kasir')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Bagian Menu -->
        <div class="col-md-8">
            <h4 class="mb-3">Menu</h4>
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
                                <i class="bi bi-cart-plus">Tambah</i>
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
                        </tr>
                    </thead>
                    <tbody id="keranjang-body">
                        <tr>
                            <td colspan="4" class="text-center text-muted">Keranjang kosong</td>
                        </tr>
                    </tbody>
                </table>
                <h5>Total: Rp <span id="total"></span></h5>

                <hr>

                <!-- Form Input Pembayaran -->
                <select id="pelanggan_id" class="form-control">
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($pelanggan as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>


                <div class="mb-2">
                    <label class="form-label">Kasir</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                </div>

                <div class="mb-2">
                    <label for="jumlah_tunai" class="form-label">Jumlah Tunai</label>
                    <input type="number" class="form-control" id="jumlah_tunai" oninput="hitungKembalian()">
                </div>

                <h5>Kembalian: Rp <span id="kembalian"></span></h5>

                <button class="btn btn-danger btn-block" onclick="clearKeranjang()">Clear</button>
                <button class="btn btn-primary btn-block" onclick="prosesPembayaran()">Bayar</button>
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
</style>

<script>
    let keranjang = [];

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
                harga,
                qty: 1,
                subtotal: harga,
                stok
            });
        }
        renderKeranjang();
    }

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
    }

    function renderKeranjang() {
    let tbody = document.getElementById("keranjang-body");
    let totalElement = document.getElementById("total");
    tbody.innerHTML = "";
    let total = 0; // Perbaikan dari string ke angka

    if (keranjang.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Keranjang kosong</td></tr>`;
    } else {
        keranjang.forEach(item => {
            let subtotal = item.qty * item.harga; 
            total += subtotal;  // Sekarang total bertambah dengan benar

            tbody.innerHTML += `
            <tr>
                <td>${item.nama}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="kurangiDariKeranjang('${item.id}')">-</button>
                    ${item.qty}
                    <button class="btn btn-sm btn-success" onclick="tambahKeKeranjang('${item.id}', '${item.nama}', ${item.harga}, ${item.stok})">+</button>
                </td>
                <td>Rp ${subtotal.toLocaleString()}</td>
            </tr>`;
        });
    }

    totalElement.innerText = `${total.toLocaleString()}`; // Format angka rupiah
    hitungKembalian();
}



function hitungKembalian() {
    let total = keranjang.reduce((sum, item) => sum + (item.qty * item.harga), 0);
    let jumlahTunai = parseFloat(document.getElementById("jumlah_tunai").value) || 0;

    let kembalian = jumlahTunai - total;

    // Jika hasilnya NaN atau tidak valid, tampilkan 0
    document.getElementById("kembalian").innerText = kembalian >= 0 ? kembalian.toLocaleString() : "0";
}


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
                Swal.fire({
                    icon: "success",
                    title: "Keranjang Dikosongkan",
                    text: "Semua item telah dihapus dari keranjang.",
                });
            }
        });
    }


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
    let total = keranjang.reduce((sum, item) => sum * item.subtotal, 0);

    console.log("Total: ", total);
    console.log("Jumlah Tunai: ", jumlahTunai);

    if (jumlahTunai < total) {
        Swal.fire({
            icon: "error",
            title: "Pembayaran Gagal",
            text: "Jumlah tunai kurang dari total pembayaran!",
        });
        return;
    }

    let dataTransaksi = {
        pelanggan_id: document.getElementById("pelanggan_id")?.value || null,
        barang_id: keranjang.map(item => item.id),
        jumlah: keranjang.map(item => item.qty),
        harga_jual: keranjang.map(item => item.harga),
        jumlah_tunai: jumlahTunai,
        kembalian: jumlahTunai - total,
    };

    fetch(document.querySelector('meta[name="route-penjualan"]').getAttribute('content'), {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                window.location.href = data.redirect; // Redirect ke halaman tujuan
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
</script>
@endsection