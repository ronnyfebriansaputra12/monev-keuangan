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
    .filter-section .form-select-sm,
    .filter-section .btn-sm {
        height: 38px;
        border-radius: 8px;
    }

    #dtKlasifikasi thead th {
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

    .text-satker {
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
            <h4 class="page-title mb-1">Master Klasifikasi RO</h4>
            <ul class="breadcrumbs bg-transparent p-0 m-0 d-flex align-items-center" style="list-style: none; gap: 8px;">
                <li class="nav-home"><a href="#"><i class="fas fa-home text-primary"></i></a></li>
                <li class="separator text-muted"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
                <li class="nav-item"><a href="#" class="text-muted">Master Data</a></li>
                <li class="separator text-muted"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
                <li class="nav-item"><span class="font-weight-bold text-dark">Klasifikasi RO</span></li>
            </ul>
        </div>
        <a href="{{ route('master.klasifikasi-ros.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Klasifikasi RO
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Filter Section --}}
<div class="filter-section border-0 shadow-sm">
    <form method="GET" action="{{ route('master.klasifikasi-ros.index') }}">
        <div class="row align-items-end g-3">
            <div class="col-md-4">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Kegiatan</label>
                <select name="kegiatan_id" class="form-select form-select-sm shadow-none border-gray-300">
                    <option value="">-- Semua Kegiatan --</option>
                    @foreach($kegiatans as $k)
                        <option value="{{ $k->id }}" @selected((string)$kegiatanId === (string)$k->id)>
                            {{ $k->kode_kegiatan }} - {{ $k->nama_kegiatan }} 
                            ({{ $k->program?->satker?->nama_satker }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Tahun</label>
                <input type="number" name="tahun" class="form-control form-control-sm shadow-none border-gray-300" 
                       placeholder="Tahun" value="{{ $tahun }}">
            </div>
            <div class="col-md-3">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Pencarian Cepat</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 rounded-left-8"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control border-left-0 shadow-none" 
                           placeholder="Cari kode atau nama..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-secondary flex-fill shadow-none">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    <a href="{{ route('master.klasifikasi-ros.index') }}" class="btn btn-sm btn-light border flex-fill text-center">
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
            <table id="dtKlasifikasi" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="width:50px">No</th>
                        <th>Struktur Atas (Satker / Prog / Kgt)</th>
                        <th class="text-center" style="width:100px">Kode KRO</th>
                        <th>Nama Klasifikasi RO</th>
                        <th class="text-center" style="width:80px">Tahun</th>
                        <th class="text-center" style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($klasifikasiRos as $i => $r)
                    <tr>
                        <td class="text-center align-middle text-muted small">{{ $i+1 }}</td>
                        <td class="align-middle">
                            <div class="text-satker text-uppercase">{{ $r->kegiatan?->program?->satker?->nama_satker }}</div>
                            <div class="small text-muted">
                                <span class="badge badge-light border text-dark" style="font-size: 0.65rem;">P: {{ $r->kegiatan?->program?->kode_program }}</span>
                                <span class="badge badge-light border text-dark" style="font-size: 0.65rem;">K: {{ $r->kegiatan?->kode_kegiatan }}</span>
                            </div>
                        </td>
                        <td class="text-center align-middle font-weight-bold text-dark">
                            <code>{{ $r->kode_klasifikasi }}</code>
                        </td>
                        <td class="align-middle font-weight-bold text-dark">{{ $r->nama_klasifikasi }}</td>
                        <td class="text-center align-middle">
                            <span class="badge badge-info px-3 py-2 rounded-pill shadow-xs">
                                {{ $r->tahun_anggaran }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <div class="btn-group">
                                {{-- Ikon Edit: Style Samakan dengan Realisasi --}}
                                <a href="{{ route('master.klasifikasi-ros.edit', $r) }}" 
                                   class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Ikon Hapus: Style Samakan dengan Realisasi --}}
                                <form action="{{ route('master.klasifikasi-ros.destroy', $r) }}" 
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
    // DataTables
    $('#dtKlasifikasi').DataTable({
      pageLength: 25,
      order: [[2, 'asc']],
      responsive: true,
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
      }
    });

    // SweetAlert confirm delete
    $(document).on('submit', '.form-delete', function (e) {
      e.preventDefault();
      const form = this;

      swal({
        title: "Yakin hapus?",
        text: "Data Klasifikasi RO ini tidak bisa dikembalikan.",
        icon: "warning",
        buttons: {
          cancel: { text: "Batal", visible: true, className: "btn btn-light border" },
          confirm: { text: "Ya, hapus", className: "btn btn-danger" }
        },
        dangerMode: true
      }).then(function (willDelete) {
        if (willDelete) form.submit();
      });
    });

    // Notification Logic (Toast)
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