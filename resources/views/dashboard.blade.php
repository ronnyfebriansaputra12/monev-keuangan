@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="avatar avatar-xl bg-primary-gradient rounded-circle shadow-sm d-flex align-items-center justify-content-center text-white">
                <i class="fas fa-user-check fa-lg"></i>
            </div>
        </div>
        <div>
            <h3 class="fw-bold mb-0">Ringkasan Anggaran</h3>
            <p class="text-muted mb-0">Halo, <strong>{{ Auth::user()->name }}</strong>. Berikut adalah performa anggaran hari ini.</p>
        </div>
    </div>
    <div class="ms-md-auto py-3 py-md-0">
        <div class="btn-group shadow-sm">
            <button class="btn btn-white fw-bold px-3 border border-end-0">
                <i class="fas fa-cloud-download-alt me-2 text-primary"></i>Export
            </button>
            <a href="{{ route('realisasi-v2.index') }}" class="btn btn-primary fw-bold px-4">
                <i class="fas fa-plus-circle me-2"></i>Entri Baru
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1 uppercase tracking-wider">TOTAL PAGU</p>
                        <h3 class="fw-bold mb-0 mt-1">Rp 12.500.000</h3>
                    </div>
                    <div class="icon-shape bg-soft-primary text-primary rounded-3 px-3 py-2">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge bg-soft-success text-success fw-bold">
                        <i class="fas fa-arrow-up me-1"></i> 2.5%
                    </span>
                    <span class="text-muted small ms-2">Dari target awal</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1 uppercase tracking-wider">REALISASI</p>
                        <h3 class="fw-bold mb-0 mt-1 text-success">Rp 8.125.000</h3>
                    </div>
                    <div class="icon-shape bg-soft-success text-success rounded-3 px-3 py-2">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress rounded-pill" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 65%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-1 uppercase tracking-wider">SISA PAGU</p>
                        <h3 class="fw-bold mb-0 mt-1 text-warning">Rp 4.375.000</h3>
                    </div>
                    <div class="icon-shape bg-soft-warning text-warning rounded-3 px-3 py-2">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                </div>
                <p class="mb-0 mt-3 small text-muted"><i class="fas fa-info-circle me-1"></i> Tersisa 4 bulan lagi</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4 text-center">
                <p class="text-muted small fw-bold mb-2 uppercase tracking-wider">PROGRES PENYERAPAN</p>
                <h2 class="fw-black mb-0 text-primary">65.4%</h2>
                <small class="text-muted">Target Tahunan: 95%</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between">
                <h5 class="fw-bold mb-0 text-dark">Laporan Realisasi Bulanan</h5>
                <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold">
                    <option>Tahun 2024</option>
                    <option>Tahun 2023</option>
                </select>
            </div>
            <div class="card-body p-4">
                <div style="height: 350px">
                    <canvas id="modernChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-dark">Antrian Berkas</h5>
            </div>
            <div class="card-body p-4">
                <div class="timeline-task">
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm rounded-circle bg-soft-info text-info fw-bold shadow-sm border border-info">V</div>
                        </div>
                        <div class="ms-3 w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Verifikasi Berkas</h6>
                                <span class="badge rounded-pill bg-info">12 Berkas</span>
                            </div>
                            <small class="text-muted">Review kelengkapan dokumen</small>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm rounded-circle bg-soft-primary text-primary fw-bold shadow-sm border border-primary">P</div>
                        </div>
                        <div class="ms-3 w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Proses PPK</h6>
                                <span class="badge rounded-pill bg-primary">5 Berkas</span>
                            </div>
                            <small class="text-muted">Persetujuan pejabat pengadaan</small>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm rounded-circle bg-soft-warning text-warning fw-bold shadow-sm border border-warning">B</div>
                        </div>
                        <div class="ms-3 w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Bendahara</h6>
                                <span class="badge rounded-pill bg-warning text-dark">8 Berkas</span>
                            </div>
                            <small class="text-muted">Persiapan pencairan dana</small>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-2 border-top">
                    <a href="{{ route('realisasi-v2.index') }}" class="btn btn-light w-100 fw-bold py-2 rounded-3 text-primary transition-all hover-up">
                        Lihat Semua Detail <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary {
        background-color: rgba(23, 125, 255, 0.1);
    }

    .bg-soft-success {
        background-color: rgba(40, 167, 69, 0.1);
    }

    .bg-soft-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .bg-soft-info {
        background-color: rgba(23, 162, 184, 0.1);
    }

    .bg-primary-gradient {
        background: linear-gradient(135deg, #177dff 0%, #0052b1 100%);
    }

    .rounded-4 {
        border-radius: 1.25rem !important;
    }

    .uppercase {
        text-transform: uppercase;
    }

    .tracking-wider {
        letter-spacing: 0.05em;
    }

    .fw-black {
        font-weight: 900;
    }

    .avatar-xl {
        width: 48px;
        height: 48px;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .hover-up:hover {
        transform: translateY(-3px);
    }
</style>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
<script>
    var ctx = document.getElementById('modernChart').getContext('2d');

    // Gradasi Warna Biru
    var gradientFill = ctx.createLinearGradient(0, 0, 0, 300);
    gradientFill.addColorStop(0, "rgba(23, 125, 255, 0.4)");
    gradientFill.addColorStop(1, "rgba(255, 255, 255, 0)");

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
            datasets: [{
                label: "Realisasi (Juta)",
                borderColor: "#177dff",
                backgroundColor: gradientFill,
                pointBackgroundColor: "#fff",
                pointBorderColor: "#177dff",
                pointBorderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 8,
                fill: true,
                borderWidth: 4,
                tension: 0.4, // Membuat line melengkung (smooth)
                data: [420, 380, 560, 490, 610, 520, 680, 740, 810, 920, 950, 1100]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "#f8f9fa",
                        drawBorder: false
                    },
                    ticks: {
                        color: "#adb5bd"
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: "#adb5bd"
                    }
                }
            }
        }
    });
</script>
@endpush