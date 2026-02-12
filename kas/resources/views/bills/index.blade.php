@extends('layouts.app')

@section('title', 'Data Tagihan')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 font-weight-bold text-dark">
                <i class="fas fa-receipt mr-2 text-primary"></i>Manajemen Tagihan
            </h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('bill.create') }}" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-plus mr-1"></i> Tagihan Baru
            </a>
            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#bulkBillModal">
                <i class="fas fa-copy mr-1"></i> Buat Massal
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="m-0">Daftar Tagihan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Tipe Tagihan</th>
                            <th>Jumlah</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bills as $bill)
                            <tr>
                                <td>
                                    <strong>{{ $bill->customer->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $bill->customer->student_id }}</small>
                                </td>
                                <td>{{ $bill->bill_type }}</td>
                                <td>Rp{{ number_format($bill->amount, 0, ',', '.') }}</td>
                                <td>{{ $bill->due_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($bill->isPaid())
                                        <span class="badge badge-success">Terbayar</span>
                                    @elseif($bill->isOverdue())
                                        <span class="badge badge-danger">Overdue</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$bill->isPaid())
                                        <button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#payBillModal{{ $bill->id }}">
                                            <i class="fas fa-check"></i> Bayar
                                        </button>
                                    @endif
                                    <a href="{{ route('bill.edit', $bill) }}" class="btn btn-xs btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('bill.destroy', $bill) }}" method="POST" style="display:inline;">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Yakin hapus?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Bayar -->
                            <div class="modal fade" id="payBillModal{{ $bill->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Catat Pembayaran</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('bill.markAsPaid', $bill) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Tagihan</label>
                                                    <input type="text" class="form-control" value="{{ $bill->customer->name }} - Rp{{ number_format($bill->amount, 0, ',', '.') }}" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label>Jumlah Dibayar <span class="text-danger">*</span></label>
                                                    <input type="number" name="paid_amount" class="form-control" step="1" min="0" max="{{ $bill->amount }}" required placeholder="Masukkan jumlah">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Catat Pembayaran</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data tagihan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $bills->links() }}
        </div>
    </div>
</div>

<!-- Modal Bulk Bill -->
<div class="modal fade" id="bulkBillModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Tagihan Massal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('bill.generateRoutine') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipe Tagihan <span class="text-danger">*</span></label>
                        <input type="text" name="bill_type" class="form-control" placeholder="Contoh: Kas Bulanan, Iuran" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Tagihan <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" step="1" min="0" required placeholder="Contoh: 5000">
                    </div>
                    <div class="form-group">
                        <label>Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <p class="text-muted small">Tagihan akan dibuat untuk semua siswa yang aktif</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate Tagihan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-xs {
        padding: .25rem .5rem;
        font-size: .875rem;
    }
</style>
@endsection
