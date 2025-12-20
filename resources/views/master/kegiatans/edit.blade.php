@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Edit Kegiatan</h4>

        <a href="{{ route('master.kegiatans.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('master.kegiatans.index') }}">Kegiatan</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><span>Edit</span></li>
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
        <div class="card-title mb-0">Form Edit Kegiatan</div>
    </div>

    <form action="{{ route('master.kegiatans.update', $kegiatan) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Program <span class="text-danger">*</span></label>
                    <select name="program_id" class="form-select @error('program_id') is-invalid @enderror">
                        <option value="">-- Pilih Program --</option>
                        @foreach($programs as $p)
                        <option value="{{ $p->id }}" @selected(old('program_id', $kegiatan->program_id) == $p->id)>
                            {{ $p->kode_program }} - {{ $p->nama_program }} ({{ $p->satker?->nama_satker }})
                        </option>
                        @endforeach
                    </select>
                    @error('program_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tahun Anggaran <span class="text-danger">*</span></label>
                    <input type="number" name="tahun_anggaran"
                        value="{{ old('tahun_anggaran', $kegiatan->tahun_anggaran) }}"
                        class="form-control @error('tahun_anggaran') is-invalid @enderror"
                        min="2000" max="2100">
                    @error('tahun_anggaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kode Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="kode_kegiatan"
                        value="{{ old('kode_kegiatan', $kegiatan->kode_kegiatan) }}"
                        class="form-control @error('kode_kegiatan') is-invalid @enderror"
                        maxlength="50">
                    @error('kode_kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kegiatan"
                        value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}"
                        class="form-control @error('nama_kegiatan') is-invalid @enderror"
                        maxlength="255">
                    @error('nama_kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.kegiatans.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Update
            </button>
        </div>
    </form>
</div>
@endsection