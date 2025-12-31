@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <h4 class="page-title mb-0">Master User</h4>
        <a href="{{ route('master.users.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah User
        </a>
    </div>
    <ul class="breadcrumbs mb-0">
        <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="#">Master Data</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><span>User</span></li>
    </ul>
</div>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1">Role</label>
                <select name="role" class="form-select form-select-sm">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" @selected($role=='admin' )>Admin</option>
                    <option value="user" @selected($role=='user' )>User</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1">Pencarian</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, email, atau PLO" value="{{ $search }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-secondary"><i class="fa fa-filter"></i> Filter</button>
                <a href="{{ route('master.users.index') }}" class="btn btn-sm btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="dtUsers" class="table table-striped table-bordered table-hover w-100">
                <thead class="thead-light">
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>PLO Code</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $u)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td class="text-center"><span class="badge bg-info">{{ strtoupper($u->role) }}</span></td>
                        <td>{{ $u->plo_code ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('master.users.edit', $u) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('master.users.destroy', $u) }}" method="POST" class="d-inline form-delete">
                                @csrf @method('DELETE')
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
@endsection
@push('scripts')
<script>
    $(function() {

        // ✅ DataTables
        $('#dtUsers').DataTable({
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