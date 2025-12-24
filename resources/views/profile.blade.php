@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div>
        <h4 class="page-title mb-1">Profil Pengguna</h4>
        <ul class="breadcrumbs">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><span>Pengaturan</span></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><span>Profil</span></li>
        </ul>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-profile card-round">
            <div class="card-header" style="background-image: url('{{ asset('assets/img/blogpost.jpg') }}'); background-size: cover; height: 120px;">
                <div class="profile-picture">
                    <div class="avatar avatar-xl border border-white border-4 shadow-sm">
                        <img src="{{ asset('assets/img/profile.jpg') }}" alt="User Profile" class="avatar-img rounded-circle">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="user-profile text-center">
                    <div class="name fw-bold h4 mb-1">{{ Auth::user()->name }}</div>
                    <div class="job text-muted mb-2">{{ Auth::user()->email }}</div>
                    <div class="desc mb-3">
                        <span class="badge badge-primary rounded-pill px-3">{{ Auth::user()->role }}</span>
                    </div>
                    <div class="social-media">
                        <p class="small text-muted mb-0">Terdaftar Sejak:</p>
                        <p class="fw-bold">{{ Auth::user()->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row user-stats text-center">
                    <div class="col">
                        <div class="number text-info">12</div>
                        <div class="title small text-muted">Berkas Input</div>
                    </div>
                    <div class="col">
                        <div class="number text-success">45</div>
                        <div class="title small text-muted">Terverifikasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Edit Informasi Personal</div>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">NIP / ID Pegawai</label>
                            <input type="text" class="form-control bg-light" value="199208212020121001" readonly>
                            <small class="text-muted italic">*Hubungi admin untuk mengubah NIP</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jabatan dalam Sistem</label>
                            <input type="text" class="form-control bg-light" value="{{ Auth::user()->role }}" readonly>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="card-title mb-3">Ubah Kata Sandi</div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="reset" class="btn btn-label-secondary me-2">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .card-profile .profile-picture {
        margin-top: -55px;
        margin-bottom: 15px;
        display: flex;
        justify-content: center;
    }
    .user-stats .number {
        font-size: 18px;
        font-weight: 700;
    }
</style>
@endpush