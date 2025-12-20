@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <h4 class="page-title mb-0">Master Satker</h4>
    </div>

    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#"><i class="icon-home"></i></a>
        </li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><span>Satker</span></li>
    </ul>
</div>
@endsection

@section('content')

{{-- Action Bar --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <a href="{{ route('master.satkers.create') }}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Tambah Satker
    </a>

    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <input
            type="number"
            name="tahun"
            class="form-control form-control-sm"
            placeholder="Tahun"
            value="{{ $tahun }}"
            style="width:120px">

        <input
            type="text"
            name="search"
            class="form-control form-control-sm"
            placeholder="Cari kode / nama"
            value="{{ $search }}"
            style="width:200px">

        <button type="submit" class="btn btn-sm btn-secondary">
            <i class="fa fa-filter"></i> Filter
        </button>

        <a href="{{ route('master.satkers.index') }}" class="btn btn-sm btn-light">
            Reset
        </a>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="dtSatkers" class="table table-striped table-bordered table-hover w-100">
                <thead class="thead-light">
                    <tr>
                        <th style="width:60px">No</th>
                        <th>Kode Satker</th>
                        <th>Nama Satker</th>
                        <th style="width:100px">Tahun</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($satkers as $i => $s)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $s->kode_satker }}</td>
                        <td>{{ $s->nama_satker }}</td>
                        <td class="text-center">{{ $s->tahun_anggaran }}</td>
                        <td class="text-center">
                            <a href="{{ route('master.satkers.edit', $s) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            {{-- ✅ HAPUS pakai SweetAlert (tanpa confirm) --}}
                            <form
                                action="{{ route('master.satkers.destroy', $s) }}"
                                method="POST"
                                class="d-inline form-delete">
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
    $(function() {

        // ✅ DataTables
        $('#dtSatkers').DataTable({
            pageLength: 25,
            order: [
                [2, 'asc']
            ],
            responsive: true
        });

        // ✅ SweetAlert confirm delete
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

        // ✅ Flash messages (session)
        const msgSuccess = @json(session('success'));
        const msgError = @json(session('error'));
        const msgWarning = @json(session('warning'));
        const msgInfo = @json(session('info'));

        // ✅ Validasi error
        @if($errors->any())
        const msgValidation = @json($errors->first());
        @else
        const msgValidation = null;
        @endif

        // ✅ Prioritas (biar gak dobel popup)
        if (msgValidation) {
            swal({
                title: "Validasi gagal",
                text: msgValidation,
                icon: "error",
                buttons: {
                    confirm: {
                        text: "OK",
                        className: "btn btn-danger"
                    }
                }
            });
        } else if (msgError) {
            swal({
                title: "Gagal",
                text: msgError,
                icon: "error",
                buttons: {
                    confirm: {
                        text: "OK",
                        className: "btn btn-danger"
                    }
                }
            });
        } else if (msgWarning) {
            swal({
                title: "Peringatan",
                text: msgWarning,
                icon: "warning",
                buttons: {
                    confirm: {
                        text: "OK",
                        className: "btn btn-warning"
                    }
                }
            });
        } else if (msgInfo) {
            swal({
                title: "Info",
                text: msgInfo,
                icon: "info",
                buttons: {
                    confirm: {
                        text: "OK",
                        className: "btn btn-primary"
                    }
                }
            });
        } else if (msgSuccess) {
            swal({
                title: "Berhasil",
                text: msgSuccess,
                icon: "success",
                buttons: {
                    confirm: {
                        text: "OK",
                        className: "btn btn-success"
                    }
                }
            });
        }

    });
</script>
@endpush


@endsection