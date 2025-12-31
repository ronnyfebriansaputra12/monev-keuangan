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
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="admin" @selected($user->role == 'admin')>Admin</option>
                        <option value="user" @selected($user->role == 'user')>User</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">PLO Code</label>
                    <input type="text" name="plo_code" value="{{ old('plo_code', $user->plo_code) }}" class="form-control">
                </div>
                <div class="col-md-12">
                    <div class="alert alert-info py-2 mb-0">
                        Kosongkan password jika tidak ingin mengubahnya.
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
    </form>
</div>
@endsection