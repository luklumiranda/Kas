@extends('layouts.app')
@section('title', 'Laporan Transparansi')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 font-weight-bold"><i class="fas fa-file-pdf mr-2 text-danger"></i>Laporan Transparansi</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('transparency.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Laporan Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Akses Publik</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->title }}</td>
                            <td>{{ $report->start_date->format('d/m/Y') }} - {{ $report->end_date->format('d/m/Y') }}</td>
                            <td>
                                @if($report->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('transparency.public', $report->access_token) }}" target="_blank" class="badge badge-info" title="Buka di tab baru">
                                    <i class="fas fa-external-link-alt"></i> Lihat
                                </a>
                                <button class="btn btn-xs btn-light" onclick="copyToken('{{ $report->access_token }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                            <td>
                                <a href="{{ route('transparency.edit', $report) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('transparency.destroy', $report) }}" method="POST" style="display:inline;">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Yakin?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada laporan transparansi</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $reports->links() }}
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h6 class="m-0"><i class="fas fa-download mr-2"></i>Export Laporan Cepat</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="" class="form-inline">
                <div class="form-group mr-2">
                    <label for="start_date" class="mr-2">Dari:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="form-group mr-2">
                    <label for="end_date" class="mr-2">Sampai:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>
                <a href="{{ route('transparency.exportPdf') }}" class="btn btn-danger btn-sm mr-2" id="pdfExport">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <a href="{{ route('transparency.exportExcel') }}" class="btn btn-success btn-sm" id="excelExport">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
            </form>
        </div>
    </div>
</div>

<style>.btn-xs { padding: .25rem .5rem; font-size: .875rem; }</style>

<script>
    function copyToken(token) {
        const fullUrl = "{{ url('/laporan-publik/') }}/" + token;
        navigator.clipboard.writeText(fullUrl);
        alert('Link berhasil disalin!');
    }

    document.getElementById('pdfExport').addEventListener('click', function(e) {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        this.href = "{{ route('transparency.exportPdf') }}" + "?start_date=" + startDate + "&end_date=" + endDate;
    });

    document.getElementById('excelExport').addEventListener('click', function(e) {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        this.href = "{{ route('transparency.exportExcel') }}" + "?start_date=" + startDate + "&end_date=" + endDate;
    });
</script>
@endsection
