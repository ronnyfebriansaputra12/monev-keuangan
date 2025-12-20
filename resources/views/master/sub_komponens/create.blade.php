@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h4 class="page-title mb-0">Tambah Sub Komponen</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Master Data</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('master.sub-komponens.index') }}">Sub Komponen</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>Tambah</span></li>
            </ul>
        </div>

        <a href="{{ route('master.sub-komponens.index') }}" class="btn btn-light btn-sm">
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
        <div class="card-title mb-0">Form Tambah Sub Komponen</div>
    </div>

    <form action="{{ route('master.sub-komponens.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">
                        Komponen <span class="text-danger">*</span>
                    </label>
                    <select name="komponen_id"
                        class="form-select @error('komponen_id') is-invalid @enderror">
                        <option value="">-- Pilih Komponen --</option>
                        @foreach($komponens as $k)
                        <option value="{{ $k->id }}"
                            @selected(old('komponen_id')==$k->id)>
                            {{ $k->kode_komponen }} - {{ $k->nama_komponen }}
                            (RO: {{ $k->rincianOutput?->kode_ro }})
                        </option>
                        @endforeach
                    </select>
                    @error('komponen_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

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

                <div class="col-md-3">
                    <label class="form-label">
                        Kode Sub Komponen <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                        name="kode_subkomponen"
                        value="{{ old('kode_subkomponen') }}"
                        class="form-control @error('kode_subkomponen') is-invalid @enderror"
                        maxlength="50"
                        placeholder="Contoh: 001">
                    @error('kode_subkomponen')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">
                        Nama Sub Komponen <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                        name="nama_subkomponen"
                        value="{{ old('nama_subkomponen') }}"
                        class="form-control @error('nama_subkomponen') is-invalid @enderror"
                        maxlength="255"
                        placeholder="Contoh: Dukungan operasional ...">
                    @error('nama_subkomponen')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.sub-komponens.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection