<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transparansi Keuangan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h1, h2 { color: #0066cc; }
        .header { border-bottom: 2px solid #0066cc; padding-bottom: 10px; margin-bottom: 20px; }
        .summary { background-color: #f0f0f0; padding: 15px; margin: 15px 0; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #0066cc; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .amount { font-weight: bold; }
        .total-row { background-color: #e0e0e0; font-weight: bold; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Transparansi Keuangan</h1>
        <p>Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary">
        <h3>Ringkasan Keuangan</h3>
        <table>
            <tr>
                <td style="width: 50%;">Total Pemasukan</td>
                <td class="text-right">Rp{{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="text-right">Rp{{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Saldo</td>
                <td class="text-right">Rp{{ number_format($balance, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <h2>Rincian Tagihan</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Siswa</th>
                <th>Tipe Tagihan</th>
                <th class="text-right">Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bills as $bill)
            <tr>
                <td>{{ $bill->created_at->format('d/m/Y') }}</td>
                <td>{{ $bill->customer->name }}</td>
                <td>{{ $bill->bill_type }}</td>
                <td class="text-right amount">Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                <td>{{ $bill->isPaid() ? 'Terbayar' : 'Belum Bayar' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Rincian Pengeluaran</h2>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                <td>{{ $expense->category->name }}</td>
                <td>{{ $expense->description }}</td>
                <td class="text-right amount">Rp{{ number_format($expense->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Catatan:</strong> Laporan ini merupakan dokumen resmi yang menunjukkan transparansi pengelolaan keuangan koperasi.</p>
        <p>Untuk pertanyaan, silakan hubungi bendahara atau pengurus koperasi.</p>
    </div>
</body>
</html>
