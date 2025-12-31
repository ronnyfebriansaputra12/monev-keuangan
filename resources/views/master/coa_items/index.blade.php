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

            <a href="{{ route('master.coa-items.create') }}" class="btn btn-primary btn-round btn-sm">
                <i class="fa fa-plus me-1"></i> Tambah COA (Manual)
            </a>
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
<div class="row mb-3">
    <div class="col-sm-6 col-md-4">
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
                            <h4 class="card-title text-primary">Rp {{ number_format($totalPagu ?? 0, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="card card-stats card-round">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-icon">
                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                            <p class="card-category">Total Realisasi</p>
                            <h4 class="card-title text-secondary">Rp {{ number_format($totalRealisasi ?? 0, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
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
                            <p class="card-category">Sisa Anggaran</p>
                            <h4 class="card-title text-success">Rp {{ number_format($totalSisa ?? 0, 0, ',', '.') }}</h4>
                        </div>
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
* LOGIKA BARU: Jika Lv 0 maka tampilkan COA
*/
$getLabelKode = function($c) {
// Jika Level 0, tampilkan COA sesuai permintaan
if ($c->level == 0) return 'COA';

// Jika level lainnya, tetap gunakan logika hierarchy sebelumnya
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

    // KOLOM AKUN: Menampilkan "COA" untuk item detail/child
    $rows .= ' <td>';
        if($c->level > 0 || $c->catatan_desk == 'COA') {
        $rows .= '<div class="fw-bold text-info">COA</div>';
        } else {
        $rows .= '<div class="fw-bold">'.e($c->mak?->akun?->kode_akun ?? '-').'</div>';
        $rows .= '<div class="text-muted small">'.e($c->mak?->akun?->nama_akun ?? '-').'</div>';
        }
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

    // KOLOM KODE: Sekarang menampilkan "COA" jika baris tersebut adalah Lv 0
    $rows .= ' <td class="text-center fw-bold text-primary">'.e($kodeTampil).'</td>';

    $rows .= ' <td class="text-end">'.number_format((int)($c->volume ?? 0), 0, ',', '.').'</td>';
    $rows .= ' <td class="text-center">'.e($c->satuan ?? '').'</td>';
    $rows .= ' <td class="text-end">'.number_format((float)($c->harga_satuan ?? 0), 0, ',', '.').'</td>';

    $rows .= ' <td class="text-end fw-bold">
        <a class="text-decoration-none" href="'.route('realisasi-v2.create', ['coa_item_id' => $c->id]).'">
            '.number_format($pagu, 0, ',', '.').'
        </a>
    </td>';

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