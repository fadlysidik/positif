@extends('layout.kasir')

@section('content')
<div class="container">
    <h2>Transaksi Penjualan</h2>
    <div class="row">
        <div class="col-md-6">
            <h4>Pilih Pelanggan</h4>
            <select id="pelanggan_id" class="form-control">
                <option value="">-- Pilih Pelanggan --</option>
                @foreach ($pelanggan as $p)
                <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>

            <h4 class="mt-4">Pilih Barang</h4>
            <ul class="list-group">
                @foreach ($barang as $b)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $b->nama }} - Rp {{ number_format($b->harga_jual, 0, ',', '.') }}
                    <button class="btn btn-sm btn-primary" onclick="tambahKeKeranjang({{ $b->id }}, '{{ $b->nama }}', {{ $b->harga_jual }})">
                        Tambah
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-6">
            <h4>Keranjang</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Barang</th>
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
            <button class="btn btn-success btn-lg btn-block" onclick="prosesPembayaran()">Bayar</button>
        </div>
    </div>
</div>

<script>
    let keranjang = [];

    function tambahKeKeranjang(id, nama, harga) {
        let index = keranjang.findIndex(item => item.id === id);
        if (index !== -1) {
            keranjang[index].qty++;
        } else {
            keranjang.push({
                id,
                nama,
                harga,
                qty: 1
            });
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
                let subtotal = item.qty * item.harga;
                total += subtotal;
                tbody.innerHTML += `
                    <tr>
                        <td>${item.nama}</td>
                        <td>${item.qty}</td>
                        <td>Rp ${subtotal.toLocaleString()}</td>
                    </tr>
                `;
            });
        }

        totalElement.innerText = total.toLocaleString();
    }

    function prosesPembayaran() {
        let pelangganId = document.getElementById("pelanggan_id").value;
        if (keranjang.length === 0) {
            alert("Keranjang masih kosong!");
            return;
        }

        let data = {
            pelanggan_id: pelangganId,
            barang_id: keranjang.map(item => item.id),
            jumlah: keranjang.map(item => item.qty),
            harga_jual: keranjang.map(item => item.harga),
            _token: "{{ csrf_token() }}"
        };

        fetch("{{ route('penjualan.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                alert("Pembayaran berhasil!");
                keranjang = [];
                renderKeranjang();
            })
            .catch(error => console.error("Error:", error));
    }
</script>
@endsection