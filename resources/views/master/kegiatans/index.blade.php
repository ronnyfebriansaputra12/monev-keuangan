@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Master Kegiatan</h4>

        <a href="{{ route('master.kegiatans.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah Kegiatan
        </a>
    </div>

    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><span>Kegiatan</span></li>
    </ul>
</div>
@endsection

@section('content')

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">

            <div class="col-md-5">
                <label class="form-label mb-1">Program</label>
                <select name="program_id" class="form-select form-select-sm">
                    <option value="">-- Semua Program --</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" @selected((string)$programId === (string)$p->id)>
                            {{ $p->kode_program }} - {{ $p->nama_program }} ({{ $p->satker?->nama_satker }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label mb-1">Tahun</label>
                <input
                    type="number"
                    name="tahun"
                    class="form-control form-control-sm"
                    placeholder="Tahun"
                    value="{{ $tahun }}"
                    min="2000"
                    max="2100">
            </div>

            <div class="col-md-3">
                <label class="form-label mb-1">Pencarian</label>
                <input
                    type="text"
                    name="search"
                    class="form-control form-control-sm"
                    placeholder="Cari kode / nama"
                    value="{{ $search }}">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">
                    <i class="fa fa-filter"></i> Filter
                </button>
                <a href="{{ route('master.kegiatans.index') }}" class="btn btn-sm btn-light w-100">
                    Reset
                </a>
            </div>

        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="dtKegiatans" class="table table-striped table-bordered table-hover w-100">
                <thead class="thead-light">
                    <tr>
                        <th style="width:60px">No</th>
                        <th>Satker</th>
                        <th>Program</th>
                        <th style="width:160px">Kode Kegiatan</th>
                        <th>Nama Kegiatan</th>
                        <th style="width:100px">Tahun</th>
                        <th style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kegiatans as $i => $k)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $k->program?->satker?->nama_satker }}</td>
                        <td>{{ $k->program?->kode_program }} - {{ $k->program?->nama_program }}</td>
                        <td>{{ $k->kode_kegiatan }}</td>
                        <td>{{ $k->nama_kegiatan }}</td>
                        <td class="text-center">{{ $k->tahun_anggaran }}</td>
                        <td class="text-center">
                            <a href="{{ route('master.kegiatans.edit', $k) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form action="{{ route('master.kegiatans.destroy', $k) }}" method="POST" class="d-inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
  $(function () {
    // DataTables
    $('#dtKegiatans').DataTable({
      pageLength: 25,
      order: [[3, 'asc']],
      responsive: true
    });

    // SweetAlert confirm delete
    $(document).on('submit', '.form-delete', function (e) {
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
        dangerMode: true
      }).then(function (willDelete) {
        if (willDelete) form.submit();
      });
    });

    // Flash messages (session)
    const msgSuccess = @json(session('success'));
    const msgError   = @json(session('error'));
    const msgWarning = @json(session('warning'));
    const msgInfo    = @json(session('info'));

    @if ($errors->any())
      const msgValidation = @json($errors->first());
    @else
      const msgValidation = null;
    @endif

    if (msgValidation) {
      swal({ title: "Validasi gagal", text: msgValidation, icon: "error",
        buttons: { confirm: { text: "OK", className: "btn btn-danger" } }
      });
    } else if (msgError) {
      swal({ title: "Gagal", text: msgError, icon: "error",
        buttons: { confirm: { text: "OK", className: "btn btn-danger" } }
      });
    } else if (msgWarning) {
      swal({ title: "Peringatan", text: msgWarning, icon: "warning",
        buttons: { confirm: { text: "OK", className: "btn btn-warning" } }
      });
    } else if (msgInfo) {
      swal({ title: "Info", text: msgInfo, icon: "info",
        buttons: { confirm: { text: "OK", className: "btn btn-primary" } }
      });
    } else if (msgSuccess) {
      swal({ title: "Berhasil", text: msgSuccess, icon: "success",
        buttons: { confirm: { text: "OK", className: "btn btn-success" } }
      });
    }
  });
</script>
@endpush
@endsection
