@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h4 class="page-title mb-0">Tambah Master Akun</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Master Data</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('master.master-akuns.index') }}">Master Akun</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>Tambah</span></li>
            </ul>
        </div>

        <a href="{{ route('master.master-akuns.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <div class="fw-bold mb-1">Periksa kembali input kamu:</div>
    <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-header">
        <div class="card-title mb-0">Form Tambah Master Akun</div>
    </div>

    <form action="{{ route('master.master-akuns.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Kode Akun <span class="text-danger">*</span></label>
                    <input type="text"
                           name="kode_akun"
                           value="{{ old('kode_akun') }}"
                           class="form-control @error('kode_akun') is-invalid @enderror"
                           maxlength="20"
                           placeholder="Contoh: 532111"
                           required>
                    @error('kode_akun')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label">Nama Akun <span class="text-danger">*</span></label>
                    <input type="text"
                           name="nama_akun"
                           value="{{ old('nama_akun') }}"
                           class="form-control @error('nama_akun') is-invalid @enderror"
                           maxlength="255"
                           placeholder="Contoh: Belanja Modal Peralatan dan Mesin"
                           required>
                    @error('nama_akun')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.master-akuns.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection
