@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <h4 class="page-title mb-0">Edit Komponen</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Master Data</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('master.komponens.index') }}">Komponen</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>Edit</span></li>
            </ul>
        </div>

        <a href="{{ route('master.komponens.index') }}" class="btn btn-light btn-sm">
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
        <div class="card-title mb-0">Form Edit Komponen</div>
    </div>

    <form action="{{ route('master.komponens.update', $komponen) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="row g-3">

                {{-- Rincian Output --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Rincian Output <span class="text-danger">*</span>
                    </label>

                    <select name="rincian_output_id"
                            class="form-select @error('rincian_output_id') is-invalid @enderror"
                            required>
                        <option value="">-- Pilih Rincian Output --</option>
                        @foreach($rincianOutputs as $ro)
                            <option value="{{ $ro->id }}"
                                @selected(old('rincian_output_id', $komponen->rincian_output_id) == $ro->id)>
                                {{ $ro->kode_ro }} - {{ $ro->nama_ro }}
                            </option>
                        @endforeach
                    </select>

                    @error('rincian_output_id')
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
                           value="{{ old('tahun_anggaran', $komponen->tahun_anggaran) }}"
                           class="form-control @error('tahun_anggaran') is-invalid @enderror"
                           required min="2000" max="2100">

                    @error('tahun_anggaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Kode Komponen --}}
                <div class="col-md-3">
                    <label class="form-label">
                        Kode Komponen <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="kode_komponen"
                           value="{{ old('kode_komponen', $komponen->kode_komponen) }}"
                           class="form-control @error('kode_komponen') is-invalid @enderror"
                           required maxlength="50"
                           placeholder="Contoh: 001">

                    @error('kode_komponen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nama Komponen --}}
                <div class="col-md-12">
                    <label class="form-label">
                        Nama Komponen <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="nama_komponen"
                           value="{{ old('nama_komponen', $komponen->nama_komponen) }}"
                           class="form-control @error('nama_komponen') is-invalid @enderror"
                           required maxlength="255"
                           placeholder="Contoh: Dukungan Operasional ...">

                    @error('nama_komponen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.komponens.index') }}" class="btn btn-light">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Update
            </button>
        </div>
    </form>
</div>

@endsection
