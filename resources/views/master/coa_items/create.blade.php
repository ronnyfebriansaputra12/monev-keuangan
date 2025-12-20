@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h4 class="page-title mb-1">Tambah COA (Bulk)</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Master Data</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('master.coa-items.index') }}">COA</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>Tambah</span></li>
            </ul>
        </div>

        <a href="{{ route('master.coa-items.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <div class="fw-bold mb-1">Periksa kembali input kamu:</div>
    <ul class="mb-0">
        @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <form action="{{ route('master.coa-items.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="card-body">

            <div class="row g-3 mb-3">

                {{-- SUB KOMPONEN (WAJIB karena DB NOT NULL) --}}
                <div class="col-md-6">
                    <label class="form-label">Sub Komponen <span class="text-danger">*</span></label>
                    <select name="sub_komponen_id" class="form-select @error('sub_komponen_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Sub Komponen --</option>
                        @foreach($subKomponens as $s)
                        <option value="{{ $s->id }}" @selected(old('sub_komponen_id')==$s->id)>
                            {{ $s->kode_subkomponen }} - {{ $s->nama_subkomponen }}
                        </option>
                        @endforeach
                    </select>
                    @error('sub_komponen_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- MAK --}}
                <div class="col-md-4">
                    <label class="form-label">MAK (Akun) <span class="text-danger">*</span></label>
                    <select name="mak_id" class="form-select @error('mak_id') is-invalid @enderror" required>
                        <option value="">-- Pilih MAK --</option>
                        @foreach($maks as $m)
                        <option value="{{ $m->id }}" @selected(old('mak_id')==$m->id)>
                            {{ $m->akun?->kode_akun ?? '-' }} - {{ $m->akun?->nama_akun ?? '-' }}
                            | {{ $m->nama_mak ?? '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('mak_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- TAHUN --}}
                <div class="col-md-2">
                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                    <input type="number"
                        name="tahun_anggaran"
                        value="{{ old('tahun_anggaran', date('Y')) }}"
                        class="form-control @error('tahun_anggaran') is-invalid @enderror"
                        min="2000" max="2100" required>
                    @error('tahun_anggaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- ADD ROW --}}
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="button" id="btnAdd" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Tambah Baris
                    </button>
                </div>
            </div>

            <div class="alert alert-info">
                <div class="fw-bold mb-1">Cara input Uraian COA (biar tidak keliru)</div>
                <div class="small">
                    <b>1) Item biasa (langsung di bawah MAK)</b>: tulis normal<br>
                    <code>Pengadaan Pelaporan</code><br><br>

                    <b>2) Jika ingin memberi penanda level (opsional)</b> — ini untuk bantu pengelompokan:<br>
                    <code>&gt; Biaya konsumsi rapat pembahasan</code> (Lv 1 = Pengelompokan, otomatis terkunci)<br>
                    <code>&gt;&gt; Kudapan/snack</code> (Lv 2 = Detail)<br><br>

                    <span class="text-muted">
                        Catatan: Jika pakai prefix <code>&gt;</code> (Lv 1), Vol/Satuan/Harga akan otomatis dikunci biar tidak salah input.
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="tblItems">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px" class="text-center">No</th>
                            <th style="min-width:420px">Uraian COA <span class="text-danger">*</span></th>
                            <th style="width:110px" class="text-center">Level</th>
                            <th style="width:110px" class="text-center">Vol</th>
                            <th style="width:140px">Satuan</th>
                            <th style="width:180px" class="text-end">Harga</th>
                            <th style="width:180px" class="text-end">Jumlah</th>
                            <th style="width:80px" class="text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $r)
                        <tr>
                            <td class="text-center row-no">{{ $i+1 }}</td>

                            <td>
                                <input type="text"
                                    name="items[{{ $i }}][uraian]"
                                    class="form-control uraian"
                                    value="{{ old("items.$i.uraian", $r['uraian'] ?? '') }}"
                                    placeholder="Contoh: Pengadaan Pelaporan / > Biaya konsumsi rapat pembahasan"
                                    required>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-secondary level-badge">Lv 0</span>
                            </td>

                            <td>
                                <input type="number"
                                    name="items[{{ $i }}][volume]"
                                    class="form-control volume text-center"
                                    value="{{ old("items.$i.volume", $r['volume'] ?? 1) }}"
                                    min="0">
                            </td>

                            <td>
                                <input type="text"
                                    name="items[{{ $i }}][satuan]"
                                    class="form-control satuan"
                                    style="min-width:120px"
                                    value="{{ old("items.$i.satuan", $r['satuan'] ?? 'Pcs') }}"
                                    placeholder="Pcs / Paket / Kali">
                            </td>

                            <td>
                                <input type="number"
                                    name="items[{{ $i }}][harga_satuan]"
                                    class="form-control harga text-end"
                                    value="{{ old("items.$i.harga_satuan", $r['harga_satuan'] ?? 0) }}"
                                    min="0" step="1">
                            </td>

                            <td class="text-end">
                                <input type="text" class="form-control text-end jumlah_display" value="0" readonly>
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btnRemove">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <div class="card-footer d-flex justify-content-end">
            <button class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(function() {

        function formatIDR(num) {
            return (Number(num || 0)).toLocaleString('id-ID');
        }

        function detectLevel(text) {
            const t = (text || '').trim();
            if (t.startsWith('>>')) return 2;
            if (t.startsWith('>')) return 1;
            return 0;
        }

        function recalcRow($tr) {
            const vol = Number($tr.find('.volume').val() || 0);
            const harga = Number($tr.find('.harga').val() || 0);
            const jumlah = vol * harga;
            $tr.find('.jumlah_display').val(formatIDR(jumlah));
        }

        // ✅ INI YANG DI-FIX: Lv 1 (>) jadi terkunci
        function applyRowRule($tr) {
            const uraian = $tr.find('.uraian').val() || '';
            const level = detectLevel(uraian);

            const $vol = $tr.find('.volume');
            const $sat = $tr.find('.satuan');
            const $harga = $tr.find('.harga');
            const $badge = $tr.find('.level-badge');
            const $jumlah = $tr.find('.jumlah_display');

            // reset row color
            $tr.removeClass('table-warning table-info table-light');

            if (level === 1) {
                // 🔒 PENGELOMPOKAN (>)
                $badge.text('Lv 1').removeClass().addClass('badge bg-warning text-dark level-badge');
                $tr.addClass('table-warning');

                $vol.val(0).prop('disabled', true);
                $sat.val('').prop('disabled', true);
                $harga.val(0).prop('disabled', true);
                $jumlah.val('0');
                return;
            }

            if (level === 2) {
                // DETAIL (>>)
                $badge.text('Lv 2').removeClass().addClass('badge bg-info text-dark level-badge');
                $tr.addClass('table-info');

                $vol.prop('disabled', false);
                $sat.prop('disabled', false);
                $harga.prop('disabled', false);

                recalcRow($tr);
                return;
            }

            // ITEM NORMAL (Lv 0)
            $badge.text('Lv 0').removeClass().addClass('badge bg-secondary level-badge');
            $vol.prop('disabled', false);
            $sat.prop('disabled', false);
            $harga.prop('disabled', false);
            recalcRow($tr);
        }

        function reIndex() {
            $('#tblItems tbody tr').each(function(idx) {
                $(this).find('.row-no').text(idx + 1);

                $(this).find('input[name*="[uraian]"]').attr('name', `items[${idx}][uraian]`);
                $(this).find('input[name*="[volume]"]').attr('name', `items[${idx}][volume]`);
                $(this).find('input[name*="[satuan]"]').attr('name', `items[${idx}][satuan]`);
                $(this).find('input[name*="[harga_satuan]"]').attr('name', `items[${idx}][harga_satuan]`);
            });
        }

        // init
        $('#tblItems tbody tr').each(function() {
            applyRowRule($(this));
        });

        $('#tblItems').on('input', '.uraian', function() {
            applyRowRule($(this).closest('tr'));
        });

        // hanya recalc kalau bukan lv1 (karena lv1 sudah return)
        $('#tblItems').on('input', '.volume,.harga', function() {
            applyRowRule($(this).closest('tr'));
        });

        $('#btnAdd').on('click', function() {
            const idx = $('#tblItems tbody tr').length;

            $('#tblItems tbody').append(`
            <tr>
                <td class="text-center row-no">${idx+1}</td>
                <td>
                    <input type="text" name="items[${idx}][uraian]" class="form-control uraian" required
                           placeholder="Contoh: Pengadaan Pelaporan / > Biaya konsumsi rapat pembahasan">
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary level-badge">Lv 0</span>
                </td>
                <td>
                    <input type="number" name="items[${idx}][volume]" class="form-control volume text-center" value="1" min="0">
                </td>
                <td>
                    <input type="text" name="items[${idx}][satuan]" class="form-control satuan" style="min-width:120px" value="Pcs"
                           placeholder="Pcs / Paket / Kali">
                </td>
                <td>
                    <input type="number" name="items[${idx}][harga_satuan]" class="form-control harga text-end" value="0" min="0" step="1">
                </td>
                <td class="text-end">
                    <input type="text" class="form-control text-end jumlah_display" value="0" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btnRemove"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `);

            const $last = $('#tblItems tbody tr:last');
            applyRowRule($last);
        });

        $(document).on('click', '.btnRemove', function() {
            const total = $('#tblItems tbody tr').length;
            if (total <= 1) {
                swal({
                    title: "Minimal 1 baris",
                    text: "Tidak bisa menghapus semua baris.",
                    icon: "warning"
                });
                return;
            }
            $(this).closest('tr').remove();
            reIndex();
        });
    });
</script>
@endpush

@endsection