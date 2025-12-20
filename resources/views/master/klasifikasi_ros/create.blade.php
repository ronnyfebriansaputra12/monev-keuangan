@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Tambah Klasifikasi RO</h4>

        <a href="{{ route('master.klasifikasi-ros.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('master.klasifikasi-ros.index') }}">Klasifikasi RO</a></li>
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
        <div class="card-title mb-0">Form Tambah Klasifikasi RO</div>
    </div>

    <form action="{{ route('master.klasifikasi-ros.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Kegiatan <span class="text-danger">*</span></label>
                    <select name="kegiatan_id" class="form-select @error('kegiatan_id') is-invalid @enderror">
                        <option value="">-- Pilih Kegiatan --</option>
                        @foreach($kegiatans as $k)
                        <option value="{{ $k->id }}" @selected(old('kegiatan_id')==$k->id)>
                            {{ $k->kode_kegiatan }} - {{ $k->nama_kegiatan }}
                            ({{ $k->program?->satker?->nama_satker }})
                        </option>
                        @endforeach
                    </select>
                    @error('kegiatan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tahun Anggaran <span class="text-danger">*</span></label>
                    <input type="number" name="tahun_anggaran"
                        value="{{ old('tahun_anggaran', date('Y')) }}"
                        class="form-control @error('tahun_anggaran') is-invalid @enderror"
                        min="2000" max="2100">
                    @error('tahun_anggaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kode Klasifikasi <span class="text-danger">*</span></label>
                    <input type="text" name="kode_klasifikasi"
                        value="{{ old('kode_klasifikasi') }}"
                        class="form-control @error('kode_klasifikasi') is-invalid @enderror"
                        placeholder="Contoh: RO01" maxlength="20">
                    @error('kode_klasifikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Nama Klasifikasi <span class="text-danger">*</span></label>
                    <input type="text" name="nama_klasifikasi"
                        value="{{ old('nama_klasifikasi') }}"
                        class="form-control @error('nama_klasifikasi') is-invalid @enderror"
                        placeholder="Contoh: Klasifikasi Rincian Output..." maxlength="255">
                    @error('nama_klasifikasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.klasifikasi-ros.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection