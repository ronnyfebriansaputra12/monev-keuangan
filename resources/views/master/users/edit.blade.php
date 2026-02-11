@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Edit User</h4>
        <a href="{{ route('master.users.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <form action="{{ route('master.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                {{-- Nama Lengkap --}}
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Role Selection --}}
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="">-- Pilih Role --</option>
                        @foreach(['PLO', 'Verifikator', 'Bendahara', 'PPK', 'PPSPM', 'PPBJ'] as $role)
                        <option value="{{ $role }}" @selected(old('role', $user->role) == $role)>
                            {{ $role }}
                        </option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- PLO Code --}}
                <div class="col-md-6">
                    <label class="form-label">PLO Code</label>
                    <input type="text" name="plo_code" value="{{ old('plo_code', $user->plo_code) }}" class="form-control @error('plo_code') is-invalid @enderror">
                    @error('plo_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <div class="alert alert-info py-2 mb-0">
                        <i class="fa fa-info-circle me-1"></i> Kosongkan password jika tidak ingin mengubahnya.
                    </div>
                </div>

                {{-- Password Baru --}}
                <div class="col-md-6">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @else
                    <small class="text-muted d-block mt-1">
                        Wajib mengandung: 1 Huruf Besar, Angka, & Simbol.
                    </small>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save me-1"></i> Update User
            </button>
        </div>
    </form>
</div>
@endsection