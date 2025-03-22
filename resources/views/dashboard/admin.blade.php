@extends('layout.admin')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Dashboard Admin</h4>

    <!-- Ringkasan Data -->
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header text-dark">Total Barang</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalBarang }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header text-dark">Total Pembelian</div>
                <div class="card-body">
                    <h5 class="card-title">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header text-dark">Total Penjualan</div>
                <div class="card-body">
                    <h5 class="card-title">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header text-dark">Total Pelanggan</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalPelanggan }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Penjualan -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Grafik Penjualan & Keuntungan</h5>
        </div>
        <div class="card-body">
            <canvas id="penjualanChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('penjualanChart').getContext('2d');

        const penjualanChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($bulanPenjualan) !!},
                datasets: [
                    {
                        label: 'Penjualan',
                        data: {!! json_encode($jumlahPenjualan) !!},
                        borderColor: '#29B6F6',
                        borderWidth: 2,
                        pointRadius: 3,
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'Keuntungan',
                        data: {!! json_encode($jumlahKeuntungan) !!},
                        borderColor: "#4CAF50",
                        borderWidth: 2,
                        pointRadius: 3,
                        tension: 0.3,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let value = tooltipItem.raw;
                                return tooltipItem.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
