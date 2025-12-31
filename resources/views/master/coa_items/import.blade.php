@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div>
        <h4 class="page-title mb-1">Import Data Anggaran</h4>
        <ul class="breadcrumbs">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><span>Master Data</span></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><span>Import Anggaran</span></li>
        </ul>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-round shadow-sm">
            <div class="card-header">
                <div class="card-title">Upload File Excel Anggaran</div>
                <p class="text-muted small mb-0">Pastikan format Excel sesuai dengan struktur RKAKL (Program s/d COA)</p>
            </div>
            
            <form action="{{ route('import.anggaran') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="form-group mb-4">
                        <label class="form-label fw-bold text-uppercase small text-muted">Tahun Anggaran</label>
                        <select name="tahun" class="form-select form-control-lg @error('tahun') is-invalid @enderror" required>
                            <option value="">-- Pilih Tahun --</option>
                            @for ($i = date('Y'); $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}" {{ old('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label fw-bold text-uppercase small text-muted">File Excel (.xlsx, .xls)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-file-excel text-success"></i></span>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                        </div>
                        <small class="text-muted d-block mt-2">
                            *Gunakan kolom <strong>Catatan Desk</strong> untuk identifikasi level data (Program, Kegiatan, KRO, RO, KOM, SUBKOM, MAK, COA).
                        </small>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning border-0 mb-0">
                        <div class="d-flex">
                            <i class="fas fa-info-circle me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold">Informasi Penting:</h6>
                                <p class="small mb-0">Sistem akan menggunakan fungsi <strong>updateOrCreate</strong>. Jika kode data sudah ada pada tahun yang sama, sistem akan memperbarui data tersebut alih-alih membuat duplikat.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-3">
                    <a href="{{ route('master.coa-items.index') }}" class="btn btn-label-secondary btn-round me-2">Kembali</a>
                    <button type="submit" class="btn btn-primary btn-round">
                        <i class="fas fa-upload me-2"></i> Mulai Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection