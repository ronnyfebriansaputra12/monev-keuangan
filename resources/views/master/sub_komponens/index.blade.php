@extends('layouts.index')

{{-- Tambahkan CSS DataTables di head melalui push atau langsung --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .filter-section {
        background: #ffffff;
        border: 1px solid #e3e6f0;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
    }

    .filter-section .form-control-sm, 
    .filter-section .form-select-sm,
    .filter-section .btn-sm {
        height: 38px;
        border-radius: 8px;
    }

    #dtSubKomponen thead th {
        background-color: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        color: #4e73df;
        border-bottom: 2px solid #e3e6f0;
        vertical-align: middle;
    }

    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
    }

    code {
        color: #e74a3b;
        background: #f8f9fc;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
    }

    .text-structure {
        font-size: 0.75rem;
        font-weight: bold;
        color: #858796;
    }
</style>
@endpush

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="page-title mb-1">Master Sub Komponen</h4>
            <ul class="breadcrumbs bg-transparent p-0 m-0 d-flex align-items-center" style="list-style: none; gap: 8px;">
                <li class="nav-home"><a href="#"><i class="fas fa-home text-primary"></i></a></li>
                <li class="separator text-muted"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
                <li class="nav-item"><a href="#" class="text-muted">Master Data</a></li>
                <li class="separator text-muted"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
                <li class="nav-item"><span class="font-weight-bold text-dark">Sub Komponen</span></li>
            </ul>
        </div>
        <a href="{{ route('master.sub-komponens.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Sub Komponen
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Filter Section --}}
<div class="filter-section border-0 shadow-sm">
    <form method="GET" action="{{ route('master.sub-komponens.index') }}">
        <div class="row align-items-end g-3">
            <div class="col-md-4">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Komponen Parent</label>
                <select name="komponen_id" class="form-select form-select-sm shadow-none border-gray-300">
                    <option value="">-- Semua Komponen --</option>
                    @foreach($komponens as $komponen)
                        <option value="{{ $komponen->id }}" @selected((string)$komponenId===(string)$komponen->id)>
                            {{ $komponen->kode_komponen }} - {{ $komponen->nama_komponen }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Tahun</label>
                <input type="number" name="tahun" class="form-control form-control-sm shadow-none border-gray-300" 
                       value="{{ $tahun }}" min="2000" max="2100" placeholder="Tahun">
            </div>

            <div class="col-md-3">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Pencarian Cepat</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 rounded-left-8"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control border-left-0 shadow-none" 
                           placeholder="Cari kode atau nama sub..." value="{{ $search }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-secondary flex-fill shadow-none">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('master.sub-komponens.index') }}" class="btn btn-sm btn-light border flex-fill text-center">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Table Section --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table id="dtSubKomponen" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="width:50px">No</th>
                        <th>Parent (Satker / Komponen)</th>
                        <th class="text-center" style="width:120px">Kode Sub</th>
                        <th>Nama Sub Komponen</th>
                        <th class="text-center" style="width:80px">Tahun</th>
                        <th class="text-center" style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i => $item)
                    <tr>
                        <td class="text-center align-middle text-muted small">{{ $i+1 }}</td>
                        <td class="align-middle">
                            <div class="text-structure text-uppercase">{{ $item->komponen?->rincianOutput?->klasifikasiRo?->kegiatan?->program?->satker?->nama_satker }}</div>
                            <div class="small text-muted">
                                <span class="font-weight-bold text-primary">{{ $item->komponen?->kode_komponen }}</span> - {{ $item->komponen?->nama_komponen }}
                            </div>
                        </td>
                        <td class="text-center align-middle">
                            <code>{{ $item->kode_subkomponen }}</code>
                        </td>
                        <td class="align-middle font-weight-bold text-dark">{{ $item->nama_subkomponen }}</td>
                        <td class="text-center align-middle">
                            <span class="badge badge-info px-3 py-2 rounded-pill shadow-xs">
                                {{ $item->tahun_anggaran }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <div class="btn-group">
                                <a href="{{ route('master.sub-komponens.edit', $item) }}" 
                                   class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('master.sub-komponens.destroy', $item) }}" 
                                      method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function () {
    // ✅ DataTables (Fixed ID from dtKomponen to dtSubKomponen)
    $('#dtSubKomponen').DataTable({
        pageLength: 25,
        order: [[3, 'asc']],
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
        }
    });

    // ✅ SweetAlert Confirm Delete
    $(document).on('submit', '.form-delete', function (e) {
        e.preventDefault();
        const form = this;

        swal({
            title: "Yakin hapus?",
            text: "Data sub komponen ini tidak bisa dikembalikan.",
            icon: "warning",
            buttons: {
                cancel: { text: "Batal", visible: true, className: "btn btn-light border" },
                confirm: { text: "Ya, hapus", className: "btn btn-danger" }
            },
            dangerMode: true
        }).then(ok => { if (ok) form.submit(); });
    });

    // ✅ Notification Logic
    const toast = (title, text, icon, btnClass) => {
        swal({
            title: title,
            text: text,
            icon: icon,
            buttons: { confirm: { text: "OK", className: btnClass } }
        });
    };

    @if(session('success')) toast("Berhasil", @json(session('success')), "success", "btn btn-success"); @endif
    @if(session('error')) toast("Gagal", @json(session('error')), "error", "btn btn-danger"); @endif
    @if($errors->any()) toast("Gagal", @json($errors->first()), "error", "btn btn-danger"); @endif
});
</script>
@endpush
@endsection