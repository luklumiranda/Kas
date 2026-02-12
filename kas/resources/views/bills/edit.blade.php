@extends('layouts.app')

@section('title', 'Edit Tagihan')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="h3 font-weight-bold text-dark mb-4">
                <i class="fas fa-edit mr-2"></i>Edit Tagihan
            </h1>

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

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('bill.update', $bill) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="customer_id">Siswa <span class="text-danger">*</span></label>
                            <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Siswa --</option>
                                @forelse($students as $student)
                                    <option value="{{ $student->id }}" {{ $bill->customer_id == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->username }})
                                    </option>
                                @empty
                                    <option disabled>Tidak ada siswa tersedia</option>
                                @endforelse
                            </select>
                            @error('customer_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="bill_type">Tipe Tagihan <span class="text-danger">*</span></label>
                            <input type="text" name="bill_type" id="bill_type" class="form-control @error('bill_type') is-invalid @enderror" 
                                value="{{ $bill->bill_type }}" required>
                            @error('bill_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="amount">Jumlah Tagihan <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                                step="1" min="0" value="{{ $bill->amount }}" required>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="due_date">Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                                value="{{ $bill->due_date->format('Y-m-d') }}" required>
                            @error('due_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Catatan (Opsional)</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                rows="3">{{ $bill->notes }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        @if($bill->isPaid())
                        <div class="alert alert-info mb-3">
                            <strong>Tagihan ini sudah terbayar</strong> sebesar Rp{{ number_format($bill->paid_amount, 0, ',', '.') }} pada {{ $bill->paid_date->format('d/m/Y H:i') }}
                        </div>
                        @endif

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-save mr-1"></i> Update Tagihan
                            </button>
                            <a href="{{ route('bill.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
