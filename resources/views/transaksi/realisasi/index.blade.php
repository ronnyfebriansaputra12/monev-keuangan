@extends('layouts.index')

@section('content')

@php
// ===== SIMULASI ROLE (TANPA LOGIN) =====
$roleSimulasi = 'BENDAHARA'; // ADMIN | PLO | PPK | BENDAHARA
@endphp

<div class="container-fluid">

    {{-- ===== Ringkasan COA ===== --}}
    @if($coaItem)
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-2">{{ $coaItem->uraian }}</h5>

            <div class="row">
                <div class="col-md-3">
                    <small>Pagu</small>
                    <div class="fw-bold">
                        Rp {{ number_format($coaItem->pagu, 0, ',', '.') }}
                    </div>
                </div>

                <div class="col-md-3">
                    <small>Total Realisasi</small>
                    <div class="fw-bold text-success">
                        Rp {{ number_format($coaItem->realisasi_total, 0, ',', '.') }}
                    </div>
                </div>

                <div class="col-md-3">
                    <small>Sisa</small>
                    <div class="fw-bold text-danger">
                        Rp {{ number_format($coaItem->sisa_realisasi, 0, ',', '.') }}
                    </div>
                </div>

                <div class="col-md-3 text-end">
                    <!-- {{-- PLO / ADMIN boleh tambah realisasi --}} -->
                    <!-- @if(in_array($roleSimulasi, ['ADMIN','PLO']) && $coaItem->sisa_realisasi > 0) -->
                    <!-- <a href="{{ route('realisasi.create', ['coa_item_id' => $coaItem->id]) }}"
                           class="btn btn-primary mt-3">
                            + Tambah Realisasi
                        </a> -->
                    <!-- @endif -->
                    @if($coaItem && $coaItem->sisa_realisasi > 0)
                    <a href="{{ route('realisasi.create', ['coa_item_id' => $coaItem->id]) }}"
                        class="btn btn-primary mt-3">
                        + Tambah Realisasi
                    </a>
                    @else
                    <span class="text-muted fst-italic">
                        Anggaran telah habis
                    </span>
                    @endif

                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== Table Realisasi ===== --}}
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Kode PLO</th>
                        <th>Nama Kegiatan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($headers as $row)
                    <tr>
                        <td>{{ $row->kode_unik_plo }}</td>
                        <td>{{ $row->nama_kegiatan }}</td>
                        <td>
                            Rp {{ number_format($row->total, 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $row->status_flow }}
                            </span>
                        </td>
                        <td class="text-nowrap">

                            {{-- Semua role boleh lihat detail --}}
                            <a href="{{ route('realisasi.show', $row->id) }}"
                                class="btn btn-sm btn-info">
                                Detail
                            </a>

                            {{-- BENDAHARA boleh finalisasi --}}
                            <!-- @if($roleSimulasi === 'BENDAHARA'
                                && $row->status_flow !== 'FINAL_BENDAHARA') -->
                                
                            <form action="{{ route('realisasi.finalize', $row->id) }}"
                                method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success"
                                    onclick="return confirm('Finalisasi realisasi ini?')">
                                    Finalize
                                </button>
                            </form>
                            <!-- @endif -->

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Belum ada realisasi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $headers->links() }}
        </div>
    </div>

</div>
@endsection