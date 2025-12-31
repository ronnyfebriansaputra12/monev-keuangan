@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Realisasi Baru (Role: PLO)</h6>
                    <a href="{{ route('realisasi-v2.index', ['coa_item_id' => $selectedCoa->id]) }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
                <div class="card-body">

                    {{-- PANEL INFORMASI REVISI --}}
                    {{-- Ditampilkan jika data ini berasal dari pengembalian verifikator --}}
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
                        @endphp

                        <input type="hidden" name="coa_item_id" value="{{ $selectedCoa->id }}">
                        <input type="hidden" name="satker_id" value="{{ $satker->id ?? '' }}">
                        <input type="hidden" name="tahun_anggaran" value="{{ $selectedCoa->tahun_anggaran ?? date('Y') }}">

                        <div class="row">
                            <div class="col-md-7">
                                <h5 class="text-info border-bottom pb-2 mb-3">Input Transaksi</h5>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-primary fw-bold">Kode Unik PLO</label>
                                        <input type="text" name="kode_unik_plo" id="kode_unik_plo" class="form-control border-primary" placeholder="Contoh: DMS-01" value="{{ old('kode_unik_plo') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sumber Anggaran</label>
                                        <select name="sumber_anggaran" id="sumber_anggaran" class="form-select" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="BGN" {{ old('sumber_anggaran') == 'BGN' ? 'selected' : '' }}>BGN</option>
                                            <option value="GF" {{ old('sumber_anggaran') == 'GF' ? 'selected' : '' }}>GF</option>
                                            <option value="GUP" {{ old('sumber_anggaran') == 'GUP' ? 'selected' : '' }}>GUP</option>
                                        </select>
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
                                        <label class="form-label text-primary">AKUN (Dapat Diubah)</label>
                                        <input type="text" name="akun" class="form-control border-primary" value="{{ $selectedCoa->kode_coa_item ?? old('akun', '521211') }}" placeholder="Contoh: 521211">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Penerima / Penyedia</label>
                                        <input type="text" name="penerima_penyedia" class="form-control" value="{{ old('penerima_penyedia') }}" required placeholder="Contoh: Toko Maju Jaya">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">URAIAN</label>
                                    <textarea name="uraian" class="form-control" rows="2" placeholder="Detail kuitansi...">{{ old('uraian', $selectedCoa->uraian ?? '') }}</textarea>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-danger fw-bold">JUMLAH (Bruto)</label>
                                        <input type="number" step="0.01" name="jumlah" class="form-control border-danger" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor Kuitansi Fisik</label>
                                        <input type="text" name="nomor_kuitansi" class="form-control" placeholder="Contoh: KUIT/001/XII/2025">
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

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">NPWP</label>
                                        <input type="text" name="npwp" class="form-control" placeholder="00.000.000.0-000.000">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">TGL_KUITANSI</label>
                                        <input type="date" name="tgl_kuitansi" class="form-control" required value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="mb-4 border p-3 rounded bg-white shadow-sm">
                                    <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                                        <i class="fas fa-paperclip"></i> Dokumen Pendukung
                                    </h6>
                                    <div id="wrapper-berkas">
                                        <div class="input-group mb-2 berkas-item">
                                            <input type="file" name="files[]" class="form-control form-control-sm">
                                            <button class="btn btn-outline-danger btn-sm remove-berkas" type="button" style="display:none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-berkas">
                                        <i class="fas fa-plus"></i> Tambah Berkas
                                    </button>
                                    <div class="mt-2">
                                        <small class="text-muted italic">Format: PDF, JPG, PNG (Max 2MB per file)</small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">MAK (Mata Anggaran)</label>
                                    <select name="mak_id" id="mak_id" class="form-select bg-light" required>
                                        @foreach($maks as $mak)
                                        <option value="{{ $mak->id }}" {{ ($selectedCoa->mak_id == $mak->id) ? 'selected' : '' }}>
                                            {{ $mak->nama_mak }} ({{ $mak->jenis_belanja }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5 bg-light p-3 border-start">
                                <div class="alert alert-warning mb-3 py-2 border-left-warning shadow-sm">
                                    <small class="fw-bold text-uppercase">Informasi Pagu COA:</small>
                                    <h6 class="mb-0 mt-1 fw-bold text-dark">{{ $selectedCoa->uraian ?? 'Data tidak ditemukan' }}</h6>
                                    <hr class="my-1">
                                    <!-- <small class="text-dark">Sisa Pagu: <strong class="text-danger">Rp {{ number_format($selectedCoa->sisa_realisasi ?? 0, 0, ',', '.') }}</strong></small> 
                                     -->
                                    <small class="text-dark">
                                        Sisa Pagu:
                                        <strong class="text-danger">
                                            Rp {{ number_format(($selectedCoa->jumlah ?? 0) - ($selectedCoa->realisasi_total ?? 0), 0, ',', '.') }}
                                        </strong>
                                    </small>
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
                                    <div class="mb-2 border-top pt-1">
                                        <label class="text-muted d-block mb-0 small">Rincian Output (RO):</label>
                                        <span class="fw-bold text-dark">{{ $ro->kode_ro ?? '' }} / {{ $ro->nama_ro ?? '-' }}</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1">
                                        <label class="text-muted d-block mb-0 small">Komponen:</label>
                                        <span class="fw-bold text-dark">{{ $komp->kode_komponen ?? '-' }} / {{ $komp->nama_komponen ?? '-' }}</span>
                                    </div>
                                    <div class="mb-2 border-top pt-1">
                                        <label class="text-muted d-block mb-0 small">Sub Komponen:</label>
                                        <span class="fw-bold text-dark">{{ $sub->kode_subkomponen ?? '-' }} / {{ $sub->nama_subkomponen ?? '-' }}</span>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="text-secondary border-bottom pb-2 mb-3">Alur Verifikasi</h5>
                                <div class="mb-3">
                                    <label class="form-label">BIDANG</label>
                                    <input type="text" name="bidang" class="form-control" placeholder="Contoh: Tata Usaha" value="{{ old('bidang') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">KIRIM STATUS KE:</label>
                                    <select name="status_berkas" class="form-select border-primary shadow-sm">
                                        <option value="Proses Verifikasi">Verifikator</option>
                                        <option value="Draft">Simpan Draft</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">TGL PENYERAHAN BERKAS</label>
                                    <input type="date" name="tanggal_penyerahan_berkas" class="form-control">
                                </div>
                                <div class="form-check form-switch mt-4 p-3 bg-white border rounded shadow-sm">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="status_digitalisasi" value="1" id="digitalCheck">
                                    <label class="form-check-label fw-bold text-primary" for="digitalCheck">
                                        STATUS BERKAS TERDIGITALISASI
                                    </label>
                                </div>

                                <div class="mt-5 d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg shadow">
                                        <i class="fas fa-paper-plane me-2"></i> Simpan & Kirim Berkas
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

    function fetchNoUrut() {
        const plo = ploInput.value;
        const sumber = sumberInput.value;

        if (plo && sumber) {
            noUrutInput.value = "⏳...";

            fetch(`{{ url('realisasi-v2/get-next-no-urut') }}?plo=${encodeURIComponent(plo)}&sumber=${encodeURIComponent(sumber)}`)
                .then(response => response.json())
                .then(data => {
                    noUrutInput.value = data.next_no_urut;
                })
                .catch(error => {
                    console.error('Error fetching no urut:', error);
                    noUrutInput.value = "0001";
                });
        }
    }

    ploInput.addEventListener('blur', fetchNoUrut);
    sumberInput.addEventListener('change', fetchNoUrut);

    // SCRIPT TAMBAH BERKAS DINAMIS
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
</script>
@endsection