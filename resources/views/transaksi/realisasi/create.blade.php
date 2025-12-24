@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="page-title mb-1">Tambah Realisasi ini</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home">
                    <a href="#"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Transaksi</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('realisasi.index', ['coa_item_id' => $coaItem->id]) }}">Realisasi</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>Create</span></li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <div class="fw-bold mb-1">Periksa kembali input:</div>
    <ul class="mb-0">
        @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('realisasi.store') }}" method="POST">
@csrf

<input type="hidden" name="coa_item_id" value="{{ $coaItem->id }}">

{{-- INFO COA --}}
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-2">Informasi COA</h6>

        <table class="table table-sm">
            <tr>
                <th width="200">MAK</th>
                <td>{{ $coaItem->mak?->nama_mak }}</td>
            </tr>
            <tr>
                <th>Uraian COA</th>
                <td>{{ $coaItem->uraian }}</td>
            </tr>
            <tr>
                <th>Pagu</th>
                <td class="fw-bold text-primary">
                    Rp {{ number_format($coaItem->jumlah, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
</div>

{{-- HEADER REALISASI --}}
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Header Realisasi</h6>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tahun Anggaran</label>
                <input type="number" name="tahun_anggaran" class="form-control"
                       value="{{ old('tahun_anggaran', date('Y')) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Kode Unik PLO</label>
                <input type="text" name="kode_unik_plo" class="form-control"
                       value="{{ old('kode_unik_plo') }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Sumber Anggaran</label>
                <input type="text" name="sumber_anggaran" class="form-control"
                       value="{{ old('sumber_anggaran') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Nama Kegiatan nih</label>
                <input type="text" name="nama_kegiatan" class="form-control"
                       value="{{ old('nama_kegiatan') }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Akun</label>
                <input type="text" name="akun" class="form-control"
                       value="{{ old('akun') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Bidang</label>
                <input type="text" name="bidang" class="form-control"
                       value="{{ old('bidang') }}">
            </div>
        </div>
    </div>
</div>

{{-- DETAIL REALISASI --}}
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Detail Realisasi</h6>
            <button type="button" id="btnAddLine" class="btn btn-sm btn-secondary">
                <i class="fa fa-plus"></i> Tambah Baris
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="linesTable">
                <thead class="table-light">
                    <tr>
                        <th>Penerima / Penyedia</th>
                        <th>Uraian</th>
                        <th width="140">Jumlah</th>
                        <th width="120">PPN</th>
                        <th width="120">PPh21</th>
                        <th width="120">PPh22</th>
                        <th width="120">PPh23</th>
                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="lines[0][penerima_penyedia]" class="form-control" required></td>
                        <td><input type="text" name="lines[0][uraian]" class="form-control" required></td>
                        <td><input type="number" name="lines[0][jumlah]" class="form-control" min="0" required></td>
                        <td><input type="number" name="lines[0][ppn]" class="form-control" min="0"></td>
                        <td><input type="number" name="lines[0][pph21]" class="form-control" min="0"></td>
                        <td><input type="number" name="lines[0][pph22]" class="form-control" min="0"></td>
                        <td><input type="number" name="lines[0][pph23]" class="form-control" min="0"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btn-remove">&times;</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-end mt-3">
            <a href="{{ route('realisasi.index', ['coa_item_id' => $coaItem->id]) }}" class="btn btn-light">
                Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                Simpan Realisasi
            </button>
        </div>
    </div>
</div>

</form>
@endsection

@push('scripts')
<script>
let lineIndex = 1;

$('#btnAddLine').on('click', function () {
    const row = `
    <tr>
        <td><input type="text" name="lines[${lineIndex}][penerima_penyedia]" class="form-control" required></td>
        <td><input type="text" name="lines[${lineIndex}][uraian]" class="form-control" required></td>
        <td><input type="number" name="lines[${lineIndex}][jumlah]" class="form-control" min="0" required></td>
        <td><input type="number" name="lines[${lineIndex}][ppn]" class="form-control" min="0"></td>
        <td><input type="number" name="lines[${lineIndex}][pph21]" class="form-control" min="0"></td>
        <td><input type="number" name="lines[${lineIndex}][pph22]" class="form-control" min="0"></td>
        <td><input type="number" name="lines[${lineIndex}][pph23]" class="form-control" min="0"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger btn-remove">&times;</button>
        </td>
    </tr>`;
    $('#linesTable tbody').append(row);
    lineIndex++;
});

$(document).on('click', '.btn-remove', function () {
    $(this).closest('tr').remove();
});
</script>
@endpush
