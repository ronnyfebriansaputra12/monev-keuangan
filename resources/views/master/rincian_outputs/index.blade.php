@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Master Rincian Output (RO)</h4>

        <a href="{{ route('master.rincian-outputs.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah Rincian Output
        </a>
    </div>

    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><span>Rincian Output</span></li>
    </ul>
</div>
@endsection

@section('content')

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">

            <div class="col-md-5">
                <label class="form-label mb-1">Klasifikasi RO</label>
                <select name="klasifikasi_ro_id" class="form-select form-select-sm">
                    <option value="">-- Semua Klasifikasi RO --</option>
                    @foreach($klasifikasiRos as $r)
                        <option value="{{ $r->id }}" @selected((string)$klasifikasiRoId === (string)$r->id)>
                            {{ $r->kode_klasifikasi }} - {{ $r->nama_klasifikasi }}
                            ({{ $r->kegiatan?->program?->satker?->nama_satker }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label mb-1">Tahun</label>
                <input type="number" name="tahun"
                       class="form-control form-control-sm"
                       placeholder="Tahun"
                       value="{{ $tahun }}"
                       min="2000" max="2100">
            </div>

            <div class="col-md-3">
                <label class="form-label mb-1">Pencarian</label>
                <input type="text" name="search"
                       class="form-control form-control-sm"
                       placeholder="Cari kode / nama"
                       value="{{ $search }}">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary w-100">
                    <i class="fa fa-filter"></i> Filter
                </button>
                <a href="{{ route('master.rincian-outputs.index') }}"
                   class="btn btn-sm btn-light w-100">
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
            <table id="dtRO" class="table table-striped table-bordered table-hover w-100">
                <thead class="thead-light">
                    <tr>
                        <th style="width:60px">No</th>
                        <th>Satker</th>
                        <th style="width:140px">Klasifikasi</th>
                        <th style="width:140px">Kode RO</th>
                        <th>Nama RO</th>
                        <th style="width:100px">Tahun</th>
                        <th style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rincianOutputs as $i => $ro)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $ro->klasifikasiRo?->kegiatan?->program?->satker?->nama_satker }}</td>
                        <td>{{ $ro->klasifikasiRo?->kode_klasifikasi }}</td>
                        <td>{{ $ro->kode_ro }}</td>
                        <td>{{ $ro->nama_ro }}</td>
                        <td class="text-center">{{ $ro->tahun_anggaran }}</td>
                        <td class="text-center">
                            <a href="{{ route('master.rincian-outputs.edit', $ro) }}"
                               class="btn btn-sm btn-warning">Edit</a>

                            <form action="{{ route('master.rincian-outputs.destroy', $ro) }}"
                                  method="POST"
                                  class="d-inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
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
    $('#dtRO').DataTable({
        pageLength: 25,
        order: [[3, 'asc']],
        responsive: true
    });

    // SweetAlert delete
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
        }).then((ok) => {
            if (ok) form.submit();
        });
    });

    const msgSuccess = @json(session('success'));
    @if($errors->any())
        const msgValidation = @json($errors->first());
    @else
        const msgValidation = null;
    @endif

    if (msgValidation) {
        swal("Validasi gagal", msgValidation, "error");
    } else if (msgSuccess) {
        swal("Berhasil", msgSuccess, "success");
    }
});
</script>
@endpush
@endsection
