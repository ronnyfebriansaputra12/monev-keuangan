@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Detail Transaksi Realisasi
                    </h6>
                    <span class="badge {{ $realisasi->status_berkas == 'Selesai' ? 'bg-success' : ($realisasi->status_berkas == 'Ditolak/Revisi' ? 'bg-danger' : 'bg-warning') }} p-2 shadow-sm">
                        <i class="fas fa-info-circle me-1"></i> {{ $realisasi->status_berkas }}
                    </span>
                </div>
                <div class="card-body text-dark">
                    <div class="row mb-4">
                        <div class="col-6">
                            <small class="text-muted d-block">Nomor Urut PLO:</small>
                            <h5 class="fw-bold text-primary">{{ $realisasi->no_urut }}</h5>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block">Tanggal Kuitansi:</small>
                            <h5 class="fw-bold">{{ $realisasi->tgl_kuitansi ? $realisasi->tgl_kuitansi->format('d F Y') : '-' }}</h5>
                        </div>
                    </div>

                    <table class="table table-sm table-borderless mb-4">
                        <tr>
                            <td width="35%" class="text-muted small fw-bold">ID Transaksi (PLO)</td>
                            <td width="5%">:</td>
                            <td class="fw-bold">{{ $realisasi->kode_unik_plo }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-bold">Sumber Anggaran</td>
                            <td>:</td>
                            <td class="fw-bold">
                                <span class="badge bg-dark">{{ $realisasi->sumber_anggaran == 'GUP' ? 'DIPA' : $realisasi->sumber_anggaran }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-bold">Jenis Realisasi</td>
                            <td>:</td>
                            <td>
                                @if($realisasi->jenis_realisasi)
                                <span class="badge border border-success text-success fw-bold px-3">{{ $realisasi->jenis_realisasi }}</span>
                                @else
                                <span class="text-muted small italic">- Belum ditentukan -</span>
                                @endif
                            </td>
                        </tr>

                        {{-- DETAIL SPP --}}
                        @if($realisasi->no_spp || $realisasi->tgl_spp)
                        <tr class="bg-light">
                            <td class="text-success small fw-bold">Nomor SPP</td>
                            <td class="text-success">:</td>
                            <td class="fw-bold text-success">{{ $realisasi->no_spp ?? '-' }}</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="text-success small fw-bold">Tanggal SPP</td>
                            <td class="text-success">:</td>
                            <td class="fw-bold text-success">{{ $realisasi->tgl_spp ? $realisasi->tgl_spp->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endif

                        {{-- NEW: DETAIL SPM & FAKTUR PAJAK (Inputan PPSPM) --}}
                        @if($realisasi->no_spm || $realisasi->tgl_spm)
                        <tr style="background-color: #f8f9fc;">
                            <td class="text-primary small fw-bold">Nomor SPM</td>
                            <td class="text-primary">:</td>
                            <td class="fw-bold text-primary">{{ $realisasi->no_spm ?? '-' }}</td>
                        </tr>
                        <tr style="background-color: #f8f9fc;">
                            <td class="text-primary small fw-bold">Tanggal SPM</td>
                            <td class="text-primary">:</td>
                            <td class="fw-bold text-primary">{{ $realisasi->tgl_spm ? $realisasi->tgl_spm->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endif

                        @if($realisasi->no_faktur_pajak || $realisasi->tgl_faktur_pajak)
                        <tr style="background-color: #fff9f0;">
                            <td class="text-warning small fw-bold">Nomor Faktur Pajak</td>
                            <td class="text-warning">:</td>
                            <td class="fw-bold text-dark">{{ $realisasi->no_faktur_pajak ?? '-' }}</td>
                        </tr>
                        <tr style="background-color: #fff9f0;">
                            <td class="text-warning small fw-bold">Tanggal Faktur Pajak</td>
                            <td class="text-warning">:</td>
                            <td class="fw-bold text-dark">{{ $realisasi->tgl_faktur_pajak ? $realisasi->tgl_faktur_pajak->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endif

                        <tr>
                            <td class="text-muted small fw-bold">Penerima / Penyedia</td>
                            <td>:</td>
                            <td class="fw-bold">{{ $realisasi->penerima_penyedia }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-bold">Uraian Kegiatan</td>
                            <td>:</td>
                            <td>{{ $realisasi->uraian }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-bold">NPWP</td>
                            <td>:</td>
                            <td><code class="text-dark fw-bold">{{ $realisasi->npwp ?? '-' }}</code></td>
                        </tr>

                        {{-- BARIS BARU: STATUS SP2D --}}
                        <tr class="border-top">
                            <td class="text-muted small fw-bold pt-2">Status SP2D</td>
                            <td class="pt-2">:</td>
                            <td class="pt-2">
                                @if($realisasi->status_sp2d)
                                <span class="badge bg-success shadow-sm px-3 py-2">
                                    <i class="fas fa-check-double me-1"></i> SUDAH TERBIT SP2D
                                </span>
                                @else
                                <span class="badge bg-light text-muted border px-3 py-2">
                                    <i class="fas fa-clock me-1"></i> BELUM / PROSES SP2D
                                </span>
                                @endif
                            </td>
                        </tr>

                        {{-- STATUS DIGITALISASI --}}
                        <tr>
                            <td class="text-muted small fw-bold pt-2">Status Digitalisasi</td>
                            <td class="pt-2">:</td>
                            <td class="pt-2">
                                @if($realisasi->status_digitalisasi)
                                <span class="badge bg-primary shadow-sm px-3 py-2">
                                    <i class="fas fa-cloud-upload-alt me-1"></i> TERDIGITALISASI
                                </span>
                                @else
                                <span class="badge bg-light text-muted border px-3 py-2">
                                    <i class="fas fa-history me-1"></i> BELUM DIGITALISASI
                                </span>
                                @endif
                            </td>
                        </tr>

                        {{-- DATA GUP & SPBY --}}
                        @if($realisasi->gup || $realisasi->no_urut_arsip_spby)
                        <tr class="border-top">
                            <td class="text-muted small fw-bold text-primary pt-2">Nomor GUP</td>
                            <td class="pt-2">:</td>
                            <td class="fw-bold text-primary pt-2">{{ $realisasi->gup ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small fw-bold text-primary">No. Urut Arsip SPBY</td>
                            <td>:</td>
                            <td class="fw-bold text-primary">{{ $realisasi->no_urut_arsip_spby ?? '-' }}</td>
                        </tr>
                        @endif
                    </table>

                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary uppercase small">
                        <i class="fas fa-calculator me-2"></i>Rincian Keuangan & Pajak
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light shadow-sm mb-3">
                                <small class="text-muted">Jumlah Bruto (A):</small>
                                <h4 class="fw-bold text-dark">Rp {{ number_format($realisasi->jumlah, 2, ',', '.') }}</h4>
                                <small class="text-muted">PPN (B):</small>
                                <p class="mb-0 fw-bold text-primary">+ Rp {{ number_format($realisasi->ppn, 2, ',', '.') }}</p>
                                <hr class="my-2">
                                <small class="text-muted">Total Kotor (A + B):</small>
                                <h5 class="fw-bold text-dark">Rp {{ number_format((float)$realisasi->jumlah_kotor, 2, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded shadow-sm bg-white">
                                <small class="text-muted text-danger fw-bold small">Potongan Pajak (PPh):</small>
                                <div class="d-flex justify-content-between small mt-2">
                                    <span>PPh Pasal 21</span>
                                    <span>Rp {{ number_format($realisasi->pph21, 2, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span>PPh Pasal 22</span>
                                    <span>Rp {{ number_format($realisasi->pph22, 2, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span>PPh Pasal 23</span>
                                    <span>Rp {{ number_format($realisasi->pph23, 2, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span>PPh 22 / Lainnya</span>
                                    <span>Rp {{ number_format($realisasi->pph_final, 2, ',', '.') }}</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-bold text-danger">
                                    <span>Total Potongan (C)</span>
                                    <span>- Rp {{ number_format((float)$realisasi->pph_total, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-primary text-white rounded shadow text-center">
                        <small class="text-uppercase opacity-75 small">Jumlah Bersih Yang Dibayarkan (Total Kotor - Potongan):</small>
                        <h2 class="fw-bold mb-0">Rp {{ number_format((float)$realisasi->total_bersih, 2, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            {{-- PANEL ACTIONS --}}
            @if(Auth::user()->role == 'Verifikator' && $realisasi->status_berkas == 'Proses Verifikasi')
            <div class="card shadow mb-4 border-left-warning text-dark">
                <div class="card-header py-3 bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold small uppercase"><i class="fas fa-check-double me-1"></i> Panel Verifikator</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('realisasi-v2.approve', $realisasi->id) }}" method="POST" class="mb-2">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm w-100 shadow-sm fw-bold">
                            <i class="fas fa-check me-1"></i> SETUJUI KE BENDAHARA
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger btn-sm w-100 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalReject">
                        <i class="fas fa-undo me-1"></i> KEMBALIKAN KE PLO
                    </button>
                </div>
            </div>
            @endif

            @if(Auth::user()->role == 'Bendahara' && $realisasi->status_berkas == 'Terverifikasi')
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold small uppercase"><i class="fas fa-money-check-alt me-1"></i> Tahap 1: Input GUP</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('realisasi-v2.edit', $realisasi->id) }}" class="btn btn-primary btn-sm w-100 shadow-sm fw-bold mb-2">
                        <i class="fas fa-edit me-1"></i> ISI GUP/SPBY & KE PPK
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalReturnVerif">
                        <i class="fas fa-reply me-1"></i> BALIK KE VERIFIKATOR
                    </button>
                </div>
            </div>
            @endif

            @if(Auth::user()->role == 'PPK' && $realisasi->status_berkas == 'Proses PPK')
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold small uppercase"><i class="fas fa-user-check me-1"></i> Panel Verifikasi PPK</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('realisasi-v2.verify-ppk', $realisasi->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-info btn-sm w-100 text-white fw-bold shadow-sm">
                            <i class="fas fa-arrow-right me-1"></i> SETUJUI (LANJUT PPSPM)
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- PANEL PPSPM (Tampil saat Proses PPSPM ATAU Menunggu SP2D Terbit) --}}
            @if(Auth::user()->role == 'PPSPM' && ($realisasi->status_berkas == 'Proses PPSPM' || $realisasi->status_berkas == 'Menunggu SP2D Terbit'))
            <div class="card shadow mb-4 border-left-dark">
                <div class="card-header py-3 bg-dark text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold small uppercase"><i class="fas fa-stamp me-1"></i> Panel Verifikasi PPSPM</h6>
                    @if($realisasi->status_berkas == 'Menunggu SP2D Terbit')
                    <span class="badge bg-light text-dark shadow-sm">Tahap SP2D</span>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('realisasi-v2.verify-ppspm', $realisasi->id) }}" method="POST">
                        @csrf @method('PATCH')

                        @if($realisasi->jenis_realisasi == 'LS')
                        {{-- Input Catatan Revisi --}}
                        <div class="mb-3">
                            <label class="small mb-1 fw-bold text-danger">Catatan/Alasan Revisi (Wajib jika menolak)</label>
                            <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Masukkan alasan jika berkas ditolak ke PPBJ..."></textarea>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                {{-- Jika status sudah Menunggu SP2D, tombol berubah menjadi Selesaikan --}}
                                @if($realisasi->status_berkas == 'Menunggu SP2D Terbit')
                                <button type="submit" name="action" value="finalize_ls" class="btn btn-success btn-sm w-100 fw-bold shadow-sm">
                                    <i class="fas fa-check-double me-1"></i> SELESAIKAN (LS)
                                </button>
                                @else
                                <button type="submit" name="action" value="approve" class="btn btn-dark btn-sm w-100 fw-bold shadow-sm">
                                    <i class="fas fa-arrow-right me-1"></i> LANJUT SP2D
                                </button>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <button type="submit" name="action" value="reject" class="btn btn-outline-danger btn-sm w-100 fw-bold shadow-sm">
                                    <i class="fas fa-undo me-1"></i> TOLAK KE PPBJ
                                </button>
                            </div>
                        </div>
                        @else
                        {{-- Logika untuk Non-LS (GUP) --}}
                        <button type="submit" name="action" value="approve" class="btn btn-dark btn-sm w-100 fw-bold shadow-sm">
                            <i class="fas fa-check-circle me-1"></i> SETUJUI (FINALISASI BEND.)
                        </button>
                        @endif
                    </form>

                    {{-- Tombol Edit SPM khusus untuk PPSPM jika ingin ubah nomor SPM tanpa ganti status --}}
                    @if($realisasi->status_berkas == 'Menunggu SP2D Terbit')
                    <hr>
                    <a href="{{ route('realisasi-v2.edit', $realisasi->id) }}" class="btn btn-outline-dark btn-sm w-100 fw-bold">
                        <i class="fas fa-edit me-1"></i> UPDATE NO. SPM / FAKTUR
                    </a>
                    @endif
                </div>
            </div>
            @endif
            @if(Auth::user()->role == 'Bendahara' && $realisasi->status_berkas == 'Menunggu Finalisasi Bendahara')
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold small uppercase"><i class="fas fa-flag-checkered me-1"></i> Tahap 2: Finalisasi</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('realisasi-v2.finalize', $realisasi->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success btn-sm w-100 shadow-sm fw-bold">
                            <i class="fas fa-check-double me-1"></i> SELESAIKAN & POTONG PAGU
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @php
            $lampiran = $realisasi->lampiran;
            if (is_string($lampiran)) { $lampiran = json_decode($lampiran, true); }
            $lampiran = is_array($lampiran) ? $lampiran : [];
            $lampiran = collect($lampiran)->sortBy('nama_berkas')->values()->all();
            @endphp

            {{-- PANEL LAMPIRAN BERKAS --}}
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold small uppercase"><i class="fas fa-paperclip me-1"></i> Kelengkapan Dokumen</h6>
                    <span class="badge bg-white text-info shadow-sm">{{ count($lampiran) }} Berkas</span>
                </div>
                <div class="card-body p-0">
                    @if(count($lampiran) > 0)
                    <div class="list-group list-group-flush" style="max-height: 450px; overflow-y: auto;">
                        @foreach($lampiran as $file)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 hover-bg-light border-bottom">
                            <div class="text-truncate me-2" style="max-width: 85%;">
                                @php
                                $nama = $file['nama_berkas'] ?? 'Berkas';
                                $icon = 'fa-file-alt';
                                $iconColor = 'text-secondary';
                                if(Str::contains($nama, ['Kuitansi', 'Kwitansi', 'Invoice'])) { $icon = 'fa-receipt'; $iconColor = 'text-success'; }
                                elseif(Str::contains($nama, ['Surat', 'SPPD', 'Undangan'])) { $icon = 'fa-envelope-open-text'; $iconColor = 'text-primary'; }
                                elseif(Str::contains($nama, ['SSP', 'NPWP', 'Pajak'])) { $icon = 'fa-file-invoice-dollar'; $iconColor = 'text-danger'; }
                                elseif(Str::contains($nama, ['Pesawat', 'Transport', 'Taksi'])) { $icon = 'fa-plane-departure'; $iconColor = 'text-warning'; }
                                @endphp
                                <i class="fas {{ $icon }} {{ $iconColor }} me-2"></i>
                                <span class="small text-dark fw-bold">{{ $nama }}</span>
                                <br>
                                <small class="text-muted x-small">
                                    <i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($file['uploaded_at'] ?? now())->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ asset('storage/' . ($file['path'] ?? '')) }}" target="_blank" class="btn btn-sm btn-info btn-circle shadow-sm" title="Lihat Dokumen">
                                    <i class="fas fa-eye fa-xs"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                        <p class="text-muted small mb-0">Belum ada lampiran berkas digital.</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-secondary small uppercase"><i class="fas fa-sitemap me-1"></i> Informasi Pagu</h6>
                </div>
                <div class="card-body small p-3">
                    @php
                    $coa = $realisasi->coaItem;
                    $satker = $coa?->subKomponen?->komponen?->rincianOutput?->klasifikasiRo?->kegiatan?->program?->satker;
                    @endphp
                    <div class="mb-2">
                        <label class="text-muted d-block mb-0 x-small">Satker:</label>
                        <span class="fw-bold text-dark small">{{ $satker?->nama_satker ?? '-' }}</span>
                    </div>
                    <div class="mb-2 border-top pt-1 text-primary">
                        <label class="text-muted d-block mb-0 x-small text-dark">Item Anggaran (COA):</label>
                        <span class="fw-bold">{{ $coa->uraian ?? '-' }}</span>
                    </div>
                    <div class="mb-1 border-top pt-1">
                        <label class="text-muted d-block mb-0 x-small">MAK (Belanja):</label>
                        <span class="badge bg-dark text-white">{{ $realisasi->mak?->nama_mak ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        @if(Auth::user()->role == 'PLO' && ($realisasi->status_berkas == 'Draft' || $realisasi->status_berkas == 'Ditolak/Revisi'))
                        <a href="{{ route('realisasi-v2.edit', $realisasi->id) }}" class="btn btn-warning btn-sm text-white shadow fw-bold">
                            <i class="fas fa-edit me-1"></i> EDIT DATA
                        </a>
                        @endif
                        <a href="{{ route('realisasi-v2.index', ['coa_item_id' => $realisasi->coa_item_id]) }}" class="btn btn-secondary btn-sm shadow fw-bold">
                            <i class="fas fa-list me-1"></i> KEMBALIKAN KE DAFTAR
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- AUDIT TRAIL --}}
    <h6 class="fw-bold text-primary uppercase small mb-4 mt-2"><i class="fas fa-history me-1"></i> Riwayat Aktivitas & Audit Trail</h6>
    <div class="audit-trail-wrapper mb-5">
        @forelse($realisasi->logs as $log)
        <div class="audit-item d-flex position-relative mb-4">
            <div class="audit-line"></div>
            <div class="audit-dot bg-primary shadow-sm"></div>
            <div class="card border-0 shadow-sm w-100 ms-3 audit-card">
                <div class="card-body py-3 px-4 text-dark">
                    <div class="row align-items-center">
                        <div class="col-lg-5">
                            <h6 class="fw-bold mb-1 small">{{ $log->activity }}</h6>
                            <p class="text-muted mb-0 x-small leading-relaxed">{{ $log->description }}</p>
                        </div>
                        <div class="col-lg-4 text-center">
                            <div class="user-pill d-inline-flex align-items-center bg-light border px-3 py-1 rounded-pill">
                                <i class="fas fa-user-circle text-primary me-2"></i>
                                <span class="x-small fw-bold text-dark">{{ $log->user->name ?? 'System' }}</span>
                                <span class="x-small text-muted ms-1">({{ $log->role }})</span>
                            </div>
                        </div>
                        <div class="col-lg-3 text-end">
                            <div class="mb-1">
                                <span class="text-muted x-small"><i class="far fa-clock me-1"></i> {{ $log->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($log->status_akhir)
                            <span class="badge x-small py-1 px-3 rounded-pill {{ $log->status_akhir == 'Selesai' ? 'bg-success' : ($log->status_akhir == 'Ditolak/Revisi' ? 'bg-danger' : 'bg-info') }}">
                                {{ $log->status_akhir }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm text-center py-4">
            <p class="text-muted small mb-0">Belum ada riwayat aktivitas.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- MODALS --}}
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('realisasi-v2.reject', $realisasi->id) }}" method="POST">
            @csrf @method('PATCH')
            <div class="modal-content text-dark">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title font-weight-bold small">Kembalikan ke PLO</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="catatan" class="form-control" rows="4" placeholder="Tulis catatan revisi..." required></textarea>
                </div>
                <div class="modal-footer py-1">
                    <button type="submit" class="btn btn-danger btn-sm px-4">Kirim ke PLO</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalReturnVerif" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('realisasi-v2.return-verif', $realisasi->id) }}" method="POST">
            @csrf @method('PATCH')
            <div class="modal-content text-dark">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title font-weight-bold small">Kembalikan ke Verifikator</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="catatan" class="form-control" rows="4" placeholder="Catatan untuk verifikator..." required></textarea>
                </div>
                <div class="modal-footer py-1">
                    <button type="submit" class="btn btn-danger btn-sm px-4">Kirim ke Verifikator</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .x-small {
        font-size: 0.7rem;
    }

    .audit-trail-wrapper {
        position: relative;
        padding-left: 10px;
    }

    .audit-item:not(:last-child) .audit-line {
        position: absolute;
        left: 5px;
        top: 15px;
        bottom: -30px;
        width: 2px;
        background: #e3e6f0;
        z-index: 1;
    }

    .audit-dot {
        position: absolute;
        left: 0;
        top: 8px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        z-index: 2;
    }

    .audit-card {
        transition: all 0.2s ease;
        border-radius: 12px !important;
    }

    .audit-card:hover {
        transform: translateX(5px);
        background-color: #f8f9fc;
    }

    .hover-bg-light:hover {
        background-color: #f8f9fc;
    }
</style>
@endsection