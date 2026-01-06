@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Realisasi Baru (Role: PLO)</h6>
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
                        "Nominatif",
                        "Surat Tugas",
                        "Surat Bukti / Pengeluaran Asli",
                        "DPR (Daftar Pengeluaran Riil)",
                        "SPPD",
                        "Undangan / Surat Undangan TTE Kapus",
                        "Notulen / Notulensi",
                        "Dokumentasi",
                        "Daftar Hadir / Daftar Hadir Lengkap TTD Penanggung Jawab Kegiatan",
                        "Kuitansi dan Invoice",
                        "Kuitansi Hotel",
                        "Kuitansi Transport",
                        "DPR Taksi Bandara",
                        "Kuitansi Pesawat/Kereta / Tiket",
                        "Boarding Pass Pesawat/Kereta",
                        "Rekap Transport Riil",
                        "Rincian Biaya Perjalanan Dinas (RPD)",
                        "BAP",
                        "SSP (Surat Setoran Pajak) / PPN / PPh",
                        "NPWP",
                        "Rekening Koran / Buku Tabungan",
                        "CV + NIK/NIP (Narsum)",
                        "Undangan Narsum / Permohonan Narsum",
                        "Surat Tugas Dari K/L/Pihak Narsum atau SK",
                        "Materi / Materi Pelatihan / Materi & Dokumentasi",
                        "Daftar Hadir Narsum",
                        "Berkas Pengadaan dan Pembayaran",
                        "Sertifikat",
                        "Kuitansi Pelatihan",
                        "Form 8 Jam",
                        "Surat Bukti UH & Transport",
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
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Sumber Anggaran</label>
                                        <select name="sumber_anggaran" id="sumber_anggaran" class="form-select border-primary shadow-sm" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="BGN" {{ old('sumber_anggaran') == 'BGN' ? 'selected' : '' }}>BGN</option>
                                            <option value="GF" {{ old('sumber_anggaran') == 'GF' ? 'selected' : '' }}>GF</option>
                                            <option value="GUP" {{ old('sumber_anggaran') == 'GUP' ? 'selected' : '' }}>GUP</option>
                                        </select>
                                    </div>

                                    {{-- 2. BIDANG: Muncul jika GUP dipilih, Posisinya dekat Kode Unik --}}
                                    <div class="col-md-4" id="container_bidang" style="display:none;">
                                        <label class="form-label fw-bold text-primary">BIDANG (GUP)</label>
                                        <select name="bidang" id="select_bidang" class="form-select border-primary shadow-sm">
                                            <option value="" selected disabled>-- Pilih Bidang --</option>
                                            <option value="TU">Tata Usaha (TU)</option>
                                            <option value="SI">Sistem Informasi (SI)</option>
                                            <option value="IF">Infra (IF)</option>
                                            <option value="KM">Keamanan (KT)</option>
                                            <option value="PR">Perencana (PR)</option>
                                            <option value="AD">ADKLS (AD)</option>
                                        </select>
                                    </div>

                                    {{-- 3. KODE UNIK PLO --}}
                                    <div class="col-md-4">
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
                                        <label class="form-label text-primary">AKUN</label>
                                        <input type="text" name="akun" class="form-control border-primary" value="{{ $selectedCoa->kode_coa_item ?? old('akun') }}">
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
                                        <label class="form-label small text-primary">PPh Final</label>
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
                                                    <div class="file-name-display small text-primary mt-1" style="display:none; font-size: 0.75rem;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- KOLOM KANAN: INFO & STATUS --}}
                            <div class="col-md-5 bg-light p-3 border-start">
                                <div class="alert alert-warning mb-3 py-2 border-left-warning shadow-sm">
                                    <small class="fw-bold text-uppercase">Informasi Pagu COA:</small>
                                    <h6 class="mb-0 mt-1 fw-bold text-dark">{{ $selectedCoa->uraian ?? 'Data tidak ditemukan' }}</h6>
                                    <hr class="my-1">
                                    <small class="text-dark">
                                        Sisa Pagu:
                                        <strong class="text-danger">
                                            Rp {{ number_format(($selectedCoa->jumlah ?? 0) - ($selectedCoa->realisasi_total ?? 0), 0, ',', '.') }}
                                        </strong>
                                    </small>
                                </div>

                                {{-- SILSILAH / HIERARCHY - TETAP ADA --}}
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
                                    <select name="status_berkas" class="form-select border-primary shadow-sm">
                                        <option value="Proses Verifikasi">Verifikator</option>
                                        <option value="Draft">Simpan Draft</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">TGL PENYERAHAN BERKAS</label>
                                    <input type="date" name="tanggal_penyerahan_berkas" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>

                                <div class="form-check form-switch mt-4 p-3 bg-white border rounded shadow-sm">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="status_digitalisasi" value="1" id="digitalCheck">
                                    <label class="form-check-label fw-bold text-primary" for="digitalCheck">
                                        STATUS BERKAS TERDIGITALISASI
                                    </label>
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
    // Inisialisasi Elemen
    const ploInput = document.getElementById('kode_unik_plo');
    const sumberInput = document.getElementById('sumber_anggaran');
    const noUrutInput = document.getElementById('no_urut');
    const bidangSelect = document.getElementById('select_bidang');
    const containerBidang = document.getElementById('container_bidang');
    const userInitial = ploInput.getAttribute('data-user-initial');

    // Fungsi Fetch No Urut & Update Kode
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
                    // data.next_no_urut sekarang berupa angka biasa (1, 2, dst)
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

                    // Hasil format di UI: [Initial].[Middle].[AngkaMurni]
                    ploInput.value = `${userInitial}.${middlePart}.${data.next_no_urut}`;
                })
                .catch(error => {
                    console.error('Error:', error);
                    noUrutInput.value = "1";
                    ploInput.value = "";
                });
        }
    }

    // Event Listeners
    bidangSelect.addEventListener('change', fetchNoUrut);
    sumberInput.addEventListener('change', fetchNoUrut);

    // --- SCRIPT BERKAS ---
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

            const allInputs = document.querySelectorAll('.doc-input');
            let count = 0;
            allInputs.forEach(i => {
                if (i.files.length > 0) count++;
            });
            counter.innerText = `${count} Terpilih`;
            counter.className = count > 0 ? "badge bg-primary" : "badge bg-secondary";
        });
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