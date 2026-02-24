@extends('layouts.index')

{{-- Tambahkan CSS DataTables & Select2 untuk dropdown yang lebih baik --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .filter-box {
        background: #ffffff;
        border: 1px solid #e3e6f0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .nav-pills.nav-secondary .nav-link {
        background: #f8f9fc;
        color: #5a5c69;
        border-radius: 12px;
        margin-right: 10px;
        margin-bottom: 12px;
        padding: 12px 20px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #eaecf4;
        display: flex;
        align-items: center;
        min-height: 45px;
    }

    .nav-pills.nav-secondary .nav-link.active {
        background: #6861ce !important;
        color: white !important;
        border-color: #6861ce;
        box-shadow: 0 8px 15px rgba(104, 97, 206, 0.25);
        transform: translateY(-2px);
    }

    /* ANIMASI KEDIP SOFT (MENGGUNAKAN RGBA) */
    @keyframes blink-soft {
        0% {
            background-color: #f44336;
            color: white;
        }

        50% {
            background-color: rgba(244, 67, 54, 0.15);
            color: #f44336;
        }

        100% {
            background-color: #f44336;
            color: white;
        }
    }

    .blink-danger {
        animation: blink-soft 2s infinite ease-in-out !important;
        border-color: #f44336 !important;
    }

    .blink-danger .badge-count {
        background: white !important;
        color: #f44336 !important;
    }

    .badge-count {
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 0.75rem;
        margin-left: 10px;
        font-weight: 800;
        min-width: 25px;
        text-align: center;
    }

    .count-default {
        background: #eaecf4;
        color: #5a5c69;
    }

    .count-warning {
        background: #fff4e5;
        color: #ff9800;
    }

    .count-danger {
        background: #ffebee;
        color: #f44336;
    }

    .count-success {
        background: #e8f5e9;
        color: #4caf50;
    }

    .count-info {
        background: #e3f2fd;
        color: #2196f3;
    }

    .nav-link.active .badge-count {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    #realisasiTable {
        border-collapse: collapse !important;
    }

    #realisasiTable thead th {
        background-color: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.75rem;
        color: #4e73df;
    }

    #realisasiTable tfoot th {
        background-color: #f8f9fc;
        border-top: 2px solid #e3e6f0;
        color: #3a3b45;
    }

    .filter-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #9e9e9e;
        margin-bottom: 5px;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Realisasi Anggaran</h6>
                        <span class="text-muted small">{{ $selectedCoa->uraian ?? 'Semua Mata Anggaran' }}</span>
                    </div>
                    <div>
                        @if($coaItemId)
                        <a href="{{ route('realisasi-v2.create', ['coa_item_id' => $coaItemId]) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Realisasi
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    {{-- Statistik Ringkasan Pagu --}}
                    @if($selectedCoa)
                    <div class="row mb-4">
                        <div class="col-xl-4 col-md-6 mb-2">
                            <div class="border rounded p-3 bg-light">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pagu</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($selectedCoa->jumlah, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-2">
                            <div class="border rounded p-3 bg-light border-left-danger">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Realisasi (Kumulatif)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($selectedCoa->realisasi_total, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 mb-2">
                            <div class="border rounded p-3 bg-light border-left-success">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sisa Anggaran</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format(($selectedCoa->jumlah - $selectedCoa->realisasi_total), 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Filter Navigation --}}
                    <div class="filter-box shadow-sm">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary-soft p-2 rounded mr-2">
                                <i class="fas fa-layer-group text-primary"></i>
                            </div>
                            <label class="small fw-bold text-uppercase text-muted m-0">Filter Berdasarkan Progres Berkas</label>
                        </div>

                        @php
                        $currentStatus = request('status_berkas');
                        $statusConfig = [
                        'Draft' => ['color' => 'count-default'],
                        'Menunggu Finalisasi Bendahara' => ['color' => 'count-warning'],
                        'Ditolak/Revisi' => ['color' => 'count-danger'],
                        'Proses Verifikasi' => ['color' => 'count-info'],
                        'Terverifikasi' => ['color' => 'count-info'],
                        'Proses PPK' => ['color' => 'count-info'],
                        'Proses PPSPM' => ['color' => 'count-info'],
                        'Selesai' => ['color' => 'count-success'],
                        ];
                        $totalCountAll = isset($counts) ? array_sum($counts) : 0;
                        @endphp

                        <ul class="nav nav-pills nav-secondary mb-4" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ empty($currentStatus) ? 'active' : '' }}"
                                    href="{{ route('realisasi-v2.index', ['coa_item_id' => $coaItemId, 'search' => request('search')]) }}">
                                    Semua Berkas <span class="badge-count count-default">{{ (int)$totalCountAll }}</span>
                                </a>
                            </li>
                            @foreach($statusConfig as $statusName => $cfg)
                            @php
                            $shouldBlink = ($statusName === 'Ditolak/Revisi' && isset($counts[$statusName]) && $counts[$statusName] > 0);
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link {{ $currentStatus == $statusName ? 'active' : '' }} {{ $shouldBlink ? 'blink-danger' : '' }}"
                                    href="{{ route('realisasi-v2.index', ['coa_item_id' => $coaItemId, 'status_berkas' => $statusName, 'search' => request('search')]) }}">
                                    {{ e($statusName) }}
                                    <span class="badge-count {{ $cfg['color'] }}">{{ (int)($counts[$statusName] ?? 0) }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>

                        {{-- FILTER BERJENJANG --}}
                        <div class="pt-4 border-top">
                            <form action="{{ route('realisasi-v2.index') }}" method="GET">
                                <input type="hidden" name="coa_item_id" value="{{ e($coaItemId) }}">
                                <input type="hidden" name="status_berkas" value="{{ e($currentStatus) }}">

                                <div class="row">
                                    {{-- FILTER KHUSUS PPSPM & BENDAHARA --}}
                                    @if(in_array(auth()->user()->role, ['PPSPM', 'Bendahara', 'PPBJ', 'PLO','Verifikator', 'PPK']))
                                    <div class="col-md-3 mb-3">
                                        <label class="filter-label text-primary font-weight-bold">
                                            <i class="fas fa-filter mr-1"></i> Jenis Realisasi
                                        </label>
                                        <select name="jenis_realisasi" class="form-control form-control-sm select2-filter" onchange="this.form.submit()">
                                            <option value="">-- Semua Jenis --</option>
                                            @foreach($listJenisRealisasi as $jenis)
                                            <option value="{{ e($jenis) }}" {{ request('jenis_realisasi') == $jenis ? 'selected' : '' }}>{{ e($jenis) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif

                                    <div class="col-md-3 mb-3">
                                        <label class="filter-label">Tanggal Mulai</label>
                                        <input type="date" name="tgl_awal" class="form-control form-control-sm" value="{{ e(request('tgl_awal')) }}" onchange="this.form.submit()">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="filter-label">Tanggal Selesai</label>
                                        <input type="date" name="tgl_akhir" class="form-control form-control-sm" value="{{ e(request('tgl_akhir')) }}" onchange="this.form.submit()">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="filter-label">Filter GUP</label>
                                        <select name="filter_gup" class="form-control form-control-sm select2-filter" onchange="this.form.submit()">
                                            <option value="">-- Semua GUP --</option>
                                            @foreach($listGup as $gup)
                                            <option value="{{ e($gup) }}" {{ request('filter_gup') == $gup ? 'selected' : '' }}>{{ e($gup) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="filter-label">Filter RO</label>
                                        <select name="filter_ro" class="form-control form-control-sm select2-filter" onchange="this.form.submit()">
                                            <option value="">-- Semua RO --</option>
                                            @foreach($listRo as $kode_ro)
                                            <option value="{{ e($kode_ro) }}" {{ request('filter_ro') == $kode_ro ? 'selected' : '' }}>{{ e($kode_ro) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="filter-label">Filter Akun</label>
                                        <select name="filter_akun" class="form-control form-control-sm select2-filter" onchange="this.form.submit()">
                                            <option value="">-- Semua Akun --</option>
                                            @foreach($listAkun as $row)
                                            <option value="{{ e($row->kode_akun) }}" {{ request('filter_akun') == $row->kode_akun ? 'selected' : '' }}>{{ e($row->kode_akun) }} - {{ e($row->nama_akun) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="filter-label">Pencarian Cepat</label>
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Kode PLO, Penerima, atau Uraian..." value="{{ e(request('search')) }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-sm px-3" type="submit"><i class="fas fa-search"></i> Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <div class="btn-group">
                                            <button type="submit" formaction="{{ route('realisasi-v2.export-excel') }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-file-excel mr-1"></i> Export Excel
                                            </button>
                                            <a href="{{ route('realisasi-v2.index', ['coa_item_id' => $coaItemId]) }}" class="btn btn-light btn-sm border">
                                                <i class="fas fa-sync-alt"></i> Reset Filter
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Data Table --}}
                    <div class="table-responsive">
                        <table id="realisasiTable" class="table table-hover small w-100">
                            <thead class="text-center">
                                <tr>
                                    <th>Struktur Anggaran</th>
                                    <th>Kode PLO</th>
                                    <th>Jenis</th>
                                    <th>No Urut</th>
                                    <th>Tgl Kuitansi</th>
                                    <th>Penerima</th>
                                    <th>Uraian</th>
                                    <th>Bruto</th>
                                    <th>Status</th>
                                    <th>GUP</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                @php
                                $coa = $item->coaItem;
                                $mak = $coa?->mak;
                                $kodeAkun = $mak?->akun?->kode_akun ?? '-';
                                $kodeRo = $coa?->subKomponen?->komponen?->rincianOutput?->kode_ro ?? '-';

                                $badgeColor = match($item->status_berkas) {
                                'Selesai' => 'success',
                                'Ditolak/Revisi' => 'danger',
                                'Terverifikasi', 'Proses Verifikasi', 'Proses PPK', 'Proses PPSPM' => 'info',
                                default => 'warning'
                                };
                                @endphp
                                <tr>
                                    <td class="align-middle">
                                        <div class="small">
                                            <div class="mb-1">
                                                <span class="text-primary font-weight-bold" style="width: 45px; display: inline-block;">MAK:</span>
                                                <span class="text-dark">{{ e($mak?->nama_mak ?? '-') }}</span>
                                            </div>
                                            <div class="mb-1">
                                                <span class="text-info font-weight-bold" style="width: 45px; display: inline-block;">RO:</span>
                                                <span class="badge badge-info-soft text-info border-info border">{{ e($kodeRo) }}</span>
                                            </div>
                                            <div>
                                                <span class="text-success font-weight-bold" style="width: 45px; display: inline-block;">AKUN:</span>
                                                <span class="badge badge-light border text-dark">{{ e($kodeAkun) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold text-dark">{{ e($item->kode_unik_plo) }}</td>

                                    <td class="text-center align-middle">
                                        <span class="badge text-white"
                                            style="background-color: {{ $item->jenis_realisasi == 'LS' ? '#6f42c1' : '#fd7e14' }};">
                                            {{ e($item->jenis_realisasi) }}
                                        </span>
                                    </td>

                                    <td class="text-center align-middle">{{ str_pad($item->no_urut, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="text-center align-middle">{{ optional($item->tgl_kuitansi)->format('d/m/Y') }}</td>
                                    <td class="align-middle">{{ e($item->penerima_penyedia) }}</td>
                                    <td class="align-middle text-muted">{{ Str::limit($item->uraian, 35) }}</td>
                                    <td class="text-end align-middle font-weight-bold">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-{{ $badgeColor }} px-3 py-2 rounded-pill shadow-xs">
                                            {{ e($item->status_berkas) }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($item->gup)
                                        <span class="badge badge-secondary px-2 py-1">{{ e($item->gup) }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group">
                                            <a href="{{ route('realisasi-v2.edit', $item->id) }}" class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('realisasi-v2.show', $item->id) }}" class="btn btn-sm btn-outline-info border-0" title="Detail"><i class="fas fa-eye"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-end align-middle py-3">
                                        <span class="text-uppercase font-weight-bold">Total Realisasi :</span>
                                    </th>
                                    <th class="text-end align-middle py-3">
                                        <span class="h6 mb-0 font-weight-bold text-primary">
                                            Rp {{ number_format($totalRealisasiFiltered ?? 0, 0, ',', '.') }}
                                        </span>
                                    </th>
                                    <th colspan="3" class="bg-light"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
    $(function() {
        // ✅ DataTables
        $('#realisasiTable').DataTable({
            pageLength: 25,
            order: [
                [3, 'asc']
            ],
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            }
        });

        // ✅ SweetAlert Confirm Delete
        $(document).on('submit', '.form-delete', function(e) {
            e.preventDefault();
            const form = this;

            swal({
                title: "Yakin hapus?",
                text: "Data komponen ini tidak bisa dikembalikan.",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Batal",
                        visible: true,
                        className: "btn btn-light border"
                    },
                    confirm: {
                        text: "Ya, hapus",
                        className: "btn btn-danger"
                    }
                },
                dangerMode: true
            }).then(ok => {
                if (ok) form.submit();
            });
        });

// ✅ Notification Logic
        const toast = (title, text, icon, btnClass) => {
            swal({
                title: title,
                text: text,
                icon: icon,
                button: {
                    text: "OK",
                    className: btnClass
                }
            });
        };

        @if(session('success'))
            toast("Berhasil", {!! json_encode(session('success')) !!}, "success", "btn btn-success");
        @endif

        @if(session('error'))
            toast("Gagal", {!! json_encode(session('error')) !!}, "error", "btn btn-danger");
        @endif

        @if($errors->any())
            toast("Gagal", {!! json_encode($errors->first()) !!}, "error", "btn btn-danger");
        @endif
    });
</script>
@endpush