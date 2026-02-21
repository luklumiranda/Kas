@extends('layouts.app')
@section('title', 'Edit Laporan Transparansi')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="h3 font-weight-bold mb-4"><i class="fas fa-edit mr-2"></i>Edit Laporan Transparansi</h1>

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
                    <form action="{{ route('transparency.update', $transparencyReport) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="title">Judul Laporan <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                                value="{{ $transparencyReport->title }}" required>
                            @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ $transparencyReport->description }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" 
                                        value="{{ $transparencyReport->start_date->format('Y-m-d') }}" required>
                                    @error('start_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Tanggal Akhir <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                        value="{{ $transparencyReport->end_date->format('Y-m-d') }}" required>
                                    @error('end_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_active" value="1" {{ $transparencyReport->is_active ? 'checked' : '' }}>
                                Aktifkan laporan ini
                            </label>
                        </div>

                        <div class="alert alert-info">
                            <strong>Link Publik:</strong><br>
                            <code>{{ route('transparency.public', $transparencyReport->access_token) }}</code>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-save mr-1"></i> Update</button>
                            <a href="{{ route('transparency.index') }}" class="btn btn-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
