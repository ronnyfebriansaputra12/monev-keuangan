@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ Auth::user()->role == 'Bendahara' ? 'Verifikasi & Input GUP/SPBY (Role: Bendahara)' : 'Edit / Revisi Realisasi (Role: PLO)' }}
                    </h6>
                    <a href="{{ route('realisasi-v2.index', ['coa_item_id' => $realisasiV2->coa_item_id]) }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
                <div class="card-body">

                    {{-- PANEL INFORMASI REVISI --}}
                    @if($realisasiV2->status_berkas == 'Ditolak/Revisi')
                    <div class="alert alert-danger border-left-danger shadow-sm mb-4">
                        <h6 class="fw-bold"><i class="fas fa-exclamation-circle me-2"></i> PERLU REVISI (Catatan Terakhir):</h6>
                        <p class="mb-0 bg-white p-2 rounded border mt-2 text-dark font-italic">
                            "{{ Str::afterLast($realisasiV2->uraian, '[CATATAN') }}"
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
                        @endphp

                        <input type="hidden" name="coa_item_id" value="{{ $selectedCoa->id }}">
                        <input type="hidden" name="satker_id" value="{{ $realisasiV2->satker_id }}">
                        <input type="hidden" name="tahun_anggaran" value="{{ $realisasiV2->tahun_anggaran }}">

                        <div class="row">
                            <div class="col-md-7">
                                <h5 class="text-info border-bottom pb-2 mb-3">Data Transaksi</h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-primary fw-bold">Kode Unik PLO</label>
                                        <input type="text" name="kode_unik_plo" id="kode_unik_plo" class="form-control border-primary" value="{{ old('kode_unik_plo', $realisasiV2->kode_unik_plo) }}" required {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sumber Anggaran</label>
                                        <select name="sumber_anggaran" id="sumber_anggaran" class="form-select" required {{ Auth::user()->role == 'Bendahara' ? 'disabled' : '' }}>
                                            <option value="RM" {{ $realisasiV2->sumber_anggaran == 'RM' ? 'selected' : '' }}>RM (Rupiah Murni)</option>
                                            <option value="PNBP" {{ $realisasiV2->sumber_anggaran == 'PNBP' ? 'selected' : '' }}>PNBP</option>
                                            <option value="PL" {{ $realisasiV2->sumber_anggaran == 'PL' ? 'selected' : '' }}>PL</option>
                                            <option value="GUP" {{ $realisasiV2->sumber_anggaran == 'GUP' ? 'selected' : '' }}>GUP</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">No Urut</label>
                                        <input type="text" name="no_urut" id="no_urut" class="form-control bg-light fw-bold text-primary" readonly value="{{ str_pad($realisasiV2->no_urut, 4, '0', STR_PAD_LEFT) }}">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Nama Kegiatan</label>
                                        <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $realisasiV2->nama_kegiatan) }}" required {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label text-primary">AKUN</label>
                                        <input type="text" name="akun" class="form-control border-primary" value="{{ old('akun', $realisasiV2->akun) }}" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Penerima / Penyedia</label>
                                        <input type="text" name="penerima_penyedia" class="form-control" value="{{ old('penerima_penyedia', $realisasiV2->penerima_penyedia) }}" required {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">URAIAN</label>
                                    <textarea name="uraian" class="form-control" rows="3" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>{{ old('uraian', $realisasiV2->uraian) }}</textarea>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-danger fw-bold">JUMLAH (Bruto)</label>
                                        <input type="number" step="0.01" name="jumlah" class="form-control border-danger" value="{{ old('jumlah', $realisasiV2->jumlah) }}" required {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor Kuitansi Fisik</label>
                                        <input type="text" name="nomor_kuitansi" class="form-control" value="{{ old('nomor_kuitansi', $realisasiV2->nomor_kuitansi) }}" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label small">PPh 21</label>
                                        <input type="number" step="0.01" name="pph21" class="form-control" value="{{ old('pph21', $realisasiV2->pph21) }}" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">PPh 23</label>
                                        <input type="number" step="0.01" name="pph23" class="form-control" value="{{ old('pph23', $realisasiV2->pph23) }}" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-primary">PPh Final</label>
                                        <input type="number" step="0.01" name="pph_final" class="form-control border-primary" value="{{ old('pph_final', $realisasiV2->pph_final) }}" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">PPN</label>
                                        <input type="number" step="0.01" name="ppn" class="form-control" value="{{ old('ppn', $realisasiV2->ppn) }}" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">NPWP</label>
                                        <input type="text" name="npwp" class="form-control" value="{{ old('npwp', $realisasiV2->npwp) }}" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">TGL KUITANSI</label>
                                        <input type="date" name="tgl_kuitansi" class="form-control" required value="{{ $realisasiV2->tgl_kuitansi?->format('Y-m-d') }}" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                    </div>
                                </div>

                                {{-- PENGELOLAAN BERKAS --}}
                                <div class="mb-4 border p-3 rounded bg-white shadow-sm">
                                    <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="fas fa-paperclip"></i> Dokumen Pendukung</h6>
                                    @if(!empty($realisasiV2->lampiran))
                                    <div class="mb-3">
                                        <label class="small fw-bold text-secondary">Berkas Saat Ini:</label>
                                        <div class="list-group list-group-flush border rounded mb-2">
                                            @foreach($realisasiV2->lampiran as $index => $path)
                                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 bg-light">
                                                <span class="small text-truncate" style="max-width: 80%"><i class="fas fa-file-alt me-2"></i> Berkas {{ $index + 1 }}</span>
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    @if(Auth::user()->role == 'PLO')
                                    <div id="wrapper-berkas">
                                        <div class="input-group mb-2 berkas-item">
                                            <input type="file" name="files[]" class="form-control form-control-sm">
                                            <button class="btn btn-outline-danger btn-sm remove-berkas" type="button" style="display:none;"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-berkas"><i class="fas fa-plus"></i> Tambah Berkas Baru</button>
                                    @endif
                                </div>

                                {{-- INPUT KHUSUS BENDAHARA --}}
                                @if(Auth::user()->role == 'Bendahara')
                                <div class="mb-4 border p-3 rounded bg-white shadow-sm border-left-primary">
                                    <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="fas fa-file-invoice me-1"></i> Kelengkapan Bendahara</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Nomor GUP</label>
                                            <input type="text" name="gup" class="form-control border-primary" value="{{ old('gup', $realisasiV2->gup) }}" placeholder="Contoh: GUP-001/2025" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">No. Urut Arsip SPBY</label>
                                            <input type="text" name="no_urut_arsip_spby" class="form-control border-primary" value="{{ old('no_urut_arsip_spby', $realisasiV2->no_urut_arsip_spby) }}" placeholder="Contoh: 0451/SPBY/2025" required>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="mb-4">
                                    <label class="form-label">MAK (Mata Anggaran)</label>
                                    <select name="mak_id" id="mak_id" class="form-select bg-light" required {{ Auth::user()->role == 'Bendahara' ? 'disabled' : '' }}>
                                        @foreach($maks as $mak)
                                        <option value="{{ $mak->id }}" {{ ($realisasiV2->mak_id == $mak->id) ? 'selected' : '' }}>
                                            {{ $mak->nama_mak }} ({{ $mak->jenis_belanja }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5 bg-light p-3 border-start">
                                {{-- PANEL INFORMASI PAGU TETAP SAMA --}}
                                <div class="alert alert-warning mb-3 py-2 border-left-warning shadow-sm">
                                    <small class="fw-bold text-uppercase">Informasi Pagu COA:</small>
                                    <h6 class="mb-0 mt-1 fw-bold text-dark">{{ $selectedCoa->uraian ?? 'Data tidak ditemukan' }}</h6>
                                    <hr class="my-1">
                                    <small class="text-dark">Sisa Pagu: <strong class="text-danger">Rp {{ number_format($selectedCoa->sisa_realisasi ?? 0, 0, ',', '.') }}</strong></small>
                                </div>

                                <h5 class="text-secondary border-bottom pb-2 mb-3">Silsilah / Hierarchy</h5>
                                <div id="info-box" class="small">
                                    <div class="mb-2">
                                        <label class="text-muted d-block mb-0 small">Satker:</label>
                                        <span class="fw-bold text-dark">{{ $satker->nama_satker ?? '-' }}</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1">
                                        <label class="text-muted d-block mb-0 small">Program / Kegiatan:</label>
                                        <span class="fw-bold text-dark">{{ $prog->kode_program ?? '' }} / {{ $keg->kode_kegiatan ?? '' }}</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1 text-primary">
                                        <label class="text-muted d-block mb-0 small text-dark">Rincian Output (RO):</label>
                                        <span class="fw-bold">{{ $ro->nama_ro ?? '-' }}</span>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="text-secondary border-bottom pb-2 mb-3">Status Pengajuan</h5>
                                <div class="mb-3">
                                    <label class="form-label">BIDANG</label>
                                    <input type="text" name="bidang" class="form-control" value="{{ old('bidang', $realisasiV2->bidang) }}" {{ $realisasiV2->status_berkas == 'Menunggu Finalisasi Bendahara' || Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">STATUS SAAT INI:</label>

                                    @if($realisasiV2->status_berkas == 'Menunggu Finalisasi Bendahara')
                                    {{-- Jika di tahap final, status dikunci & tidak bisa diubah oleh PLO --}}
                                    <div class="p-2 bg-light border rounded fw-bold text-success">
                                        <i class="fas fa-clock me-1"></i> {{ $realisasiV2->status_berkas }}
                                    </div>
                                    <input type="hidden" name="status_berkas" value="{{ $realisasiV2->status_berkas }}">
                                    @else
                                    {{-- Jika status selain final (Draft/Revisi), tampilkan dropdown normal --}}
                                    <select name="status_berkas" class="form-select border-primary shadow-sm" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                        @if(Auth::user()->role == 'Bendahara')
                                        <option value="Proses PPK" selected>Teruskan ke PPK</option>
                                        @else
                                        <option value="Proses Verifikasi" {{ $realisasiV2->status_berkas == 'Proses Verifikasi' ? 'selected' : '' }}>Verifikator (Ajukan Kembali)</option>
                                        <option value="Draft" {{ $realisasiV2->status_berkas == 'Draft' ? 'selected' : '' }}>Simpan Draft</option>
                                        @endif
                                    </select>
                                    @endif
                                </div>

                                <!-- <div class="mb-3">
                                    <label class="form-label font-weight-bold">STATUS BERKAS:</label>

                                    @if($realisasiV2->status_berkas == 'Menunggu Finalisasi Bendahara')
                                    {{-- Jika berkas sudah di tahap Bendahara, PLO tidak boleh mengubah status --}}
                                    <div class="p-2 border rounded bg-light text-success fw-bold">
                                        <i class="fas fa-lock me-2"></i> {{ $realisasiV2->status_berkas }}
                                    </div>
                                    {{-- Hidden input agar nilai status_berkas tetap terkirim dan tidak error saat validasi --}}
                                    <input type="hidden" name="status_berkas" value="{{ $realisasiV2->status_berkas }}">
                                    <small class="text-muted italic">Status terkunci karena dalam proses finalisasi bendahara.</small>
                                    @else
                                    {{-- Jika status selain itu (Draft/Ditolak), tampilkan pilihan normal --}}
                                    <select name="status_berkas" class="form-select border-primary shadow-sm" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                        @if(Auth::user()->role == 'Bendahara')
                                        <option value="Proses PPK" selected>Teruskan ke PPK</option>
                                        @else
                                        <option value="Proses Verifikasi" {{ $realisasiV2->status_berkas == 'Proses Verifikasi' ? 'selected' : '' }}>Verifikator (Ajukan Kembali)</option>
                                        <option value="Draft" {{ $realisasiV2->status_berkas == 'Draft' ? 'selected' : '' }}>Simpan Draft</option>
                                        @endif
                                    </select>
                                    @endif
                                </div> -->

                                <div class="mb-3">
                                    <label class="form-label">TGL PENYERAHAN BERKAS</label>
                                    <input type="date" name="tanggal_penyerahan_berkas" class="form-control" value="{{ $realisasiV2->tanggal_penyerahan_berkas?->format('Y-m-d') }}" {{ Auth::user()->role == 'Bendahara' ? 'readonly' : '' }}>
                                </div>

                                <div class="form-check form-switch mt-4 p-3 bg-white border rounded shadow-sm">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="status_digitalisasi" value="1" id="digitalCheck" {{ $realisasiV2->status_digitalisasi ? 'checked' : '' }} {{ Auth::user()->role == 'Bendahara' ? 'disabled' : '' }}>
                                    <label class="form-check-label fw-bold text-primary" for="digitalCheck">
                                        STATUS BERKAS TERDIGITALISASI
                                    </label>
                                </div>

                                <div class="mt-5 d-grid">
                                    <button type="submit" class="btn {{ Auth::user()->role == 'Bendahara' ? 'btn-primary' : 'btn-success' }} btn-lg shadow">
                                        <i class="fas {{ Auth::user()->role == 'Bendahara' ? 'fa-paper-plane' : 'fa-check-circle' }} me-2"></i>
                                        {{ Auth::user()->role == 'Bendahara' ? 'Update & Kirim ke PPK' : 'Update & Kirim Revisi' }}
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
    @if(Auth::user()-> role == 'PLO')
    document.getElementById('add-berkas').addEventListener('click', function() {
        const wrapper = document.getElementById('wrapper-berkas');
        const firstItem = document.querySelector('.berkas-item');
        const newItem = firstItem.cloneNode(true);
        newItem.querySelector('input').value = "";
        newItem.querySelector('.remove-berkas').style.display = 'block';
        wrapper.appendChild(newItem);
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-berkas')) {
            e.target.closest('.berkas-item').remove();
        }
    });
    @endif
</script>
@endsection