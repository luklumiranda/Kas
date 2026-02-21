@extends('layouts.app')
@section('title', 'Edit Kategori Pengeluaran')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="h3 font-weight-bold mb-4"><i class="fas fa-edit mr-2"></i>Edit Kategori Pengeluaran</h1>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Error!</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('expense-category.update', $expenseCategory) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                value="{{ $expenseCategory->name }}" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ $expenseCategory->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save mr-1"></i> Update</button>
                            <a href="{{ route('expense-category.index') }}" class="btn btn-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
