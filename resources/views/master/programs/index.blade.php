@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Master Program</h4>

        <a href="{{ route('master.programs.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah Program
        </a>
    </div>

    <ul class="breadcrumbs mb-0">
        <li class="nav-home">
            <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><span>Program</span></li>
    </ul>
</div>
@endsection

@section('content')

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Satker</label>
                <select name="satker_id" class="form-select form-select-sm">
                    <option value="">-- Semua Satker --</option>
                    @foreach($satkers as $s)
                        <option value="{{ $s->id }}" @selected((string)$satkerId === (string)$s->id)>
                            {{ $s->nama_satker }}
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

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary">
                    <i class="fa fa-filter"></i> Filter
                </button>
                <a href="{{ route('master.programs.index') }}" class="btn btn-sm btn-light">
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
            <table id="dtPrograms" class="table table-striped table-bordered table-hover w-100">
                <thead class="thead-light">
                    <tr>
                        <th style="width:60px">No</th>
                        <th>Satker</th>
                        <th style="width:160px">Kode Program</th>
                        <th>Nama Program</th>
                        <th style="width:100px">Tahun</th>
                        <th style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programs as $i => $p)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $p->satker?->nama_satker }}</td>
                        <td>{{ $p->kode_program }}</td>
                        <td>{{ $p->nama_program }}</td>
                        <td class="text-center">{{ $p->tahun_anggaran }}</td>
                        <td class="text-center">
                            <a href="{{ route('master.programs.edit', $p) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form action="{{ route('master.programs.destroy', $p) }}" method="POST" class="d-inline form-delete">
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
    $('#dtPrograms').DataTable({
      pageLength: 25,
      order: [[2, 'asc']],
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
