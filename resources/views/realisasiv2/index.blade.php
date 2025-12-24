@extends('layouts.index')

{{-- Tambahkan CSS DataTables di head melalui push atau langsung --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .filter-box {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="m-0 font-weight-bold text-primary">List Realisasi: {{ $selectedCoa->uraian ?? 'Semua Data' }}</h6>
                        <small class="text-muted">Tahun Anggaran: {{ $selectedCoa->tahun_anggaran ?? '-' }}</small>
                    </div>
                    <div>
                        @if($coaItemId)
                        <a href="{{ route('realisasi-v2.create', ['coa_item_id' => $coaItemId]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Realisasi
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">

                    {{-- Alert Statistik --}}
                    @if($selectedCoa)
                    <div class="alert alert-info border-left-info shadow-sm mb-4">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <small class="d-block text-uppercase fw-bold">Total Pagu:</small>
                                <h5 class="fw-bold mb-0">Rp {{ number_format($selectedCoa->jumlah, 0, ',', '.') }}</h5>
                            </div>
                            <div class="col-md-4 text-center border-left">
                                <small class="d-block text-uppercase fw-bold text-danger">Total Realisasi:</small>
                                <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($selectedCoa->realisasi_total, 0, ',', '.') }}</h5>
                            </div>
                            <div class="col-md-4 text-center border-left">
                                <small class="d-block text-uppercase fw-bold text-success">Sisa Pagu:</small>
                                <h5 class="fw-bold mb-0 text-success">Rp {{ number_format(($selectedCoa->jumlah - $selectedCoa->realisasi_total), 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Form Filter --}}
                    <div class="filter-box">
                        <form action="{{ route('realisasi-v2.index') }}" method="GET" class="row g-2 align-items-end">
                            <input type="hidden" name="coa_item_id" value="{{ $coaItemId }}">

                            <div class="col-md-4">
                                <label class="small fw-bold">Pencarian Global</label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari PLO / Kegiatan / Penyedia..." value="{{ $search }}">
                            </div>

                            <div class="col-md-3">
                                <label class="small fw-bold">Status Berkas</label>
                                <select name="status_berkas" class="form-select form-select-sm">
                                    <option value="">-- Semua Status --</option>
                                    <option value="Proses" {{ request('status_berkas') == 'Proses' ? 'selected' : '' }}>Proses</option>
                                    <option value="Selesai" {{ request('status_berkas') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="Ditolak/Revisi" {{ request('status_berkas') == 'Ditolak/Revisi' ? 'selected' : '' }}>Ditolak/Revisi</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <button class="btn btn-primary btn-sm px-3" type="submit">
                                    <i class="fas fa-filter fa-sm"></i> Terapkan Filter
                                </button>
                                <a href="{{ route('realisasi-v2.index', ['coa_item_id' => $coaItemId]) }}" class="btn btn-light btn-sm border">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="realisasiTable" class="table table-bordered table-hover small w-100">
                            <thead class="bg-light text-center">
                                <tr>
                                    <th width="180px">Struktur Anggaran</th>
                                    <th width="80px">Kode PLO</th>
                                    <th width="50px">No Urut</th>
                                    <th>Tgl Kuitansi</th>
                                    <th>Penerima</th>
                                    <th>Uraian</th>
                                    <th>Bruto</th>
                                    <th>PPh</th>
                                    <th>Bersih</th>
                                    <th>Status</th>
                                    <th width="70px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                @php
                                $coa = $item->coaItem;
                                $subKomp = $coa?->subKomponen;
                                $komp = $subKomp?->komponen;
                                $ro = $komp?->rincianOutput;
                                $kro = $ro?->klasifikasiRo;
                                $kgt = $kro?->kegiatan;
                                $mak = $coa?->mak;
                                @endphp
                                <tr @if($item->status_berkas == 'Ditolak/Revisi') class="table-warning" @endif>
                                    <td>
                                        <div class="small lh-sm">
                                            <strong>Kgt:</strong> {{ $kgt?->kode_kegiatan ?? '-' }}<br>
                                            <strong>KRO:</strong> {{ $kro?->kode_klasifikasi_ro ?? '-' }}<br>
                                            <strong>MAK:</strong> {{ $mak?->nama_mak ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-primary">{{ $item->kode_unik_plo }}</td>
                                    <td class="text-center">{{ str_pad($item->no_urut, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="text-center">{{ $item->tgl_kuitansi->format('d/m/Y') }}</td>
                                    <td>{{ $item->penerima_penyedia }}</td>
                                    <td>{{ Str::limit($item->uraian, 40) }}</td>
                                    <td class="text-end fw-bold">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td class="text-end text-danger">{{ number_format((float)$item->pph_total, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format((float)$item->total_bersih, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @php
                                        $badgeColor = match($item->status_berkas) {
                                        'Selesai' => 'success',
                                        'Ditolak/Revisi' => 'danger',
                                        default => 'warning'
                                        };
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }} shadow-sm">{{ $item->status_berkas }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('realisasi-v2.edit', $item->id) }}" class="btn btn-white btn-sm border shadow-sm" title="Edit">
                                                <i class="fas fa-edit text-primary"></i>
                                            </a>
                                            <a href="{{ route('realisasi-v2.show', $item->id) }}" class="btn btn-white btn-sm border shadow-sm" title="Detail">
                                                <i class="fas fa-eye text-info"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script DataTables Online --}}
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#realisasiTable').DataTable({
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            },
            "columnDefs": [{
                    "orderable": false,
                    "targets": [0, 10]
                } // Nonaktifkan sortir di kolom struktur & aksi
            ],
            "order": [
                [2, 'desc']
            ] // Default sortir No Urut terbaru
        });
    });
</script>
@endpush