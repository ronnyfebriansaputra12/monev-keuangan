@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="page-title mb-1">COA</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home">
                    <a href="#"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Master Data</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>COA</span></li>
            </ul>
        </div>

        <a href="{{ route('master.coa-items.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah COA (Bulk)
        </a>
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

<div class="card mb-3">
    <div class="card-body">

        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">

            <select name="mak_id" class="form-select form-select-sm" style="min-width: 320px;">
                <option value="">-- Semua MAK (Akun) --</option>
                @foreach($maks as $m)
                    <option value="{{ $m->id }}" @selected((string)$makId === (string)$m->id)>
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
     * Key = parent_id (null -> root), value = array of items.
     */
    $grouped = $coaItems->groupBy(fn($x) => $x->parent_id ?? 0);

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
     * Row class berdasarkan level (biar kebaca)
     */
    $levelRowClass = function($lvl){
        return match((int)$lvl){
            1 => 'table-warning',
            2 => 'table-info',
            default => '',
        };
    };

    /**
     * Render tree recursive (tanpa bikin file partial).
     */
    $renderTree = function($parentId, $depth = 0) use (&$renderTree, $grouped, $levelBadge, $levelRowClass) {
        $rows = '';
        $children = $grouped[$parentId] ?? collect();

        foreach ($children as $c) {
            $indentPx = $depth * 22; // jarak indent
            $prefix = $depth > 0 ? '↳ ' : '';

            $rows .= '<tr class="'.$levelRowClass($c->level).'" data-level="'.(int)$c->level.'">';
            $rows .= '  <td class="text-center"></td>';

            $rows .= '  <td>
                            <div class="fw-bold">'.e($c->mak?->akun?->kode_akun ?? '-').'</div>
                            <div class="text-muted small">'.e($c->mak?->akun?->nama_akun ?? '-').'</div>
                        </td>';

            $rows .= '  <td>
                            <div class="fw-bold">'.e($c->mak?->nama_mak ?? '-').'</div>
                        </td>';

            // uraian + indent + badge
            $rows .= '  <td>
                            <div style="padding-left: '.$indentPx.'px">
                                <span class="me-2">'.$levelBadge($c->level).'</span>
                                <span class="'.((int)$c->level>0?'fw-semibold':'fw-bold').'">'.$prefix.e($c->uraian).'</span>
                            </div>
                        </td>';

            $rows .= '  <td class="text-end">'.number_format((int)$c->volume, 0, ',', '.').'</td>';
            $rows .= '  <td>'.e($c->satuan ?? '').'</td>';
            $rows .= '  <td class="text-end">'.number_format((float)$c->harga_satuan, 0, ',', '.').'</td>';
            $rows .= '  <td class="text-end fw-bold">'.number_format((float)$c->jumlah, 0, ',', '.').'</td>';
            $rows .= '  <td>'.e($c->tahun_anggaran).'</td>';

            // aksi
            $rows .= '  <td>
                            <a href="'.route('master.coa-items.edit', $c).'" class="btn btn-sm btn-primary">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            <form action="'.route('master.coa-items.destroy', $c).'"
                                  method="POST"
                                  class="d-inline form-delete">
                                '.csrf_field().method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>';

            $rows .= '</tr>';

            // render anak
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
                        <th style="width:60px">No</th>
                        <th>Akun</th>
                        <th>MAK</th>
                        <th>Uraian COA</th>
                        <th style="width:90px">Vol</th>
                        <th style="width:90px">Sat</th>
                        <th style="width:160px">Harga Satuan</th>
                        <th style="width:160px">Jumlah</th>
                        <th style="width:90px">Tahun</th>
                        <th style="width:140px">Aksi</th>
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

        // DataTables: jangan sorting, karena hirarki pakai urutan + parent-child
        const dt = $('#dtCoa').DataTable({
            pageLength: 25,
            ordering: false,
            responsive: true,
            drawCallback: function () {
                // nomor urut per page
                const api = this.api();
                api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }
        });

        // SweetAlert confirm delete
        $(document).on('submit', '.form-delete', function(e) {
            e.preventDefault();
            const form = this;

            swal({
                title: "Yakin hapus?",
                text: "Data ini tidak bisa dikembalikan.",
                icon: "warning",
                buttons: {
                    cancel: { text: "Batal", visible: true, className: "btn btn-secondary" },
                    confirm: { text: "Ya, hapus", className: "btn btn-danger" }
                },
                dangerMode: true,
            }).then(function(willDelete) {
                if (willDelete) form.submit();
            });
        });

        // Flash messages
        const msgSuccess = @json(session('success'));
        const msgError   = @json(session('error'));
        const msgWarning = @json(session('warning'));
        const msgInfo    = @json(session('info'));

        @if ($errors->any())
            const msgValidation = @json($errors->first());
        @else
            const msgValidation = null;
        @endif

        if (msgValidation) swal({ title: "Validasi gagal", text: msgValidation, icon: "error" });
        else if (msgError) swal({ title: "Gagal", text: msgError, icon: "error" });
        else if (msgWarning) swal({ title: "Peringatan", text: msgWarning, icon: "warning" });
        else if (msgInfo) swal({ title: "Info", text: msgInfo, icon: "info" });
        else if (msgSuccess) swal({ title: "Berhasil", text: msgSuccess, icon: "success" });
    });
</script>
@endpush
