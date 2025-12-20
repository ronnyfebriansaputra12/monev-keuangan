@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <h4 class="page-title mb-0">Master Akun</h4>
        <a href="{{ route('master.master-akuns.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah Akun
        </a>
    </div>

    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><span>Master Akun</span></li>
    </ul>
</div>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div></div>

    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <input type="text"
               name="search"
               class="form-control form-control-sm"
               placeholder="Cari kode / nama akun"
               value="{{ $search }}"
               style="width:240px">

        <button type="submit" class="btn btn-sm btn-secondary">
            <i class="fa fa-filter"></i> Filter
        </button>

        <a href="{{ route('master.master-akuns.index') }}" class="btn btn-sm btn-light">
            Reset
        </a>
    </form>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="dtMasterAkuns" class="table table-striped table-bordered table-hover w-100">
                <thead class="thead-light">
                    <tr>
                        <th style="width:60px">No</th>
                        <th style="width:140px">Kode Akun</th>
                        <th>Nama Akun</th>
                        <th style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i => $a)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $a->kode_akun }}</td>
                        <td>{{ $a->nama_akun }}</td>
                        <td class="text-center">
                            <a href="{{ route('master.master-akuns.edit', $a) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form action="{{ route('master.master-akuns.destroy', $a) }}"
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
        // DataTables
        $('#dtMasterAkuns').DataTable({
            pageLength: 25,
            order: [[1, 'asc']],
            responsive: true
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

        // Flash message
        const msgSuccess = @json(session('success'));
        const msgError   = @json(session('error'));
        const msgWarning = @json(session('warning'));
        const msgInfo    = @json(session('info'));

        if (msgError) swal({ title: "Gagal", text: msgError, icon: "error" });
        else if (msgWarning) swal({ title: "Peringatan", text: msgWarning, icon: "warning" });
        else if (msgInfo) swal({ title: "Info", text: msgInfo, icon: "info" });
        else if (msgSuccess) swal({ title: "Berhasil", text: msgSuccess, icon: "success" });
    });
</script>
@endpush
@endsection
