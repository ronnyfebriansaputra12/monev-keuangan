@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 w-100">
        <div>
            <h4 class="page-title mb-1">Chart of Accounts (COA)</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home">
                    <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Master Data</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>COA Items</span></li>
            </ul>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('import.anggaran.view') }}" class="btn btn-success btn-round btn-sm">
                <i class="fas fa-file-excel me-1"></i> Import Anggaran
            </a>

            <!-- <a href="{{ route('master.coa-items.create') }}" class="btn btn-primary btn-round btn-sm">
                <i class="fa fa-plus me-1"></i> Tambah COA (Manual)
            </a> -->
        </div>
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

{{-- Widget Statistik Anggaran --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card card-round border border-primary h-100 shadow-sm">
            <div class="card-body p-3">
                <div class="card-title fw-bold text-uppercase mb-3 text-primary" style="font-size: 0.75rem; letter-spacing: 1px;">
                    <i class="fas fa-wallet me-1"></i> Monitoring Pagu & Sisa Anggaran
                </div>
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <p class="card-category mb-1 text-muted">Total Pagu</p>
                        <h4 class="card-title text-primary fw-bold mb-0">Rp {{ number_format($totalSisa, 0, ',', '.') }}</h4>
                        <!-- <small class="text-muted small">Alokasi Awal</small> -->
                    </div>
                    <div class="col-6 border-end">
                        <p class="card-category mb-1">Realisasi (SP2D)</p>
                        <h4 class="card-title text-secondary fw-bold mb-0">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</h4>
                        <!-- <small class="text-muted small">Sudah Selesai</small> -->
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-round border border-secondary h-100 shadow-sm">
            <div class="card-body p-3">
                <div class="card-title fw-bold text-uppercase mb-3 text-secondary" style="font-size: 0.75rem; letter-spacing: 1px;">
                    <i class="fas fa-file-invoice-dollar me-1"></i> Monitoring Realisasi (Pengeluaran)
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <p class="card-category mb-1 text-danger">Pagu Akrual</p>
                        <h4 class="card-title text-danger fw-bold mb-0">Rp {{ number_format($totalSisaSebelumSP2D, 0, ',', '.') }}</h4>
                        <!-- <small class="text-danger fw-bold small">Alokasi SP2D</small> -->
                    </div>
                    <div class="col-6">
                        <p class="card-category mb-1 text-warning">Realisasi Akrual</p>
                        <h4 class="card-title text-warning fw-bold mb-0">Rp {{ number_format($totalSebelumSP2D, 0, ',', '.') }}</h4>
                        <!-- <small class="text-warning fw-bold small">Sedang Proses</small> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">

            <select name="mak_id" class="form-select form-select-sm" style="min-width: 320px;">
                <option value="">-- Semua MAK (Akun) --</option>
                @foreach($maks as $m)
                <option value="{{ $m->id }}" @selected((string)$makId===(string)$m->id)>
                    {{ $m->akun?->kode_akun ?? '-' }} - {{ $m->akun?->nama_akun ?? '-' }}
                    | {{ $m->nama_mak ?? '' }}
                </option>
                @endforeach
            </select>

            <input type="number"
                name="tahun"
                class="form-control form-control-sm"
                style="width: 120px;"
                placeholder="Tahun"
                value="{{ $tahun }}">

            <input type="text"
                name="search"
                class="form-control form-control-sm"
                style="width: 280px;"
                placeholder="Cari uraian COA"
                value="{{ $search }}">

            <button type="submit" class="btn btn-sm btn-secondary">
                <i class="fa fa-filter"></i> Filter
            </button>

            <a href="{{ route('master.coa-items.index') }}" class="btn btn-sm btn-light">
                Reset
            </a>
        </form>
    </div>
</div>

@php
/**
* Bangun tree dari $coaItems berdasarkan parent_id.
*/
$grouped = $coaItems->groupBy(fn($x) => $x->parent_id ?? 0);

/**
* Helper Label Kode Hierarchy
*/
$getLabelKode = function($c) {
if ($c->level == 0) return 'COA';
if ($c->level == 1) return 'SUB JUDUL';
if ($c->level == 2) return 'COA';
return $c->kode ?? '-';
};

/**
* Helper untuk badge level.
*/
$levelBadge = function($lvl){
return match((int)$lvl){
0 => '<span class="badge bg-secondary">Lv 0</span>',
1 => '<span class="badge bg-warning text-dark">Lv 1</span>',
2 => '<span class="badge bg-info text-dark">Lv 2</span>',
default => '<span class="badge bg-dark">Lv '.$lvl.'</span>',
};
};

/**
* Row class berdasarkan level
*/
$levelRowClass = function($lvl){
return match((int)$lvl){
1 => 'table-warning',
2 => 'table-info',
default => '',
};
};

/**
* Render tree recursive
*/
$renderTree = function($parentId, $depth = 0) use (&$renderTree, $grouped, $levelBadge, $levelRowClass, $getLabelKode) {
$rows = '';
$children = $grouped[$parentId] ?? collect();

foreach ($children as $c) {
$currentLevel = (int)$c->level;
$indentPx = $currentLevel * 30;
$prefix = $currentLevel > 0 ? '↳ ' : '';

$pagu = (float)($c->jumlah ?? 0);
$terpakai = (float)($c->realisasi_total ?? 0);
$sisa = $pagu - $terpakai;
$kodeTampil = $getLabelKode($c);

$rows .= '<tr class="'.$levelRowClass($c->level).'" data-level="'.$currentLevel.'">';
    $rows .= ' <td class="text-center"></td>';

    // --- BAGIAN YANG DIPERBAIKI: Selalu menampilkan data akun ---
    $rows .= ' <td>';
        $rows .= '<div class="fw-bold">'.e($c->mak?->akun?->kode_akun ?? '-').'</div>';
        $rows .= '<div class="text-muted small">'.e($c->mak?->akun?->nama_akun ?? '-').'</div>';
        $rows .= ' </td>';

    $rows .= ' <td>
        <div class="fw-bold">'.e($c->mak?->nama_mak ?? '-').'</div>
    </td>';

    $rows .= ' <td>
        <div style="padding-left: '.$indentPx.'px">
            <span class="me-2">'.$levelBadge($c->level).'</span>
            <span class="'.($currentLevel > 0 ? 'fw-semibold' : 'fw-bold').'">'.$prefix.e($c->uraian).'</span>
        </div>
    </td>';

    $rows .= ' <td class="text-center fw-bold text-primary">'.e($kodeTampil).'</td>';

    $rows .= ' <td class="text-end">'.number_format((int)($c->volume ?? 0), 0, ',', '.').'</td>';
    $rows .= ' <td class="text-center">'.e($c->satuan ?? '').'</td>';
    $rows .= ' <td class="text-end">'.number_format((float)($c->harga_satuan ?? 0), 0, ',', '.').'</td>';

    $rows .= ' <td class="text-end fw-bold">';
        if ($currentLevel === 1) {
        $rows .= number_format($pagu, 0, ',', '.');
        } else {
        $rows .= ' <a class="text-decoration-none" href="'.route('realisasi-v2.create', ['coa_item_id' => $c->id]).'">
            '.number_format($pagu, 0, ',', '.').'
        </a>';
        }
        $rows .= ' </td>';

    $rows .= ' <td class="text-end text-muted italic">
        '.number_format($terpakai, 0, ',', '.').'
    </td>';

    $colorClass = $sisa < 0 ? 'text-danger' : ($sisa==0 ? 'text-muted' : 'text-success' );
        $rows .=' <td class="text-end fw-bold ' .$colorClass.'">
        '.number_format($sisa, 0, ',', '.').'
        </td>';

        $rows .= ' <td class="text-center">'.e($c->tahun_anggaran ?? '').'</td>';

        $rows .= ' <td>
            <div class="d-flex gap-1">
                <a href="'.route('master.coa-items.realisasi', $c->id).'"
                    class="btn btn-sm btn-info"
                    title="Detail Realisasi">
                    <i class="fas fa-list-ul"></i>
                </a>

                <a href="'.route('master.coa-items.edit', $c).'"
                    class="btn btn-sm btn-primary"
                    title="Edit COA">
                    <i class="fa fa-edit"></i>
                </a>

                <form action="'.route('master.coa-items.destroy', $c).'" method="POST" class="d-inline form-delete">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>';

        $rows .= '</tr>';
$rows .= $renderTree($c->id, $depth + 1);
}
return $rows;
};
@endphp

<div class="card">
    <div class="card-body">
        <div class="alert alert-light border mb-3">
            <div class="fw-bold mb-1">Keterangan level</div>
            <div class="small text-muted">
                Lv 0 = item langsung di bawah MAK • Lv 1 = grouping/parent • Lv 2 = detail/child
            </div>
        </div>

        <div class="table-responsive">
            <table id="dtCoa" class="table table-striped table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Akun</th>
                        <th>MAK</th>
                        <th>Uraian COA</th>
                        <th style="width:120px">Kode</th>
                        <th style="width:50px">Vol</th>
                        <th style="width:50px">Sat</th>
                        <th style="width:120px">Harga Satuan</th>
                        <th style="width:120px">Pagu</th>
                        <th style="width:120px">Realisasi</th>
                        <th style="width:120px">Sisa Anggaran</th>
                        <th style="width:80px">Tahun</th>
                        <th style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {!! $renderTree(0, 0) !!}
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        const dt = $('#dtCoa').DataTable({
            pageLength: 50,
            ordering: false,
            responsive: true,
            drawCallback: function() {
                const api = this.api();
                api.column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }
        });

        $(document).on('submit', '.form-delete', function(e) {
            e.preventDefault();
            const form = this;
            swal({
                title: "Yakin hapus?",
                text: "Data ini tidak bisa dikembalikan.",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Batal",
                        visible: true,
                        className: "btn btn-secondary"
                    },
                    confirm: {
                        text: "Ya, hapus",
                        className: "btn btn-danger"
                    }
                },
                dangerMode: true,
            }).then(function(willDelete) {
                if (willDelete) form.submit();
            });
        });

        const msgSuccess = @json(session('success'));
        const msgError = @json(session('error'));

        if (msgError) swal("Gagal", msgError, "error");
        else if (msgSuccess) swal("Berhasil", msgSuccess, "success");
    });
</script>
@endpush