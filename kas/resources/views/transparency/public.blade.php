<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; color: #333; }
        .header { background-color: #007bff; color: white; padding: 20px; border-radius: 5px; margin-bottom: 30px; }
        .summary-card { background: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #007bff; }
        .table { background: white; }
        .badge-incoming { background-color: #28a745; }
        .badge-outgoing { background-color: #dc3545; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="header">
        <h1 class="mb-2">{{ $report->title }}</h1>
        <p class="mb-0">Periode: {{ $report->start_date->format('d/m/Y') }} - {{ $report->end_date->format('d/m/Y') }}</p>
        @if($report->description)
        <p class="mb-0 mt-2"><em>{{ $report->description }}</em></p>
        @endif
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="summary-card border-success">
                <h6 class="text-muted">Total Pemasukan</h6>
                <h3 class="text-success">Rp{{ number_format($totalIncome, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card border-danger">
                <h6 class="text-muted">Total Pengeluaran</h6>
                <h3 class="text-danger">Rp{{ number_format($totalExpense, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card border-primary">
                <h6 class="text-muted">Saldo</h6>
                <h3 class="text-primary">Rp{{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <h4 class="mb-3">Rincian Tagihan</h4>
    <div class="table-responsive mb-4">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Siswa</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr>
                    <td>{{ $bill->created_at->format('d/m/Y') }}</td>
                    <td>{{ $bill->customer->name }}</td>
                    <td>{{ $bill->bill_type }}</td>
                    <td>Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                    <td>
                        @if($bill->isPaid())
                            <span class="badge badge-success">Terbayar</span>
                        @else
                            <span class="badge badge-warning">Belum Bayar</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">Tidak ada data tagihan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h4 class="mb-3">Rincian Pengeluaran</h4>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td>{{ $expense->category->name }}</td>
                    <td>{{ Str::limit($expense->description, 30) }}</td>
                    <td>Rp{{ number_format($expense->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">Tidak ada data pengeluaran</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <hr>
    <p class="text-muted text-center small">Laporan ini bersifat transparan dan dapat diakses oleh seluruh anggota koperasi.</p>
</div>
</body>
</html>
