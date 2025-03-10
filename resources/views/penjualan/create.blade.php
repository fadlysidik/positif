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
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="keranjang-body">
                        <tr>
                            <td colspan="3" class="text-center text-muted">Keranjang kosong</td>
                        </tr>
                    </tbody>
                </table>
                <h5>Total: Rp <span id="total">0</span></h5>
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

    /* Pastikan semua kartu memiliki tinggi yang sama */
    .card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    /* Pastikan tombol "Tambah" sejajar di semua kartu */
    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
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
            keranjang.push({
                id,
                nama,
                harga,
                qty: 1,
                subtotal: harga
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
        let total = 0;

        if (keranjang.length === 0) {
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted">Keranjang kosong</td></tr>`;
        } else {
            keranjang.forEach(item => {
                total += item.subtotal;
                tbody.innerHTML += `
                    <tr>
                        <td>${item.nama}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="kurangiDariKeranjang(${item.id})">
                                <i class="bi bi-dash"></i>
                            </button>
                            ${item.qty}
                            <button class="btn btn-sm btn-success" onclick="tambahKeKeranjang(${item.id}, '${item.nama}', ${item.harga})">
                                <i class="bi bi-plus"></i>
                            </button>
                        </td>
                        <td>Rp ${item.subtotal.toLocaleString()}</td>
                    </tr>
                `;
            });
        }

        totalElement.innerText = total.toLocaleString();
    }

    function prosesPembayaran() {
        if (keranjang.length === 0) {
            alert("Keranjang masih kosong!");
            return;
        }

        let dataTransaksi = {
            barang_id: keranjang.map(item => item.id),
            jumlah: keranjang.map(item => item.qty),
            harga_jual: keranjang.map(item => item.harga),
        };

        fetch("{{ route('penjualan.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(dataTransaksi)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Gagal memproses pembayaran");
                }
                return response.json();
            })
            .then(data => {
                alert("Pembayaran berhasil!");
                keranjang = []; // Kosongkan keranjang setelah transaksi berhasil
                renderKeranjang(); // Perbarui tampilan keranjang
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Terjadi kesalahan saat melakukan pembayaran.");
            });
    }
</script>
@endsection