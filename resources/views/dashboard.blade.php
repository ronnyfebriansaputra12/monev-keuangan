@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div>
        <h4 class="page-title mb-1">Dashboard Monitoring</h4>
        <p class="text-muted small mb-0">Selamat Datang, <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role }})</p>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
        <button class="btn btn-label-info btn-round me-2">Download Report</button>
        <a href="{{ route('realisasi-v2.index') }}" class="btn btn-primary btn-round">Lihat Realisasi</a>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Pagu</p>
                            <h4 class="card-title text-primary">Rp 12.5M</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-success bubble-shadow-small">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Realisasi</p>
                            <h4 class="card-title text-success">Rp 8.2M</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Sisa Pagu</p>
                            <h4 class="card-title text-warning">Rp 4.3M</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Penyerapan</p>
                            <h4 class="card-title text-danger">65.4%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Statistik Realisasi Bulanan</div>
                    <div class="card-tools">
                        <button class="btn btn-label-info btn-sm btn-icon"><i class="fa fa-print"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="min-height: 375px">
                    <canvas id="statisticsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Antrian Berkas Aktif</div>
            </div>
            <div class="card-body pb-0">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-title rounded-circle border border-white bg-info">V</span>
                        </div>
                        <span class="text-muted">Proses Verifikasi</span>
                    </div>
                    <span class="fw-bold">12 Berkas</span>
                </div>
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-title rounded-circle border border-white bg-primary">P</span>
                        </div>
                        <span class="text-muted">Proses PPK</span>
                    </div>
                    <span class="fw-bold">5 Berkas</span>
                </div>
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-title rounded-circle border border-white bg-warning">B</span>
                        </div>
                        <span class="text-muted">Proses Bendahara</span>
                    </div>
                    <span class="fw-bold">8 Berkas</span>
                </div>
                <hr>
                <div class="text-center pb-3">
                    <a href="{{ route('realisasi-v2.index') }}" class="btn btn-sm btn-link text-primary fw-bold">Lihat Semua Antrian</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
<script>
    var ctx = document.getElementById('statisticsChart').getContext('2d');
    var statisticsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
            datasets: [{
                label: "Realisasi",
                borderColor: '#177dff',
                pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
                pointRadius: 0,
                backgroundColor: 'rgba(23, 125, 255, 0.1)',
                legendColor: '#177dff',
                fill: true,
                borderWidth: 2,
                data: [542, 480, 430, 550, 530, 453, 380, 434, 568, 610, 700, 900]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: false
            },
            tooltips: {
                bodySpacing: 4,
                mode: "nearest",
                intersect: 0,
                position: "nearest",
                xPadding: 10,
                yPadding: 10,
                caretPadding: 10
            },
            layout: {
                padding: {
                    left: 5,
                    right: 5,
                    top: 15,
                    bottom: 15
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        fontStyle: "500",
                        beginAtZero: false,
                        maxTicksLimit: 5,
                        padding: 10
                    },
                    gridLines: {
                        drawTicks: false,
                        display: false
                    }
                }],
                xAxes: [{
                    gridLines: {
                        zeroLineColor: "transparent"
                    },
                    ticks: {
                        padding: 20,
                        fontStyle: "500"
                    }
                }]
            }
        }
    });
</script>
@endpush