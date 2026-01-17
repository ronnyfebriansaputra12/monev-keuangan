@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Tambah Realisasi Baru (Role: {{ Auth::user()->role }})
                    </h6>
                    <a href="{{ route('realisasi-v2.index', ['coa_item_id' => $selectedCoa->id]) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">

                    @if(isset($realisasi) && $realisasi->status_berkas == 'Ditolak/Revisi')
                    <div class="alert alert-danger border-left-danger shadow-sm mb-4">
                        <h6 class="fw-bold"><i class="fas fa-exclamation-circle me-2"></i> PERLU REVISI (Catatan Verifikator):</h6>
                        <p class="mb-0 bg-white p-2 rounded border mt-2 text-dark font-italic">
                            "{{ $realisasi->uraian_revisi ?? 'Mohon periksa kembali kelengkapan berkas dan nominal pajak.' }}"
                        </p>
                    </div>
                    @endif

                    <form action="{{ route('realisasi-v2.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @php
                        $sub = $selectedCoa?->subKomponen;
                        $komp = $sub?->komponen;
                        $ro = $komp?->rincianOutput;
                        $keg = $ro?->klasifikasiRo?->kegiatan;
                        $prog = $keg?->program;
                        $satker = $prog?->satker;

                        $list_berkas = [
                        "Nominatif", "Surat Tugas", "Surat Bukti / Pengeluaran Asli",
                        "DPR (Daftar Pengeluaran Riil)", "SPPD", "Undangan / Surat Undangan TTE Kapus",
                        "Notulen / Notulensi", "Dokumentasi", "Daftar Hadir / Daftar Hadir Lengkap TTD Penanggung Jawab Kegiatan",
                        "Kuitansi dan Invoice", "Kuitansi Hotel", "Kuitansi Transport",
                        "DPR Taksi Bandara", "Kuitansi Pesawat/Kereta / Tiket", "Boarding Pass Pesawat/Kereta",
                        "Rekap Transport Riil", "Rincian Biaya Perjalanan Dinas (RPD)", "BAP",
                        "SSP (Surat Setoran Pajak) / PPN / PPh", "NPWP", "Rekening Koran / Buku Tabungan",
                        "CV + NIK/NIP (Narsum)", "Undangan Narsum / Permohonan Narsum",
                        "Surat Tugas Dari K/L/Pihak Narsum atau SK", "Materi / Materi Pelatihan / Materi & Dokumentasi",
                        "Daftar Hadir Narsum", "Berkas Pengadaan dan Pembayaran", "Sertifikat",
                        "Kuitansi Pelatihan", "Form 8 Jam", "Surat Bukti UH & Transport",
                        "Kwitansi Konsumsi (Makan dan Snack) TTD PJ"
                        ];
                        @endphp

                        <input type="hidden" name="coa_item_id" value="{{ $selectedCoa->id }}">
                        <input type="hidden" name="satker_id" value="{{ $satker->id ?? '' }}">
                        <input type="hidden" name="tahun_anggaran" value="{{ $selectedCoa->tahun_anggaran ?? date('Y') }}">

                        <div class="row">
                            {{-- KOLOM KIRI: INPUT DATA --}}
                            <div class="col-md-7">
                                <h5 class="text-info border-bottom pb-2 mb-3"><i class="fas fa-edit me-2"></i>Input Transaksi</h5>

                                <div class="row mb-3">
                                    {{-- 1. SUMBER ANGGARAN --}}
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Sumber Anggaran</label>
                                        <select name="sumber_anggaran" id="sumber_anggaran" class="form-select border-primary shadow-sm" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="BGN" {{ old('sumber_anggaran') == 'BGN' ? 'selected' : '' }}>BGN</option>
                                            <option value="GF" {{ old('sumber_anggaran') == 'GF' ? 'selected' : '' }}>GF</option>
                                            <option value="GUP" {{ old('sumber_anggaran') == 'GUP' ? 'selected' : '' }}>APBN</option>
                                        </select>
                                    </div>

                                    {{-- 2. BIDANG --}}
                                    <div class="col-md-3" id="container_bidang" style="display:none;">
                                        <label class="form-label fw-bold text-primary">BIDANG (GUP)</label>
                                        <select name="bidang" id="select_bidang" class="form-select border-primary shadow-sm">
                                            <option value="" selected disabled>-- Pilih --</option>
                                            <option value="TU">TU</option>
                                            <option value="SI">SI</option>
                                            <option value="IF">IF</option>
                                            <option value="KM">KT</option>
                                            <option value="PR">PR</option>
                                            <option value="AD">AD</option>
                                        </select>
                                    </div>

                                    {{-- JENIS REALISASI --}}
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-success">Jenis Realisasi</label>
                                        @if(Auth::user()->role === 'PPBJ')
                                        <input type="text" class="form-control bg-light fw-bold" value="LS" readonly>
                                        <input type="hidden" name="jenis_realisasi" value="LS">
                                        @else
                                        <select name="jenis_realisasi" id="jenis_realisasi" class="form-select border-success shadow-sm">
                                            <option value="">-- Pilih --</option>
                                            <option value="LS" {{ old('jenis_realisasi') == 'LS' ? 'selected' : '' }}>LS</option>
                                            <option value="GUP" {{ old('jenis_realisasi') == 'GUP' ? 'selected' : '' }}>GUP</option>
                                        </select>
                                        @endif
                                    </div>

                                    {{-- 3. KODE UNIK PLO --}}
                                    <div class="col-md-3">
                                        <label class="form-label text-primary fw-bold">Kode Unik PLO</label>
                                        <input type="text" name="kode_unik_plo" id="kode_unik_plo"
                                            class="form-control border-primary bg-light fw-bold"
                                            readonly placeholder="Otomatis..."
                                            data-user-initial="{{ Auth::user()->plo_code ?? 'U' }}"
                                            value="{{ old('kode_unik_plo') }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">No Urut (Otomatis)</label>
                                        <input type="text" name="no_urut" id="no_urut" class="form-control bg-light fw-bold text-primary" readonly placeholder="Auto-Check...">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Nama Kegiatan</label>
                                        <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $keg->nama_kegiatan ?? '') }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label text-primary">KODE AKUN</label>
                                        <input type="text" name="akun" class="form-control border-primary"
                                            value="{{ $selectedCoa->mak->akun->kode_akun ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Penerima / Penyedia</label>
                                        <input type="text" name="penerima_penyedia" class="form-control" value="{{ old('penerima_penyedia') }}" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">URAIAN</label>
                                    <textarea name="uraian" class="form-control" rows="2">{{ old('uraian', $selectedCoa->uraian ?? '') }}</textarea>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label text-danger fw-bold">JUMLAH (Bruto)</label>
                                        <input type="number" step="0.01" name="jumlah" class="form-control border-danger" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label small">PPh 21</label>
                                        <input type="number" step="0.01" name="pph21" class="form-control" value="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">PPh 23</label>
                                        <input type="number" step="0.01" name="pph23" class="form-control" value="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-primary">PPh 22</label>
                                        <input type="number" step="0.01" name="pph_final" class="form-control border-primary" value="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">PPN</label>
                                        <input type="number" step="0.01" name="ppn" class="form-control" value="0">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">NPWP</label>
                                        <input type="text" name="npwp" class="form-control" placeholder="00.000.000.0-000.000">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">TGL KUITANSI</label>
                                        <input type="date" name="tgl_kuitansi" class="form-control" required value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                {{-- PANEL BERKAS --}}
                                <div class="mb-4 border p-3 rounded bg-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                        <h6 class="text-primary fw-bold mb-0">
                                            <i class="fas fa-file-alt me-2"></i> Kelengkapan Berkas
                                        </h6>
                                        <span class="badge bg-secondary" id="file-counter">0 Terpilih</span>
                                    </div>

                                    <div id="wrapper-berkas" style="max-height: 450px; overflow-y: auto; padding-right: 8px;">
                                        @foreach($list_berkas as $index => $nama)
                                        <div class="file-row mb-2 p-2 rounded border-start border-4" style="border-left-color: #e3e6f0; background-color: #f8f9fc; transition: all 0.3s;">
                                            <div class="row align-items-center">
                                                <div class="col-md-5">
                                                    <label class="form-label small fw-bold mb-0 d-block text-truncate" title="{{ $nama }}">
                                                        <i class="fas fa-question-circle text-muted me-2 status-icon"></i>{{ $nama }}
                                                    </label>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group input-group-sm">
                                                        <input type="file" name="dokumen[{{ $nama }}]" class="form-control doc-input" data-title="{{ $nama }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                        <div id="additional-files-container"></div>

                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-info w-100" id="btn-add-file">
                                                <i class="fas fa-plus-circle me-1"></i> Tambah Berkas Lainnya (Tidak ada di list)
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN --}}
                            <div class="col-md-5 bg-light p-3 border-start">
                                <div class="alert alert-warning mb-3 py-2 border-left-warning shadow-sm">
                                    <small class="fw-bold text-uppercase">Informasi Pagu COA:</small>
                                    <h6 class="mb-0 mt-1 fw-bold text-dark">{{ $selectedCoa->uraian ?? 'Data tidak ditemukan' }}</h6>
                                    <hr class="my-1">
                                    <small class="text-dark">
                                        Sisa Pagu: <strong class="text-danger">Rp {{ number_format(($selectedCoa->jumlah ?? 0) - ($selectedCoa->realisasi_total ?? 0), 0, ',', '.') }}</strong>
                                    </small>
                                </div>

                                <h5 class="text-secondary border-bottom pb-2 mb-3">Silsilah / Hierarchy</h5>
                                <div id="info-box" class="small">
                                    <div class="mb-2">
                                        <label class="text-muted d-block mb-0 small">Satker:</label>
                                        <span class="fw-bold text-dark">{{ $satker->nama_satker ?? '-' }}</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1 text-truncate">
                                        <label class="text-muted d-block mb-0 small">Program / Kegiatan:</label>
                                        <span class="fw-bold text-dark">{{ $prog->kode_program ?? '' }} / {{ $keg->kode_kegiatan ?? '' }}</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1">
                                        <label class="text-muted d-block mb-0 small">Rincian Output (RO):</label>
                                        <span class="fw-bold text-dark">{{ $ro->kode_ro ?? '' }}</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1">
                                        <label class="text-muted d-block mb-0 small">Komponen / Sub:</label>
                                        <span class="fw-bold text-dark">{{ $komp->kode_komponen ?? '' }} / {{ $sub->kode_subkomponen ?? '' }}</span>
                                    </div>
                                </div>

                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">KIRIM STATUS KE:</label>
                                    <select name="status_berkas" id="status_berkas" class="form-select border-primary shadow-sm">
                                        @if(Auth::user()->role === 'PPBJ')
                                        {{-- Jika PPBJ, langsung arahkan ke PPSPM --}}
                                        <option value="Proses PPSPM" selected>Proses PPSPM</option>
                                        <option value="Draft">Simpan Draft</option>
                                        @else
                                        {{-- Default untuk PLO / Superadmin --}}
                                        <option value="Proses Verifikasi" {{ old('status_berkas') == 'Proses Verifikasi' ? 'selected' : '' }}>Verifikator</option>
                                        <option value="Draft" {{ old('status_berkas') == 'Draft' ? 'selected' : '' }}>Simpan Draft</option>
                                        <option value="Proses PPSPM" {{ old('status_berkas') == 'Proses PPSPM' ? 'selected' : '' }}>Proses PPSPM</option>
                                        <option value="Menunggu Finalisasi Bendahara" {{ old('status_berkas') == 'Menunggu Finalisasi Bendahara' ? 'selected' : '' }}>Menunggu Finalisasi Bendahara</option>
                                        @endif
                                    </select>
                                </div>

                                {{-- Hanya render jika user memiliki role PPBJ --}}
                                @if(Auth::user()->role === 'PPBJ')
                                <div id="container_status_sp2d" class="form-check form-switch mb-3 p-3 bg-white border rounded shadow-sm" style="display: none;">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="status_sp2d" value="1" id="status_sp2d">
                                    <label class="form-check-label fw-bold text-primary" for="status_sp2d">
                                        STATUS SP2D (SUDAH TERBIT)
                                    </label>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">TGL PENYERAHAN BERKAS</label>
                                    <input type="date" name="tanggal_penyerahan_berkas" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>

                                <div class="mt-4">
                                    <label class="form-label small">Mata Anggaran (MAK)</label>
                                    <select name="mak_id" class="form-select bg-light form-select-sm">
                                        @foreach($maks as $mak)
                                        <option value="{{ $mak->id }}" {{ ($selectedCoa->mak_id == $mak->id) ? 'selected' : '' }}>
                                            {{ $mak->nama_mak }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mt-5 d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg shadow">
                                        <i class="fas fa-save me-2"></i> Simpan & Kirim
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

    // Element untuk Checkbox SP2D
    const statusSelect = document.getElementById('status_berkas');
    const containerSP2D = document.getElementById('container_status_sp2d');
    const checkboxSP2D = document.getElementById('status_sp2d');

    // Fungsi Toggle Checkbox SP2D
    function toggleSP2D() {
        // Cek apakah elemen ada di halaman (hanya ada jika role PPBJ)
        if (!containerSP2D) return;

        const val = statusSelect.value;

        // Logika kemunculan berdasarkan status berkas
        if (val === 'Proses PPSPM' || val === 'Menunggu Finalisasi Bendahara') {
            containerSP2D.style.display = 'block';
        } else {
            containerSP2D.style.display = 'none';
            if (checkboxSP2D) checkboxSP2D.checked = false;
        }
    }

    // Pastikan fungsi dipanggil saat ada perubahan pada select
    if (statusSelect) {
        statusSelect.addEventListener('change', toggleSP2D);
    }

    function fetchNoUrut() {
        const sumber = sumberInput.value;
        const bidang = bidangSelect.value;

        if (sumber === 'GUP') {
            containerBidang.style.display = 'block';
            bidangSelect.setAttribute('required', 'required');
            if (!bidang) {
                ploInput.value = "";
                noUrutInput.value = "";
                return;
            }
        } else {
            containerBidang.style.display = 'none';
            bidangSelect.removeAttribute('required');
            bidangSelect.value = "";
        }

        if (sumber) {
            noUrutInput.value = "⏳...";
            const url = "{{ route('realisasi-v2.get-next-no-urut') }}";
            fetch(`${url}?sumber=${sumber}&bidang=${bidang}`)
                .then(response => response.json())
                .then(data => {
                    noUrutInput.value = data.next_no_urut;
                    let middlePart = "";
                    if (sumber === 'GUP') {
                        middlePart = bidang;
                    } else if (sumber === 'BGN') {
                        middlePart = 'MG';
                    } else if (sumber === 'GF') {
                        middlePart = 'GF';
                    } else {
                        middlePart = sumber;
                    }

                    ploInput.value = `${userInitial}.${middlePart}.${data.next_no_urut}`;
                })
                .catch(error => {
                    noUrutInput.value = "1";
                    ploInput.value = "";
                });
        }
    }

    bidangSelect.addEventListener('change', fetchNoUrut);
    sumberInput.addEventListener('change', fetchNoUrut);
    statusSelect.addEventListener('change', toggleSP2D);

    // Jalankan pengecekan SP2D saat halaman dimuat pertama kali
    document.addEventListener('DOMContentLoaded', toggleSP2D);

    // Script Counter Berkas
    document.querySelectorAll('.doc-input').forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('.file-row');
            const counter = document.getElementById('file-counter');
            if (this.files.length > 0) {
                row.style.borderLeftColor = "#4e73df";
                row.style.backgroundColor = "#eaecf4";
            } else {
                row.style.borderLeftColor = "#e3e6f0";
                row.style.backgroundColor = "#f8f9fc";
            }
            const count = Array.from(document.querySelectorAll('.doc-input')).filter(i => i.files.length > 0).length;
            counter.innerText = `${count} Terpilih`;
            counter.className = count > 0 ? "badge bg-primary" : "badge bg-secondary";
        });
    });

    // Script untuk menambah input berkas dinamis
    document.getElementById('btn-add-file').addEventListener('click', function() {
        const container = document.getElementById('additional-files-container');
        const uniqueId = Date.now(); // ID unik agar tidak bentrok

        const html = `
        <div class="file-row mb-2 p-2 rounded border-start border-4 additional-file" style="border-left-color: #17a2b8; background-color: #f0fbfc;">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <input type="text" name="custom_nama_berkas[]" class="form-control form-control-sm mb-1" placeholder="Ketik Nama Berkas..." required>
                </div>
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <input type="file" name="custom_dokumen[]" class="form-control doc-input-custom" required>
                    </div>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-file"><i class="fas fa-times"></i></button>
                </div>
            </div>
        </div>
    `;
        container.insertAdjacentHTML('beforeend', html);
    });

    // Script untuk menghapus row yang baru ditambah
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-file') || e.target.closest('.btn-remove-file')) {
            const row = e.target.closest('.file-row');
            row.remove();
            updateFileCounter(); // Panggil fungsi counter jika ada
        }
    });

    // Update counter untuk menyertakan input kustom
    function updateFileCounter() {
        const staticDocs = Array.from(document.querySelectorAll('.doc-input')).filter(i => i.files.length > 0).length;
        const customDocs = Array.from(document.querySelectorAll('.doc-input-custom')).filter(i => i.files.length > 0).length;
        const total = staticDocs + customDocs;

        const counter = document.getElementById('file-counter');
        if (counter) {
            counter.innerText = `${total} Terpilih`;
            counter.className = total > 0 ? "badge bg-primary" : "badge bg-secondary";
        }
    }

    // Delegasi event untuk input file kustom agar counter terupdate
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('doc-input-custom')) {
            updateFileCounter();
        }
    });
</script>

<style>
    #wrapper-berkas::-webkit-scrollbar {
        width: 5px;
    }

    #wrapper-berkas::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #wrapper-berkas::-webkit-scrollbar-thumb {
        background: #d1d3e2;
        border-radius: 10px;
    }

    .file-row:hover {
        border-left-color: #4e73df !important;
        background-color: #f1f3f9 !important;
    }
</style>
@endsection