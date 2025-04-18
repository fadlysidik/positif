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
                    <h4 class="fw-bold">Rp {{ number_format($labaBersih, 0, ',', '.') }}</h4>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Grafik</h6>
        </div>
        <div class="card-body">
            <div class="chart-area">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';

        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        // Area Chart Example
        var ctx = document.getElementById("myAreaChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels), // Menggunakan data labels dari controller
                datasets: [
                    {
                        label: "Pendapatan",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: @json($totalsPenjualan), // Menggunakan data pendapatan (penjualan) dari controller
                    },
                    {
                        label: "Pengeluaran",
                        lineTension: 0.3,
                        backgroundColor: "rgba(255, 99, 132, 0.05)",
                        borderColor: "rgba(255, 99, 132, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(255, 99, 132, 1)",
                        pointBorderColor: "rgba(255, 99, 132, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(255, 99, 132, 1)",
                        pointHoverBorderColor: "rgba(255, 99, 132, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: @json($totalsPembelian), // Menggunakan data pengeluaran (pembelian) dari controller
                    }
                ],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return 'Rp ' + number_format(value); // Menampilkan dengan format Rupiah
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: true
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': Rp ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</div>

@endsection
