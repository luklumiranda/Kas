@extends('layouts.app')

@section('title', 'Dashboard - Ringkasan Keuangan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 font-weight-bold text-dark">
                <i class="fas fa-chart-line mr-2 text-primary"></i>Dashboard Keuangan
            </h1>
            <p class="text-muted">Ringkasan kondisi keuangan koperasi</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('bill.index') }}" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-plus mr-1"></i> Generate Tagihan
            </a>
            <a href="{{ route('transparency.create') }}" class="btn btn-info btn-sm">
                <i class="fas fa-file-alt mr-1"></i> Buat Laporan
            </a>
        </div>
    </div>

    <!-- Widget Saldo Real-time -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="text-primary text-uppercase mb-1 small font-weight-bold">Saldo Total</div>
                    <div class="h3 mb-0 font-weight-bold">Rp{{ number_format($balance, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="text-success text-uppercase mb-1 small font-weight-bold">Total Pemasukan</div>
                    <div class="h3 mb-0 font-weight-bold">Rp{{ number_format($totalIncome, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="text-danger text-uppercase mb-1 small font-weight-bold">Total Pengeluaran</div>
                    <div class="h3 mb-0 font-weight-bold">Rp{{ number_format($totalExpense, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="text-warning text-uppercase mb-1 small font-weight-bold">Total Tunggakan</div>
                    <div class="h3 mb-0 font-weight-bold">Rp{{ number_format($totalDebt, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulan Ini -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Pemasukan Bulan Ini</h6>
                </div>
                <div class="card-body">
                    <div class="h4 mb-0 font-weight-bold">Rp{{ number_format($monthlyIncome, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h6 class="m-0">Pengeluaran Bulan Ini</h6>
                </div>
                <div class="card-body">
                    <div class="h4 mb-0 font-weight-bold">Rp{{ number_format($monthlyExpense, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="m-0">Tunggakan Overdue</h6>
                </div>
                <div class="card-body">
                    <div class="h4 mb-0 font-weight-bold">
                        {{ $overdueCount }} <small class="text-danger">Rp{{ number_format($overdueDebt, 0, ',', '.') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Arus Kas -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Grafik Arus Kas (12 Bulan Terakhir)</h6>
                </div>
                <div class="card-body" style="height: 400px;">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Debtors & Expense by Category -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-exclamation-circle text-danger mr-2"></i>Top Siswa Berpenghasilan</h6>
                </div>
                <div class="card-body">
                    @if($topDebtors->count())
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="text-right">Tunggakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topDebtors as $debtor)
                            <tr>
                                <td>{{ $debtor->name }}</td>
                                <td class="text-right text-danger font-weight-bold">
                                    Rp{{ number_format($debtor->bills_sum_amount ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted text-center">Tidak ada tunggakan</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-pie-chart text-info mr-2"></i>Pengeluaran per Kategori</h6>
                </div>
                <div class="card-body" style="height: 300px;">
                    <canvas id="expenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Tagihan Terbaru</h6>
                </div>
                <div class="card-body">
                    @if($recentBills->count())
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBills as $bill)
                            <tr>
                                <td>{{ $bill->customer->name }}</td>
                                <td>Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($bill->isPaid())
                                        <span class="badge badge-success">Terbayar</span>
                                    @elseif($bill->isOverdue())
                                        <span class="badge badge-danger">Overdue</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted text-center">Belum ada tagihan</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold">Pengeluaran Terbaru</h6>
                </div>
                <div class="card-body">
                    @if($recentExpenses->count())
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentExpenses as $expense)
                            <tr>
                                <td><span class="badge badge-secondary">{{ $expense->category->name }}</span></td>
                                <td>{{ Str::limit($expense->description, 20) }}</td>
                                <td class="text-right">Rp{{ number_format($expense->amount, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-muted text-center">Belum ada pengeluaran</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Cash Flow Chart
    const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
    const cashFlowChart = new Chart(cashFlowCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($cashFlowData->pluck('month')) !!},
            datasets: [
                {
                    label: 'Pemasukan',
                    data: {!! json_encode($cashFlowData->pluck('income')) !!},
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Pengeluaran',
                    data: {!! json_encode($cashFlowData->pluck('expense')) !!},
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Expense by Category Chart
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    const expenseChart = new Chart(expenseCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($expenseByCategory->keys()) !!},
            datasets: [{
                data: {!! json_encode($expenseByCategory->values()) !!},
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush

@endsection
