@extends('layouts.index')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@section('page-header')
<div class="page-header mb-4">
    <div>
        <h4 class="page-title mb-1">Detail Realisasi COA</h4>
        <ul class="breadcrumbs mb-0">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('master.coa-items.index') }}">COA Items</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><span>Detail Realisasi</span></li>
        </ul>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    {{-- Ringkasan & Hierarchy --}}
    <div class="col-md-4">
        {{-- Card Hierarchy Anggaran --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-secondary small uppercase">
                    <i class="fas fa-sitemap me-1"></i> Hierarchy Anggaran
                </h6>
            </div>
            <div class="card-body small p-3">
                @php
                // Penelusuran silsilah ke atas berdasarkan relasi model
                $sub = $coaItem->subKomponen;
                $komp = $sub?->komponen;
                $ro = $komp?->rincianOutput;
                $kro = $ro?->klasifikasiRo;
                $keg = $kro?->kegiatan;
                $prog = $keg?->program;
                $satker = $prog?->satker;
                @endphp

                <div class="mb-2">
                    <label class="text-muted d-block mb-0 x-small">Satker:</label>
                    <span class="fw-bold text-dark">{{ $satker?->nama_satker ?? 'Data Tidak Ditemukan' }}</span>
                </div>

                <div class="mb-2 border-top pt-1">
                    <label class="text-muted d-block mb-0 x-small">Program:</label>
                    <span class="text-dark">({{ $prog->kode_program ?? '' }}) {{ $prog->nama_program ?? '-' }}</span>
                </div>

                <div class="mb-2 border-top pt-1">
                    <label class="text-muted d-block mb-0 x-small">Kegiatan:</label>
                    <span class="text-dark">({{ $keg->kode_kegiatan ?? '' }}) {{ $keg->nama_kegiatan ?? '-' }}</span>
                </div>

                {{-- Klasifikasi RO (KRO) --}}
                <div class="mb-2 border-top pt-1">
                    <label class="text-muted d-block mb-0 x-small">Klasifikasi RO (KRO):</label>
                    <span class="text-dark">({{ $kro->kode_klasifikasi ?? '' }}) {{ $kro->nama_klasifikasi ?? '-' }}</span>
                </div>

                {{-- Rincian Output (RO) --}}
                <div class="mb-2 border-top pt-1">
                    <label class="text-muted d-block mb-0 x-small">Rincian Output (RO):</label>
                    <span class="text-dark">({{ $ro->kode_ro ?? '' }}) {{ $ro->nama_ro ?? '-' }}</span>
                </div>

                <div class="mb-2 border-top pt-1">
                    <label class="text-muted d-block mb-0 x-small text-dark">Komponen / Sub:</label>
                    <span class="text-dark">{{ $komp?->nama_komponen ?? '-' }} / {{ $sub?->nama_sub_komponen ?? '-' }}</span>
                </div>

                <div class="mb-2 border-top pt-1 text-primary">
                    <label class="text-muted d-block mb-0 x-small text-dark">COA Item (Current):</label>
                    <span class="fw-bold">{{ $coaItem->uraian ?? '-' }}</span>
                </div>

                <div class="mb-1 border-top pt-1">
                    <label class="text-muted d-block mb-0 x-small">MAK (Belanja):</label>
                    <span class="badge bg-dark text-white">{{ $coaItem->mak?->nama_mak ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Card Informasi Saldo/Pagu --}}
        <div class="card card-round">
            <div class="card-header">
                <div class="card-title">Informasi Saldo</div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Pagu:</span>
                    <span class="fw-bold text-primary">Rp {{ number_format($coaItem->pagu, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Realisasi:</span>
                    <span class="fw-bold text-danger">Rp {{ number_format($coaItem->realisasi_total, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2">
                    <span class="fw-bold">Sisa Anggaran:</span>
                    <span class="fw-bold text-success">Rp {{ number_format($coaItem->sisa_realisasi, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('master.coa-items.index') }}" class="btn btn-sm btn-outline-secondary w-100">Kembali</a>
            </div>
        </div>
    </div>

    {{-- Tabel Rincian Realisasi --}}
    <div class="col-md-8">
        <div class="card card-round shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                <div>
                    <h4 class="card-title mb-0">Riwayat Penggunaan Dana</h4>
                    <p class="text-muted small mb-0">Daftar transaksi berdasarkan kuitansi yang telah diinput</p>
                </div>
                <a href="{{ route('realisasi-v2.create', ['coa_item_id' => $coaItem->id]) }}" class="btn btn-primary btn-sm btn-round">
                    <i class="fa fa-plus me-1"></i> Tambah Realisasi
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dtRealisasi" class="table table-hover table-head-bg-primary w-100">
                        <thead>
                            <tr>
                                <th style="width: 120px">Tgl Kuitansi</th>
                                <th>No. Kuitansi</th>
                                <th>Uraian Kegiatan</th>
                                <th>Penerima / Penyedia</th>
                                <th class="text-end">Jumlah (Rp)</th>
                                <th class="text-center" style="width: 100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalHalaman = 0; @endphp
                            @forelse($realisasis as $r)
                            @php $totalHalaman += $r->jumlah; @endphp
                            <tr>
                                <td class="text-nowrap">
                                    <span class="text-uppercase small fw-bold text-muted">{{ $r->tgl_kuitansi?->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-count text-primary border border-primary fw-bold">
                                        {{ $r->nomor_kuitansi }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-wrap" style="min-width: 200px;">
                                        {{ $r->uraian }}
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-user-tie me-1 text-muted small"></i> {{ $r->penerima_penyedia }}
                                </td>
                                <td class="text-end fw-bold">
                                    {{ number_format($r->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="form-button-action">
                                        <a href="#" class="btn btn-link btn-info p-2" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        {{-- Tambahkan route edit jika sudah ada --}}
                                        {{-- <a href="{{ route('realisasi.edit', $r->id) }}" class="btn btn-link btn-primary p-2" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fa fa-edit"></i>
                                        </a> --}}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block op-3"></i>
                                    Belum ada data realisasi untuk item COA ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($realisasis->count() > 0)
                        <tfoot>
                            <tr class="bg-light fw-bold">
                                <td colspan="4" class="text-end text-uppercase">Total Akumulasi di Tabel:</td>
                                <td class="text-end text-primary">
                                    Rp {{ number_format($totalHalaman, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dtRealisasi').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" // Terjemahan Bahasa Indonesia
            },
            "order": [[ 0, "desc" ]], // Urutkan berdasarkan tanggal (kolom pertama) terbaru
            "columnDefs": [
                { "orderable": false, "targets": 5 } // Matikan sorting untuk kolom "Aksi"
            ],
            "pageLength": 10,
            "responsive": true
        });
    });
</script>
@endpush