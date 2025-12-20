@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">

    {{-- BARIS 1: Judul + tombol --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Tambah Satker</h4>

        <a href="{{ route('master.satkers.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- BARIS 2: Breadcrumb --}}
    <ul class="breadcrumbs mb-0">
        <li class="nav-home">
            <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">
            <a href="{{ route('master.satkers.index') }}">Satker</a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><span>Tambah</span></li>
    </ul>

</div>
@endsection

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <div class="fw-bold mb-1">Periksa kembali input kamu:</div>
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-header">
        <div class="card-title mb-0">Form Tambah Satker</div>
    </div>

    <form action="{{ route('master.satkers.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="card-body">
            <div class="row g-3">

                {{-- Kode Satker --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Kode Satker <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="kode_satker"
                        value="{{ old('kode_satker') }}"
                        class="form-control @error('kode_satker') is-invalid @enderror"
                        placeholder="Contoh: PSDTN001"
                        maxlength="50">
                    @error('kode_satker')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tahun Anggaran --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Tahun Anggaran <span class="text-danger">*</span>
                    </label>
                    <input
                        type="number"
                        name="tahun_anggaran"
                        value="{{ old('tahun_anggaran', date('Y')) }}"
                        class="form-control @error('tahun_anggaran') is-invalid @enderror"
                        min="2000"
                        max="2100">
                    @error('tahun_anggaran')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- spacer biar baris atas rapi (optional) --}}
                <div class="col-md-4 d-none d-md-block"></div>

                {{-- Nama Satker --}}
                <div class="col-md-12">
                    <label class="form-label">
                        Nama Satker <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="nama_satker"
                        value="{{ old('nama_satker') }}"
                        class="form-control @error('nama_satker') is-invalid @enderror"
                        placeholder="Contoh: Pusat Data dan Informasi Obat dan Makanan"
                        maxlength="255">
                    @error('nama_satker')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.satkers.index') }}" class="btn btn-light">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>

@endsection