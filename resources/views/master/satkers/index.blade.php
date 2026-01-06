@extends('layouts.index')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .filter-section {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
    }

    .filter-section .form-control-sm, 
    .filter-section .btn-sm {
        height: 38px;
        border-radius: 8px;
    }

    #dtSatkers thead th {
        background-color: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #4e73df;
        border-bottom: 2px solid #e3e6f0;
    }

    .card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
    }
</style>
@endpush

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="page-title mb-1">Master Satker</h4>
            <ul class="breadcrumbs bg-transparent p-0 m-0 d-flex align-items-center" style="list-style: none; gap: 8px;">
                <li class="nav-home"><a href="#"><i class="fas fa-home text-primary"></i></a></li>
                <li class="separator text-muted"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
                <li class="nav-item"><a href="#" class="text-muted">Master Data</a></li>
                <li class="separator text-muted"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
                <li class="nav-item"><span class="font-weight-bold text-dark">Satker</span></li>
            </ul>
        </div>
        <a href="{{ route('master.satkers.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Satker
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Filter Section --}}
<div class="filter-section border-0 shadow-sm">
    <form method="GET" action="{{ route('master.satkers.index') }}">
        <div class="row align-items-end g-3">
            <div class="col-md-2">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Tahun</label>
                <input type="number" name="tahun" class="form-control form-control-sm shadow-none border-gray-300" 
                       placeholder="Tahun" value="{{ $tahun }}">
            </div>
            <div class="col-md-6">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Cari Satker</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 rounded-left-8"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control border-left-0 shadow-none" 
                           placeholder="Masukkan kode atau nama satker..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-secondary flex-fill shadow-none">
                        <i class="fas fa-filter mr-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('master.satkers.index') }}" class="btn btn-sm btn-light border flex-fill">
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
            <table id="dtSatkers" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="width:60px">No</th>
                        <th class="text-center">Kode Satker</th>
                        <th>Nama Satker</th>
                        <th class="text-center" style="width:100px">Tahun</th>
                        <th class="text-center" style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($satkers as $i => $s)
                    <tr>
                        <td class="text-center align-middle text-muted small">{{ $i + 1 }}</td>
                        <td class="text-center align-middle font-weight-bold text-dark">{{ $s->kode_satker }}</td>
                        <td class="align-middle font-weight-bold">{{ $s->nama_satker }}</td>
                        <td class="text-center align-middle">
                            <span class="badge badge-info px-3 py-2 rounded-pill shadow-xs">
                                {{ $s->tahun_anggaran }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <div class="btn-group">
                                {{-- Ikon Edit: Style Samakan --}}
                                <a href="{{ route('master.satkers.edit', $s) }}" 
                                   class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Ikon Hapus: Style Samakan --}}
                                <form action="{{ route('master.satkers.destroy', $s) }}" 
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
    $(function() {
        // ✅ DataTables
        $('#dtSatkers').DataTable({
            pageLength: 25,
            order: [[2, 'asc']],
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari di tabel...",
                paginate: {
                    previous: "<i class='fas fa-angle-left'></i>",
                    next: "<i class='fas fa-angle-right'></i>"
                }
            }
        });

        // ✅ SweetAlert confirm delete
        $(document).on('submit', '.form-delete', function(e) {
            e.preventDefault();
            const form = this;

            swal({
                title: "Hapus Satker?",
                text: "Data ini tidak bisa dikembalikan.",
                icon: "warning",
                buttons: {
                    cancel: { text: "Batal", visible: true, className: "btn btn-light border" },
                    confirm: { text: "Ya, Hapus!", className: "btn btn-danger" }
                },
                dangerMode: true,
            }).then(function(willDelete) {
                if (willDelete) form.submit();
            });
        });

        // ✅ Flash messages
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