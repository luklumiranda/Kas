@extends('layouts.app')
@section('title', 'Tambah Pengeluaran')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="h3 font-weight-bold mb-4"><i class="fas fa-plus mr-2"></i>Pengeluaran Baru</h1>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Error!</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('expense.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="expense_category_id">Kategori <span class="text-danger">*</span></label>
                            <select name="expense_category_id" id="expense_category_id" class="form-control @error('expense_category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('expense_category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi <span class="text-danger">*</span></label>
                            <input type="text" name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                placeholder="Contoh: Fotokopi untuk ujian" value="{{ old('description') }}" required>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="amount">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                                step="1" min="0" placeholder="Contoh: 250000" value="{{ old('amount') }}" required>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expense_date">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" id="expense_date" class="form-control @error('expense_date') is-invalid @enderror" 
                                value="{{ old('expense_date', date('Y-m-d')) }}" required>
                            @error('expense_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Catatan</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="receipts">Upload Bukti Nota <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" name="receipts[]" id="receipts" class="custom-file-input @error('receipts') is-invalid @enderror" 
                                    accept=".pdf,.jpg,.jpeg,.png" multiple required>
                                <label class="custom-file-label" for="receipts">Pilih file (PDF/JPG/PNG, max 5MB)</label>
                            </div>
                            <small class="form-text text-muted">Minimal 1 file, maksimal 5MB per file</small>
                            @error('receipts')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save mr-1"></i> Simpan</button>
                            <a href="{{ route('expense.index') }}" class="btn btn-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('receipts').addEventListener('change', function() {
        let names = Array.from(this.files).map(f => f.name).join(', ');
        document.querySelector('.custom-file-label').textContent = names || 'Pilih file...';
    });
</script>
@endsection
