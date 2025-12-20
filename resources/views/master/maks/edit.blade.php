@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="page-title mb-1">Edit MAK</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Master Data</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('master.maks.index') }}">MAK</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>Edit</span></li>
            </ul>
        </div>

        <a href="{{ route('master.maks.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
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
        <div class="card-title mb-0">Form Edit MAK</div>
    </div>

    <form action="{{ route('master.maks.update', $mak) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Master Akun <span class="text-danger">*</span></label>
                    <select name="akun_id" class="form-select @error('akun_id') is-invalid @enderror">
                        <option value="">-- Pilih Akun --</option>
                        @foreach($akuns as $a)
                        <option value="{{ $a->id }}" @selected(old('akun_id', $mak->akun_id) == $a->id)>
                            {{ $a->kode_akun }} - {{ $a->nama_akun }}
                        </option>
                        @endforeach
                    </select>
                    @error('akun_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jenis Belanja</label>
                    <input type="text"
                        name="jenis_belanja"
                        value="{{ old('jenis_belanja', $mak->jenis_belanja) }}"
                        class="form-control @error('jenis_belanja') is-invalid @enderror"
                        maxlength="50">
                    @error('jenis_belanja')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Nama MAK <span class="text-danger">*</span></label>
                    <input type="text"
                        name="nama_mak"
                        value="{{ old('nama_mak', $mak->nama_mak) }}"
                        class="form-control @error('nama_mak') is-invalid @enderror"
                        maxlength="255">
                    @error('nama_mak')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.maks.index') }}" class="btn btn-light">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Update
            </button>
        </div>
    </form>
</div>
@endsection