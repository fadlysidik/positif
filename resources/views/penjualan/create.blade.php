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
                            <p class="mb-1 text-primary fw-bold flex-grow-1">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                            <button class="btn btn-primary btn-sm mt-auto w-100" onclick="tambahKeKeranjang({{ $item->id }}, '{{ $item->nama_barang }}', {{ $item->harga_jual }})">
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
                            <th>Harga</th>
                            <th>Subtotal</th>
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
                <select class="form-control" id="pelanggan_id">
                    <option value="">Tanpa Pelanggan</option>
                    @foreach($pelanggan as $p)
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

                <h5>Kembalian: Rp <span id="kembalian">0</span></h5>

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

function tambahKeKeranjang(id, nama, harga) {
    let index = keranjang.findIndex(item => item.id === id);
    if (index !== -1) {
        keranjang[index].qty++;
        keranjang[index].subtotal = keranjang[index].qty * harga;
    } else {
        keranjang.push({ id, nama, harga, qty: 1, subtotal: harga });
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
    let total = 0;

    if (keranjang.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Keranjang kosong</td></tr>`;
    } else {
        keranjang.forEach(item => {
            total += item.subtotal;
            tbody.innerHTML += `
                <tr>
                    <td>${item.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="kurangiDariKeranjang(${item.id})">-</button>
                        ${item.qty}
                        <button class="btn btn-sm btn-success" onclick="tambahKeKeranjang(${item.id}, '${item.nama}', ${item.harga})">+</button>
                    </td>
                    <td>Rp ${item.harga.toLocaleString()}</td>
                    <td>Rp ${item.subtotal.toLocaleString()}</td>
                </tr>
            `;
        });
    }

    totalElement.innerText = total.toLocaleString();
}

function hitungKembalian() {
    let total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    let jumlahTunai = parseFloat(document.getElementById("jumlah_tunai").value) || 0;
    let kembalian = jumlahTunai - total;
    document.getElementById("kembalian").innerText = kembalian.toLocaleString();
}

function clearKeranjang() {
    keranjang = [];
    renderKeranjang();
    document.getElementById("jumlah_tunai").value = "";
    document.getElementById("kembalian").innerText = "0";
}

function prosesPembayaran() {
    if (keranjang.length === 0) {
        alert("Keranjang masih kosong!");
        return;
    }

    let jumlahTunai = parseFloat(document.getElementById("jumlah_tunai").value);
    if (isNaN(jumlahTunai)) {
        alert("Masukkan jumlah tunai!");
        return;
    }

    let total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    if (jumlahTunai < total) {
        alert("Jumlah tunai kurang dari total pembayaran!");
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

    console.log("Mengirim data transaksi:", dataTransaksi); // Debugging sebelum mengirim

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
        alert("Pembayaran berhasil!");
        clearKeranjang();
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat melakukan pembayaran: " + error.message);
    });
}
</script>
@endsection
