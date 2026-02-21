@extends('layouts.app')
@section('title', 'Kategori Pengeluaran')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 font-weight-bold"><i class="fas fa-tags mr-2"></i>Kategori Pengeluaran</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('expense-category.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Kategori Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Pengeluaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td>{{ Str::limit($category->description, 40) }}</td>
                            <td><span class="badge badge-info">{{ $category->expenses_count }}</span></td>
                            <td>
                                <a href="{{ route('expense-category.edit', $category) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('expense-category.destroy', $category) }}" method="POST" style="display:inline;">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $categories->links() }}
        </div>
    </div>
</div>
<style>.btn-xs { padding: .25rem .5rem; font-size: .875rem; }</style>
@endsection
