@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Edit Realisasi (Role: {{ Auth::user()->role }})
                    </h6>
                    <a href="{{ route('realisasi-v2.index', ['coa_item_id' => $realisasiV2->coa_item_id]) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">

                    {{-- PANEL INFORMASI REVISI --}}
                    @if($realisasiV2->status_berkas == 'Ditolak/Revisi')
                    <div class="alert alert-danger border-left-danger shadow-sm mb-4">
                        <h6 class="fw-bold"><i class="fas fa-exclamation-circle me-2"></i> PERLU REVISI (Catatan Terakhir):</h6>
                        <p class="mb-0 bg-white p-2 rounded border mt-2 text-dark font-italic">
                            "{{ $realisasiV2->uraian_revisi ?? 'Mohon periksa kembali kelengkapan berkas.' }}"
                        </p>
                    </div>
                    @endif

                    <form action="{{ route('realisasi-v2.update', $realisasiV2->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @php
                        $selectedCoa = $realisasiV2->coaItem;
                        $sub = $selectedCoa?->subKomponen;
                        $komp = $sub?->komponen;
                        $ro = $komp?->rincianOutput;
                        $keg = $ro?->klasifikasiRo?->kegiatan;
                        $prog = $keg?->program;
                        $satker = $prog?->satker;

                        $list_berkas = [
                        "Nominatif", "Surat Tugas", "Surat Bukti / Pengeluaran Asli", "DPR (Daftar Pengeluaran Riil)", "SPPD",
                        "Undangan / Surat Undangan TTE Kapus", "Notulen / Notulensi", "Dokumentasi",
                        "Daftar Hadir / Daftar Hadir Lengkap TTD Penanggung Jawab Kegiatan", "Kuitansi and Invoice",
                        "Kuitansi Hotel", "Kuitansi Transport", "DPR Taksi Bandara", "Kuitansi Pesawat/Kereta / Tiket",
                        "Boarding Pass Pesawat/Kereta", "Rekap Transport Riil", "Rincian Biaya Perjalanan Dinas (RPD)",
                        "BAP", "SSP (Surat Setoran Pajak) / PPN / PPh", "NPWP", "Rekening Koran / Buku Tabungan",
                        "CV + NIK/NIP (Narsum)", "Undangan Narsum / Permohonan Narsum", "Surat Tugas Dari K/L/Pihak Narsum atau SK",
                        "Materi / Materi Pelatihan / Materi & Dokumentasi", "Daftar Hadir Narsum", "Berkas Pengadaan dan Pembayaran",
                        "Sertifikat", "Kuitansi Pelatihan", "Form 8 Jam", "Surat Bukti UH & Transport", "Kwitansi Konsumsi (Makan dan Snack) TTD PJ"
                        ];

                        $lampiranLama = is_array($realisasiV2->lampiran) ? $realisasiV2->lampiran : json_decode($realisasiV2->lampiran, true) ?? [];
                        $isLocked = ($realisasiV2->status_berkas == 'Menunggu Finalisasi Bendahara' && Auth::user()->role != 'Bendahara');
                        @endphp

                        <input type="hidden" name="coa_item_id" value="{{ $selectedCoa->id }}">
                        <input type="hidden" name="satker_id" value="{{ $realisasiV2->satker_id }}">
                        <input type="hidden" name="tahun_anggaran" value="{{ $realisasiV2->tahun_anggaran }}">

                        <div class="row">
                            {{-- KOLOM KIRI: INPUT DATA --}}
                            <div class="col-md-7">
                                <h5 class="text-info border-bottom pb-2 mb-3"><i class="fas fa-edit me-2"></i>Input Transaksi</h5>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Sumber Anggaran</label>
                                        <select name="sumber_anggaran" id="sumber_anggaran" class="form-select border-primary shadow-sm" required {{ $isLocked ? 'disabled' : '' }}>
                                            <option value="BGN" {{ $realisasiV2->sumber_anggaran == 'BGN' ? 'selected' : '' }}>BGN</option>
                                            <option value="GF" {{ $realisasiV2->sumber_anggaran == 'GF' ? 'selected' : '' }}>GF</option>
                                            <option value="GUP" {{ $realisasiV2->sumber_anggaran == 'GUP' ? 'selected' : '' }}>DIPA</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3" id="container_bidang" style="{{ $realisasiV2->sumber_anggaran == 'GUP' ? '' : 'display:none;' }}">
                                        <label class="form-label fw-bold text-primary">BIDANG (GUP)</label>
                                        <select name="bidang" id="select_bidang" class="form-select border-primary shadow-sm" {{ $isLocked ? 'disabled' : '' }}>
                                            <option value="" disabled>-- Pilih --</option>
                                            @foreach(['TU' => 'TU', 'SI' => 'SI', 'IF' => 'IF', 'KM' => 'KT', 'PR' => 'PR', 'AD' => 'AD'] as $val => $label)
                                            <option value="{{ $val }}" {{ $realisasiV2->bidang == $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-success">Jenis Realisasi</label>
                                        @if(Auth::user()->role === 'PPBJ')
                                        <input type="text" class="form-control bg-light fw-bold" value="LS" readonly>
                                        <input type="hidden" name="jenis_realisasi" value="LS">
                                        @else
                                        <select name="jenis_realisasi" id="jenis_realisasi" class="form-select border-success shadow-sm" {{ $isLocked ? 'disabled' : '' }}>
                                            <option value="GUP" {{ $realisasiV2->jenis_realisasi == 'GUP' ? 'selected' : '' }}>GUP</option>
                                            <option value="LS" {{ $realisasiV2->jenis_realisasi == 'LS' ? 'selected' : '' }}>LS</option>
                                        </select>
                                        @endif
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label text-primary fw-bold">Kode Unik PLO</label>
                                        <input type="text" name="kode_unik_plo" id="kode_unik_plo" class="form-control border-primary bg-light fw-bold"
                                            value="{{ old('kode_unik_plo', $realisasiV2->kode_unik_plo) }}" required
                                            {{ (Auth::user()->role == 'Bendahara' || $isLocked) ? 'readonly' : '' }}
                                            data-user-initial="{{ Auth::user()->plo_code ?? 'U' }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">No Urut</label>
                                        <input type="text" name="no_urut" id="no_urut" class="form-control bg-light fw-bold text-primary" readonly value="{{ $realisasiV2->no_urut }}">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Nama Kegiatan</label>
                                        <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $realisasiV2->nama_kegiatan) }}" required {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label text-primary">KODE AKUN</label>
                                        <input type="text" name="akun" class="form-control border-primary" value="{{ old('akun', $realisasiV2->akun) }}" {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Penerima / Penyedia</label>
                                        <input type="text" name="penerima_penyedia" class="form-control" value="{{ old('penerima_penyedia', $realisasiV2->penerima_penyedia) }}" required {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">URAIAN</label>
                                    <textarea name="uraian" class="form-control" rows="2" {{ $isLocked ? 'readonly' : '' }}>{{ old('uraian', $realisasiV2->uraian) }}</textarea>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label text-danger fw-bold">JUMLAH (Bruto)</label>
                                        <input type="number" step="0.01" name="jumlah" class="form-control border-danger" value="{{ old('jumlah', $realisasiV2->jumlah) }}" required {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label small">PPh 21</label>
                                        <input type="number" step="0.01" name="pph21" class="form-control" value="{{ old('pph21', $realisasiV2->pph21 ?? 0) }}" {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">PPh 23</label>
                                        <input type="number" step="0.01" name="pph23" class="form-control" value="{{ old('pph23', $realisasiV2->pph23 ?? 0) }}" {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-primary">PPh 22</label>
                                        <input type="number" step="0.01" name="pph_final" class="form-control border-primary" value="{{ old('pph_final', $realisasiV2->pph_final ?? 0) }}" {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">PPN</label>
                                        <input type="number" step="0.01" name="ppn" class="form-control" value="{{ old('ppn', $realisasiV2->ppn ?? 0) }}" {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">NPWP</label>
                                        <input type="text" name="npwp" class="form-control" placeholder="00.000.000.0-000.000" value="{{ old('npwp', $realisasiV2->npwp) }}" {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">TGL KUITANSI</label>
                                        <input type="date" name="tgl_kuitansi" class="form-control" value="{{ $realisasiV2->tgl_kuitansi?->format('Y-m-d') }}" required {{ $isLocked ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                {{-- FITUR KHUSUS PPBJ / LS --}}
                                @if(Auth::user()->role === 'PPBJ')
                                <div id="group_spp_ppbj" class="row mb-4 p-3 rounded shadow-sm border-start border-4 border-success" style="background-color: #f1fff6;">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-success"><i class="fas fa-file-invoice me-1"></i> NO SPP</label>
                                        <input type="text" name="no_spp" class="form-control border-success" value="{{ old('no_spp', $realisasiV2->no_spp) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-success"><i class="fas fa-calendar-alt me-1"></i> TANGGAL SPP</label>
                                        <input type="date" name="tgl_spp" class="form-control border-success" value="{{ $realisasiV2->tgl_spp?->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                @endif

                                {{-- PANEL BERKAS --}}
                                <div class="mb-4 border p-3 rounded bg-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                        <h6 class="text-primary fw-bold mb-0"><i class="fas fa-file-alt me-2"></i> Kelengkapan Berkas</h6>
                                        <span class="badge bg-info" id="file-counter">{{ count($lampiranLama) }} Berkas Tersimpan</span>
                                    </div>
                                    <div id="wrapper-berkas" style="max-height: 450px; overflow-y: auto; padding-right: 8px;">
                                        @foreach($list_berkas as $nama)
                                        @php
                                        $fileExist = collect($lampiranLama)->firstWhere('nama_berkas', $nama);
                                        $rowClass = $fileExist ? 'border-success bg-light' : 'border-light';
                                        @endphp
                                        <div class="file-row mb-2 p-2 rounded border-start border-4 {{ $rowClass }}" style="transition: all 0.2s;">
                                            <div class="row align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label small fw-bold mb-0 d-block text-truncate">
                                                        <i class="fas {{ $fileExist ? 'fa-check-circle text-success' : 'fa-file-alt text-muted' }} me-2"></i>
                                                        {{ $nama }}
                                                    </label>
                                                    @if($fileExist)
                                                    <div class="d-flex gap-2 mt-1">
                                                        <a href="{{ asset('storage/' . $fileExist['path']) }}" target="_blank" class="x-small text-primary fw-bold"><i class="fas fa-eye me-1"></i>Lihat</a>
                                                        <span class="x-small text-muted">| {{ \Carbon\Carbon::parse($fileExist['uploaded_at'])->format('d/m/y') }}</span>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-7">
                                                    @if(in_array(Auth::user()->role, ['PLO', 'PPBJ']))
                                                    <input type="file" name="dokumen[{{ $nama }}]" class="form-control form-control-sm doc-input shadow-sm">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                        {{-- Berkas Kustom Lama --}}
                                        @foreach($lampiranLama as $file)
                                        @if(!in_array($file['nama_berkas'], $list_berkas))
                                        <div class="file-row mb-2 p-2 rounded border-start border-4 border-info bg-light">
                                            <div class="row align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label small fw-bold mb-0 d-block text-truncate">
                                                        <i class="fas fa-check-circle text-info me-2"></i>{{ $file['nama_berkas'] }} <span class="badge bg-info p-1" style="font-size: 0.6rem">KUSTOM</span>
                                                    </label>
                                                    <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" class="x-small text-primary fw-bold"><i class="fas fa-eye me-1"></i>Lihat</a>
                                                </div>
                                                <div class="col-md-7">
                                                    @if(in_array(Auth::user()->role, ['PLO', 'PPBJ']))
                                                    <input type="file" name="dokumen[{{ $file['nama_berkas'] }}]" class="form-control form-control-sm doc-input">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                        <div id="additional-files-container"></div>
                                        @if(in_array(Auth::user()->role, ['PLO', 'PPBJ']))
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-info w-100" id="btn-add-file">
                                                <i class="fas fa-plus-circle me-1"></i> Tambah Berkas Lainnya (Kustom)
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN --}}
                            <div class="col-md-5 bg-light p-3 border-start">
                                <div class="alert alert-warning mb-4 py-2 border-left-warning shadow-sm">
                                    <small class="fw-bold text-uppercase">Informasi Pagu COA:</small>
                                    <h6 class="mb-0 mt-1 fw-bold text-dark">{{ $selectedCoa->uraian ?? 'Data tidak ditemukan' }}</h6>
                                    <hr class="my-1">
                                    <small class="text-dark">Sisa Pagu: <strong class="text-danger">Rp {{ number_format($selectedCoa->sisa_realisasi ?? 0, 0, ',', '.') }}</strong></small>
                                </div>

                                <h5 class="text-secondary border-bottom pb-2 mb-3">Silsilah / Hierarchy</h5>
                                <div id="info-box" class="small mb-4">
                                    <div class="mb-2">
                                        <label class="text-muted d-block mb-0 small">Satker:</label>
                                        <span class="fw-bold text-dark">{{ $satker->nama_satker ?? '-' }}</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1">
                                        <label class="text-muted d-block mb-0 small">Program / Kegiatan:</label>
                                        <span class="fw-bold text-dark">{{ $prog->kode_program ?? '' }} / {{ $keg->kode_kegiatan ?? '' }}</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1">
                                        <label class="text-muted d-block mb-0 small">Rincian Output (RO):</label>
                                        <span class="fw-bold text-primary">{{ $ro->nama_ro ?? '-' }} ({{ $ro->kode_ro ?? '' }})</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1">
                                        <label class="text-muted d-block mb-0 small">Komponen / Sub:</label>
                                        <span class="fw-bold text-dark">{{ $komp->kode_komponen ?? '-' }} / {{ $sub->kode_subkomponen ?? '-' }}</span>
                                    </div>
                                </div>

                                <hr>

                                {{-- DATA PPSPM --}}
                                @if(Auth::user()->role == 'PPSPM' && $realisasiV2->jenis_realisasi == 'LS')
                                <div class="mb-4 border p-3 rounded bg-white shadow-sm border-left-dark">
                                    <h6 class="text-dark fw-bold border-bottom pb-2 mb-3"><i class="fas fa-file-invoice-dollar me-1"></i> Data SPM & Faktur (PPSPM)</h6>
                                    <div class="row g-2">
                                        <div class="col-md-6 mb-2"><label class="small fw-bold">Nomor SPM</label><input type="text" name="no_spm" class="form-control form-control-sm" value="{{ old('no_spm', $realisasiV2->no_spm) }}"></div>
                                        <div class="col-md-6 mb-2"><label class="small fw-bold">Tanggal SPM</label><input type="date" name="tgl_spm" class="form-control form-control-sm" value="{{ $realisasiV2->tgl_spm?->format('Y-m-d') }}"></div>
                                        <div class="col-md-6"><label class="small fw-bold">No. Faktur Pajak</label><input type="text" name="no_faktur_pajak" class="form-control form-control-sm" value="{{ old('no_faktur_pajak', $realisasiV2->no_faktur_pajak) }}"></div>
                                        <div class="col-md-6"><label class="small fw-bold">Tgl. Faktur Pajak</label><input type="date" name="tgl_faktur_pajak" class="form-control form-control-sm" value="{{ $realisasiV2->tgl_faktur_pajak?->format('Y-m-d') }}"></div>
                                    </div>
                                </div>
                                @endif

                                {{-- PANEL BENDAHARA (DIPERTAHANKAN) --}}
                                @if(Auth::user()->role == 'Bendahara')
                                <div class="mb-4 border p-3 rounded bg-white shadow-sm border-left-primary">
                                    <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="fas fa-file-invoice me-1"></i> Kelengkapan Bendahara</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Nomor GUP</label>
                                        <input type="text" name="gup" class="form-control border-primary form-control-sm" value="{{ old('gup', $realisasiV2->gup) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">No. Urut Arsip SPBY</label>
                                        <input type="text" name="no_urut_arsip_spby" class="form-control border-primary form-control-sm" value="{{ old('no_urut_arsip_spby', $realisasiV2->no_urut_arsip_spby) }}">
                                    </div>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label small">KIRIM STATUS KE:</label>
                                    @if($realisasiV2->status_berkas == 'Menunggu Finalisasi Bendahara' && Auth::user()->role != 'Bendahara')
                                    <div class="p-2 bg-white border rounded fw-bold text-success small">{{ $realisasiV2->status_berkas }}</div>
                                    <input type="hidden" name="status_berkas" value="{{ $realisasiV2->status_berkas }}">
                                    @else
                                    <select name="status_berkas" id="status_berkas" class="form-select form-select-sm border-primary">
                                        @if(Auth::user()->role == 'Bendahara')
                                        <option value="Menunggu Finalisasi Bendahara" >Menunggu Finalisasi (Tetap)</option>
                                        <option value="Proses PPK" selected>Selesai & Teruskan ke PPK</option>
                                        <option value="Ditolak/Revisi">Kembalikan (Revisi)</option>
                                        @elseif(Auth::user()->role == 'PPBJ')
                                        <option value="Proses PPSPM" {{ $realisasiV2->status_berkas == 'Proses PPSPM' ? 'selected' : '' }}>Proses PPSPM (Ajukan)</option>
                                        <option value="Draft" {{ $realisasiV2->status_berkas == 'Draft' ? 'selected' : '' }}>Draft</option>
                                        @else
                                        <option value="Proses Verifikasi" {{ $realisasiV2->status_berkas == 'Proses Verifikasi' ? 'selected' : '' }}>Verifikator</option>
                                        <option value="Draft" {{ $realisasiV2->status_berkas == 'Draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="Proses PPSPM" {{ $realisasiV2->status_berkas == 'Proses PPSPM' ? 'selected' : '' }}>Proses PPSPM</option>
                                        <option value="Menunggu Finalisasi Bendahara" {{ $realisasiV2->status_berkas == 'Menunggu Finalisasi Bendahara' ? 'selected' : '' }}>Finalisasi Bendahara</option>
                                        @endif
                                    </select>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small">TGL PENYERAHAN BERKAS</label>
                                    <input type="date" name="tanggal_penyerahan_berkas" class="form-control form-control-sm" value="{{ $realisasiV2->tanggal_penyerahan_berkas?->format('Y-m-d') }}" {{ $isLocked ? 'readonly' : '' }}>
                                </div>

                                <div class="form-check form-switch mt-4 p-3 bg-white border rounded shadow-sm">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="status_digitalisasi" value="1" id="digitalCheck" {{ $realisasiV2->status_digitalisasi ? 'checked' : '' }} {{ ($isLocked && Auth::user()->role != 'Bendahara') ? 'disabled' : '' }}>
                                    <label class="form-check-label fw-bold text-primary small" for="digitalCheck">TERDIGITALISASI</label>
                                </div>

                                <div class="mt-4">
                                    <label class="form-label small">Mata Anggaran (MAK)</label>
                                    <select name="mak_id" class="form-select form-select-sm" {{ Auth::user()->role == 'Bendahara' ? 'disabled' : '' }}>
                                        @foreach($maks as $mak)
                                        <option value="{{ $mak->id }}" {{ ($realisasiV2->mak_id == $mak->id) ? 'selected' : '' }}>{{ $mak->nama_mak }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mt-5 d-grid">
                                    <button type="submit" class="btn {{ Auth::user()->role == 'Bendahara' ? 'btn-primary' : 'btn-success' }} btn-lg shadow">
                                        <i class="fas fa-save me-2"></i> Update Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ploInput = document.getElementById('kode_unik_plo');
    const sumberInput = document.getElementById('sumber_anggaran');
    const noUrutInput = document.getElementById('no_urut');
    const bidangSelect = document.getElementById('select_bidang');
    const containerBidang = document.getElementById('container_bidang');
    const userInitial = ploInput.getAttribute('data-user-initial');

    function fetchNoUrut() {
        const sumber = sumberInput.value;
        const bidang = bidangSelect.value;

        if (sumber === 'GUP') {
            containerBidang.style.display = 'block';
            bidangSelect.setAttribute('required', 'required');
            if (!bidang) return;
        } else {
            containerBidang.style.display = 'none';
            bidangSelect.removeAttribute('required');
        }

        @if(in_array(Auth::user()->role, ['PLO', 'PPBJ']))
        noUrutInput.value = "⏳...";
        const url = "{{ route('realisasi-v2.get-next-no-urut') }}";
        fetch(`${url}?sumber=${sumber}&bidang=${bidang}`)
            .then(response => response.json())
            .then(data => {
                noUrutInput.value = data.next_no_urut;
                let middlePart = (sumber === 'GUP') ? bidang : (sumber === 'BGN' ? 'MG' : (sumber === 'GF' ? 'GF' : sumber));
                ploInput.value = `${userInitial}.${middlePart}.${data.next_no_urut}`;
            });
        @endif
    }

    sumberInput.addEventListener('change', fetchNoUrut);
    bidangSelect.addEventListener('change', fetchNoUrut);

    // Berkas Dinamis
    document.getElementById('btn-add-file')?.addEventListener('click', function() {
        const container = document.getElementById('additional-files-container');
        const html = `
            <div class="file-row mb-2 p-2 rounded border-start border-4 additional-file shadow-sm" style="border-left-color: #17a2b8; background-color: #f0fbfc;">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <input type="text" name="custom_nama_berkas[]" class="form-control form-control-sm mb-1" placeholder="Nama Berkas..." required>
                    </div>
                    <div class="col-md-6">
                        <input type="file" name="custom_dokumen[]" class="form-control form-control-sm doc-input-custom" required>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-file"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-file')) {
            e.target.closest('.file-row').remove();
        }
    });
</script>

<style>
    .x-small {
        font-size: 0.65rem;
    }

    #wrapper-berkas::-webkit-scrollbar {
        width: 5px;
    }

    #wrapper-berkas::-webkit-scrollbar-thumb {
        background: #d1d3e2;
        border-radius: 10px;
    }

    .file-row:hover {
        background-color: #f1f3f9 !important;
        border-left-color: #4e73df !important;
    }
</style>
@endsection