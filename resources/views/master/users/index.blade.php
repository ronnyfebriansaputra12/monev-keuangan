@extends('layouts.index')

{{-- Tambahkan CSS khusus untuk UI User --}}
@push('styles')
<style>
    .filter-section {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e3e6f0;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
    }

    /* Merapikan Form Control agar sejajar */
    .filter-section .form-control-sm, 
    .filter-section .form-select-sm,
    .filter-section .btn-sm {
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #f1f3f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #4e73df;
        border: 1px solid #e3e6f0;
    }

    .table-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .role-badge {
        font-size: 0.7rem;
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-admin { background-color: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .badge-user { background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

    /* Custom Scrollbar untuk Table Responsive */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #e3e6f0;
        border-radius: 10px;
    }
</style>
@endpush

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="page-title mb-1">Master User</h4>
            <ul class="breadcrumbs bg-transparent p-0 m-0 d-flex align-items-center" style="list-style: none; gap: 8px;">
                <li class="nav-home"><a href="#"><i class="fas fa-home text-primary"></i></a></li>
                <li class="separator text-muted"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
                <li class="nav-item"><a href="#" class="text-muted">Master Data</a></li>
                <li class="separator text-muted"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></li>
                <li class="nav-item"><span class="font-weight-bold text-dark">User Management</span></li>
            </ul>
        </div>
        <a href="{{ route('master.users.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus-circle mr-2"></i> Tambah User Baru
        </a>
    </div>
</div>
@endsection

@section('content')
{{-- Filter Section --}}
<div class="filter-section border-0 shadow-sm">
    <form method="GET" action="{{ route('master.users.index') }}">
        <div class="row align-items-end g-3">
            <div class="col-md-3">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Filter Hak Akses</label>
                <select name="role" class="form-control form-control-sm shadow-none border-gray-300">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" @selected($role=='admin')>Admin System</option>
                    <option value="user" @selected($role=='user')>User Biasa</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="small font-weight-bold text-uppercase text-muted mb-2 d-block">Pencarian Cepat</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0 rounded-left-8"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control border-left-0 shadow-none" placeholder="Cari Nama, Email, atau Kode PLO..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-secondary flex-fill shadow-none">
                        <i class="fas fa-filter mr-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('master.users.index') }}" class="btn btn-sm btn-light border flex-fill">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Table Section --}}
<div class="card shadow-sm border-0 rounded-12">
    <div class="card-body">
        <div class="table-responsive">
            <table id="dtUsers" class="table table-hover w-100">
                <thead>
                    <tr class="bg-light">
                        <th class="text-center" style="width:50px">No</th>
                        <th>User Profile</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Akses PLO</th>
                        <th class="text-center" style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $u)
                    <tr>
                        <td class="text-center align-middle text-muted small">{{ $i+1 }}</td>
                        <td class="align-middle">
                            <div class="table-user-info">
                                <div class="user-avatar shadow-xs">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark">{{ $u->name }}</div>
                                    <div class="small text-muted">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center align-middle">
                            <span class="role-badge {{ $u->role == 'admin' ? 'badge-admin' : 'badge-user' }}">
                                <i class="fas {{ $u->role == 'admin' ? 'fa-user-shield' : 'fa-user' }}"></i>
                                {{ strtoupper($u->role) }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <code class="px-2 py-1 bg-light rounded text-danger font-weight-bold">{{ $u->plo_code ?? '-' }}</code>
                        </td>
                        <td class="text-center align-middle">
                            <div class="btn-group">
                                {{-- Ikon Edit Disamakan sesuai permintaan --}}
                                <a href="{{ route('master.users.edit', $u) }}" class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Ikon Hapus menggunakan tombol link/outline agar seragam --}}
                                <form action="{{ route('master.users.destroy', $u) }}" method="POST" class="d-inline form-delete">
                                    @csrf @method('DELETE')
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
@endsection

@push('scripts')
<script>
    $(function() {
        // ✅ DataTables Styling
        $('#dtUsers').DataTable({
            pageLength: 25,
            ordering: true,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search within table...",
                paginate: {
                    previous: "<i class='fas fa-angle-left'></i>",
                    next: "<i class='fas fa-angle-right'></i>"
                }
            }
        });

        // ✅ SweetAlert Confirm Delete (Elegant Style)
        $(document).on('submit', '.form-delete', function(e) {
            e.preventDefault();
            const form = this;

            swal({
                title: "Hapus User?",
                text: "Akses user ini akan dicabut secara permanen.",
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

        // ✅ Flash Messages Logic
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
    });
</script>
@endpush