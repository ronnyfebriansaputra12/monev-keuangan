@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Tambah User</h4>
        <a href="{{ route('master.users.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <form action="{{ route('master.users.store') }}" method="POST" autocomplete="off">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                {{-- Bagian Role --}}
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" id="roleSelect" class="form-select @error('role') is-invalid @enderror">
                        <option value="">-- Pilih Role --</option>
                        @foreach(['PLO', 'Verifikator', 'Bendahara', 'PPK', 'PPSPM'] as $roleName)
                        <option value="{{ $roleName }}" @selected(old('role', $user->role ?? '') == $roleName)>
                            {{ $roleName }}
                        </option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6" id="ploCodeContainer">
                    <label class="form-label">PLO Code <span id="ploRequired" class="text-danger d-none">*</span></label>
                    <input type="text" name="plo_code" value="{{ old('plo_code', $user->plo_code ?? '') }}"
                        class="form-control @error('plo_code') is-invalid @enderror"
                        placeholder="Contoh: PLO-01">
                    @error('plo_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan User</button>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        function togglePloCode() {
            const role = $('#roleSelect').val();
            if (role === 'PLO') {
                $('#ploRequired').removeClass('d-none');
            } else {
                $('#ploRequired').addClass('d-none');
            }
        }

        $('#roleSelect').on('change', togglePloCode);
        togglePloCode(); // Jalankan saat halaman load
    });
</script>
@endpush