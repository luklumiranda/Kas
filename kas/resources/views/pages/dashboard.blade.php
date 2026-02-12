@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Welcome Card -->
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2">
                                <img src="{{ asset('swamitra.jpeg') }}" width="100%" class="p-4" alt="{{ config('app.name') }}" srcset="">
                            </div>
                            <div class="col-10">
                                <h2 class="my-3">Hai, {{ Auth::user()->username }}</h2>
                                <p class="card-text h4 font-weight-light">
                                    Selamat datang di halaman dashboard {{ config('app.name') }}.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Balance Card -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="card-title text-uppercase mb-0">Total Saldo</h6>
                                <span class="h2 font-weight-bold">Rp {{ number_format($totalBalance, 0, ',', '.') }}</span>
                                <p class="mt-3 mb-0 text-white text-sm">
                                    <span class="text-success mr-2">
                                        <i class="fas fa-arrow-up"></i> Jumlah saldo keseluruhan
                                    </span>
                                </p>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-white text-primary rounded-circle shadow">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Ringkasan</h6>
                        <div class="row">
                            <div class="col-6 border-right">
                                <p class="text-muted mb-1">Jumlah Pelanggan</p>
                                <h5 class="font-weight-bold">{{ \App\Models\Customer::count() }}</h5>
                            </div>
                            <div class="col-6">
                                <p class="text-muted mb-1">Total Pinjaman</p>
                                <h5 class="font-weight-bold">{{ \App\Models\Loan::count() }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Balance Chart -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">Grafik Saldo Bulanan</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-4" style="height: 400px;">
                            <canvas id="balanceChart"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-6 col-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-success">
                                        <i class="fas fa-caret-up"></i> {{ isset($balances[count($balances)-1]) && isset($balances[count($balances)-2]) && $balances[count($balances)-2] > 0 ? round((($balances[count($balances)-1] - $balances[count($balances)-2]) / $balances[count($balances)-2]) * 100, 1) : 0 }}%
                                    </span>
                                    <h5 class="description-header">Bulan Ini</h5>
                                    <span class="description-text">Dibandingkan bulan lalu</span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-6">
                                <div class="description-block">
                                    <span class="description-percentage text-info">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                    <h5 class="description-header">Total Periode</h5>
                                    <span class="description-text">12 bulan terakhir</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('balanceChart').getContext('2d');
            const balanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [{
                        label: 'Saldo (Rp)',
                        data: {!! json_encode($balances) !!},
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#007bff',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#0056b3'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            padding: 12,
                            borderRadius: 4,
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                label: function(context) {
                                    let value = context.parsed.y;
                                    return 'Saldo: Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                },
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
