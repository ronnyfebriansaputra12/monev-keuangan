@extends('layouts.auth')

@section('content')
<div class="main-login-page">
    <div class="login-container">
        <div class="login-brand-side d-none d-lg-flex">
            <div class="overlay"></div>
            <div class="brand-content">
                <div class="brand-badge mb-4">Sistem Informasi Keuangan</div>
                <h2 class="display-5 fw-bold text-white mb-3">
                    Monitoring Keuangan <br>
                    <span class="text-warning">Pusat Data dan Informasi Obat dan Makanan</span>
                </h2>
                <p class="opacity-75 lead">
                    Transparansi, Akurasi, dan Akuntabilitas dalam pengelolaan anggaran instansi secara digital.
                </p>
            </div>
        </div>

        <div class="login-form-side">
            {{-- Class 'shake' aktif jika ada error apapun --}}
            <div class="form-wrapper animated fadeIn @if($errors->any()) shake @endif">
                <div class="text-center mb-5">
                    <div class="logo-mobile d-lg-none mb-4">
                        <img src="{{ asset('assets/img/kaiadmin/logo_dark.svg') }}" alt="logo" height="40">
                    </div>
                    <h2 class="fw-bold text-dark">Selamat Datang</h2>
                    <p class="text-muted small">Silakan masuk untuk mengelola data keuangan</p>
                </div>

                @if (session('status'))
                <div class="alert alert-success border-0 shadow-sm mb-4">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                </div>
                @endif

                {{-- Alert Throttling (Percobaan Login Berlebih) --}}
                @if($errors->has('email'))
                @php
                $errorMsg = $errors->first('email');
                $isThrottled = Str::contains($errorMsg, 'Terlalu banyak percobaan') || Str::contains($errorMsg, 'menit');
                @endphp

                @if($isThrottled)
                <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 16px; background-color: #fff5f5; color: #dc3545;">
                    <i class="fas fa-lock me-3 fa-lg"></i>
                    <div>
                        <strong class="d-block">Akses Ditangguhkan</strong>
                        <span class="small">{{ $errorMsg }}</span>
                    </div>
                </div>
                @endif
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email Field --}}
                    <div class="input-group-modern mb-4">
                        <label class="small fw-bold text-uppercase text-muted mb-2 d-block">Email Address</label>
                        <div class="input-wrapper">
                            <i class="far fa-envelope"></i>
                            <input id="email" type="email" name="email"
                                class="@error('email') is-invalid @enderror"
                                value="{{ old('email') }}" placeholder="name@bpom.go.id" required autofocus
                                @if(isset($isThrottled) && $isThrottled) disabled @endif>
                        </div>
                        @if($errors->has('email') && !(isset($isThrottled) && $isThrottled))
                        <span class="text-danger small mt-1 d-block">
                            <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first('email') }}
                        </span>
                        @endif
                    </div>

                    {{-- Password Field --}}
                    <div class="input-group-modern mb-3">
                        <label class="small fw-bold text-uppercase text-muted mb-2 d-block">Password</label>
                        <div class="input-wrapper" style="position: relative; display: flex; align-items: center;">
                            <i class="far fa-lock-alt" style="position: absolute; left: 18px; color: #94a3b8; z-index: 10;"></i>

                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="••••••••" required
                                style="width: 100%; padding: 15px 50px 15px 52px; background: #f8fafc; border: 2px solid #f1f5f9; border-radius: 16px; font-weight: 500;"
                                @if(isset($isThrottled) && $isThrottled) disabled @endif>

                            <button type="button" class="btn-toggle-pw" onclick="togglePassword()"
                                style="position: absolute; right: 20px; background: none; border: none; color: #94a3b8; cursor: pointer; padding: 0; display: flex; align-items: center; z-index: 10;">
                                <i class="far fa-eye-slash" id="eye-icon" style="font-size: 1.1rem;"></i>
                            </button>
                        </div>

                        {{-- Tampilkan error validasi password (panjang, huruf besar, simbol) --}}
                        @error('password')
                        <span class="text-danger small mt-1 d-block">
                            <i class="fas fa-shield-alt me-1"></i> {{ $message }}
                        </span>
                        @else
                        {{-- Petunjuk format password jika tidak ada error --}}
                        <div class="d-flex align-items-center mt-2 opacity-75">
                            <i class="fas fa-info-circle text-primary me-2" style="font-size: 0.75rem;"></i>
                            <span class="text-muted" style="font-size: 0.7rem;">Min. 8 Karakter, 1 Huruf Besar, Angka & Simbol.</span>
                        </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary-modern w-100 mb-4"
                        @if(isset($isThrottled) && $isThrottled) disabled style="opacity: 0.6; cursor: not-allowed;" @endif>
                        Masuk Ke Sistem <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="small text-muted mb-1">Pusat Data dan Informasi Obat dan Makanan</p>
                    <p class="small text-muted fw-bold">&copy; {{ date('Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #0056b3;
        --secondary-color: #ffc107;
        --bg-light: #f4f7fa;
    }

    /* Animasi Shake untuk Error */
    .shake {
        animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
        transform: translate3d(0, 0, 0);
    }

    @keyframes shake {

        10%,
        90% {
            transform: translate3d(-1px, 0, 0);
        }

        20%,
        80% {
            transform: translate3d(2px, 0, 0);
        }

        30%,
        50%,
        70% {
            transform: translate3d(-4px, 0, 0);
        }

        40%,
        60% {
            transform: translate3d(4px, 0, 0);
        }
    }

    .main-login-page {
        height: 100vh;
        background: var(--bg-light);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
    }

    .login-container {
        display: flex;
        width: 100%;
        max-width: 1200px;
        height: 750px;
        background: #fff;
        border-radius: 40px;
        overflow: hidden;
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.15);
    }

    .login-brand-side {
        flex: 1.4;
        background: url('https://images.unsplash.com/photo-1454165833767-027ffea9e778?q=80&w=2070&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        position: relative;
        padding: 80px;
        display: flex;
        align-items: center;
        color: white;
    }

    .login-brand-side .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(0, 86, 179, 0.9), rgba(0, 31, 63, 0.95));
    }

    .brand-content {
        position: relative;
        z-index: 10;
    }

    .brand-badge {
        display: inline-block;
        padding: 8px 20px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .login-form-side {
        flex: 1;
        padding: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-wrapper {
        width: 100%;
        max-width: 400px;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrapper i {
        position: absolute;
        left: 18px;
        color: #94a3b8;
    }

    .input-wrapper input {
        width: 100%;
        padding: 15px 15px 15px 52px;
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        color: #1e293b;
    }

    .input-wrapper input:focus {
        background: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 5px rgba(0, 86, 179, 0.1);
        outline: none;
    }

    .input-wrapper input.is-invalid {
        border-color: #f87171;
        background-color: #fffef2;
    }

    .btn-primary-modern {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 18px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 16px;
        box-shadow: 0 10px 25px rgba(0, 86, 179, 0.2);
        transition: 0.4s;
    }

    .btn-primary-modern:hover:not(:disabled) {
        background: #004494;
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 86, 179, 0.3);
    }

    @media (max-width: 992px) {
        .login-container {
            height: auto;
            max-width: 480px;
            border-radius: 30px;
            margin: 20px;
        }

        .login-form-side {
            padding: 50px 30px;
        }
    }
</style>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    }
</script>
@endsection