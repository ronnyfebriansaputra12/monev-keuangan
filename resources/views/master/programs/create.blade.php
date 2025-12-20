@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Tambah Program</h4>

        <a href="{{ route('master.programs.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('master.programs.index') }}">Program</a></li>
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
        <div class="card-title mb-0">Form Tambah Program</div>
    </div>

    <form action="{{ route('master.programs.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Satker <span class="text-danger">*</span></label>
                    <select name="satker_id" class="form-select @error('satker_id') is-invalid @enderror">
                        <option value="">-- Pilih Satker --</option>
                        @foreach($satkers as $s)
                            <option value="{{ $s->id }}" @selected(old('satker_id') == $s->id)>
                                {{ $s->nama_satker }}
                            </option>
                        @endforeach
                    </select>
                    @error('satker_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tahun Anggaran <span class="text-danger">*</span></label>
                    <input type="number" name="tahun_anggaran"
                           value="{{ old('tahun_anggaran', date('Y')) }}"
                           class="form-control @error('tahun_anggaran') is-invalid @enderror"
                           min="2000" max="2100">
                    @error('tahun_anggaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Kode Program <span class="text-danger">*</span></label>
                    <input type="text" name="kode_program"
                           value="{{ old('kode_program') }}"
                           class="form-control @error('kode_program') is-invalid @enderror"
                           placeholder="Contoh: 001" maxlength="50">
                    @error('kode_program') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Nama Program <span class="text-danger">*</span></label>
                    <input type="text" name="nama_program"
                           value="{{ old('nama_program') }}"
                           class="form-control @error('nama_program') is-invalid @enderror"
                           placeholder="Contoh: Program Pengembangan Sistem" maxlength="255">
                    @error('nama_program') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.programs.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection
