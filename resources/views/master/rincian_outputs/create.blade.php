@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h4 class="page-title mb-0">Tambah Rincian Output</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Master Data</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('master.rincian-outputs.index') }}">Rincian Output</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>Tambah</span></li>
            </ul>
        </div>

        <a href="{{ route('master.rincian-outputs.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Summary error (optional) --}}
@if ($errors->any())
<div class="alert alert-danger">
    <div class="fw-bold mb-1">Periksa kembali input kamu:</div>
    <ul class="mb-0">
        @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-header">
        <div class="card-title mb-0">Form Tambah Rincian Output</div>
    </div>

    <form action="{{ route('master.rincian-outputs.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="card-body">
            <div class="row g-3">

                {{-- Klasifikasi RO --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Klasifikasi RO <span class="text-danger">*</span>
                    </label>

                    <select name="klasifikasi_ro_id"
                        class="form-select @error('klasifikasi_ro_id') is-invalid @enderror">
                        <option value="">-- Pilih Klasifikasi --</option>
                        @foreach($klasifikasiRos as $r)
                        <option value="{{ $r->id }}"
                            @selected(old('klasifikasi_ro_id')==$r->id)>
                            {{ $r->kode_klasifikasi }} - {{ $r->nama_klasifikasi }}
                        </option>
                        @endforeach
                    </select>

                    @error('klasifikasi_ro_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tahun --}}
                <div class="col-md-3">
                    <label class="form-label">
                        Tahun Anggaran <span class="text-danger">*</span>
                    </label>

                    <input type="number"
                        name="tahun_anggaran"
                        value="{{ old('tahun_anggaran', date('Y')) }}"
                        class="form-control @error('tahun_anggaran') is-invalid @enderror"
                        min="2000" max="2100">

                    @error('tahun_anggaran')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kode RO --}}
                <div class="col-md-3">
                    <label class="form-label">
                        Kode RO <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                        name="kode_ro"
                        value="{{ old('kode_ro') }}"
                        class="form-control @error('kode_ro') is-invalid @enderror"
                        maxlength="50"
                        placeholder="Contoh: 001">

                    @error('kode_ro')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nama RO --}}
                <div class="col-md-12">
                    <label class="form-label">
                        Nama RO <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                        name="nama_ro"
                        value="{{ old('nama_ro') }}"
                        class="form-control @error('nama_ro') is-invalid @enderror"
                        maxlength="255"
                        placeholder="Contoh: Layanan Data dan Informasi ...">

                    @error('nama_ro')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.rincian-outputs.index') }}" class="btn btn-light">Batal</a>
            <button class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection