@extends('layouts.app')
@section('title', 'Detail Pengeluaran')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h1 class="h3 font-weight-bold mb-4"><i class="fas fa-receipt mr-2"></i>Detail Pengeluaran</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Kategori:</strong> {{ $expense->category ? $expense->category->name : '-' }}</p>
                            <p><strong>Deskripsi:</strong> {{ $expense->description }}</p>
                            <p><strong>Tanggal:</strong> {{ $expense->expense_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Jumlah:</strong> <span class="h4 text-danger">Rp{{ number_format($expense->amount, 0, ',', '.') }}</span></p>
                            <p><strong>Dicatat oleh:</strong> {{ $expense->user ? $expense->user->name : '-' }}</p>
                            <p><strong>Tanggal Input:</strong> {{ $expense->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if($expense->notes)
                    <div class="alert alert-info">
                        <strong>Catatan:</strong> {{ $expense->notes }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="m-0"><i class="fas fa-file mr-2"></i>Bukti Nota ({{ $expense->receipts->count() }} file)</h6>
                </div>
                <div class="card-body">
                    @if($expense->receipts->count())
                        <div class="row">
                            @foreach($expense->receipts as $receipt)
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        @if(in_array(pathinfo($receipt->file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                            <img src="{{ asset('storage/' . $receipt->file_path) }}" class="img-fluid" style="max-height: 150px;">
                                        @else
                                            <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                        @endif
                                        <p class="mt-2 small">{{ Str::limit($receipt->file_name, 25) }}</p>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ asset('storage/' . $receipt->file_path) }}" target="_blank" class="btn btn-primary" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('expense.deleteReceipt', $receipt) }}" method="POST" style="display:inline;">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin?')" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Tidak ada bukti nota</p>
                    @endif
                </div>
            </div>

            <div>
                <a href="{{ route('expense.edit', $expense) }}" class="btn btn-warning mr-2"><i class="fas fa-edit mr-1"></i> Edit</a>
                <a href="{{ route('expense.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
