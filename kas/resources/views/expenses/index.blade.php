@extends('layouts.app')
@section('title', 'Manajemen Pengeluaran')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 font-weight-bold"><i class="fas fa-money-bill-wave mr-2 text-danger"></i>Pengeluaran</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('expense.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Pengeluaran Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                            <td><span class="badge badge-secondary">{{ $expense->category->name }}</span></td>
                            <td>{{ Str::limit($expense->description, 30) }}</td>
                            <td>Rp{{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td>{{ $expense->receipts->count() }} file</td>
                            <td>
                                <a href="{{ route('expense.show', $expense) }}" class="btn btn-info btn-xs" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('expense.edit', $expense) }}" class="btn btn-warning btn-xs" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('expense.destroy', $expense) }}" method="POST" style="display:inline;">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengeluaran</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $expenses->links() }}
        </div>
    </div>
</div>
<style>
    .btn-xs { padding: .25rem .5rem; font-size: .875rem; }
</style>
@endsection
