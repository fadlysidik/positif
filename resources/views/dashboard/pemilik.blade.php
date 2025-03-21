@extends('layout.pemilik')
@section('content')

<div class="container mt-4">
    <h2 class="fw-bold">Konnichiwa, {{ Auth::user()->name }}! 👋</h2>
    <h4>Selamat datang di Dashboard Pemilik</h4>

    <div class="row g-3 mt-3">
        {{-- Total Pendapatan --}}
        <div class="col-md-3">
            <div class="card text-white shadow-sm" style="background-color: #28a745;">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <h5 class="card-title">Total Pendapatan</h5>
                    <h4 class="fw-bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="col-md-3">
            <div class="card text-white shadow-sm" style="background-color: #dc3545;">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <h5 class="card-title">Total Pengeluaran</h5>
                    <h4 class="fw-bold">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        {{-- Laba Bersih --}}
        <div class="col-md-3">
            <div class="card text-white shadow-sm" style="background-color: #ffc107;">
                <div class="card-body text-center">
                    <i class="fas fa-coins fa-2x mb-2"></i>
                    <h5 class="card-title">Laba Bersih</h5>
                    <h4 class="fw-bold">Rp {{ number_format($totalPenjualan - $totalPembelian, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        {{-- Total Pelanggan --}}
        <div class="col-md-3">
            <div class="card text-white shadow-sm" style="background-color: #17a2b8;">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h5 class="card-title">Total Pelanggan</h5>
                    <h4 class="fw-bold">{{ $totalPelanggan }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Penjualan Bulanan --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Grafik Penjualan Bulanan</h5>
                </div>
                <div class="card-body">
                    <canvas id="penjualanChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('penjualanChart').getContext('2d');
    var penjualanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($bulanPenjualan),
            datasets: [{
                label: 'Total Penjualan',
                data: @json($jumlahPenjualan),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection
