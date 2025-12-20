@extends('layouts.index')

@section('page-header')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h4 class="page-title mb-1">Edit COA (Bulk)</h4>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Master Data</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('master.coa-items.index') }}">COA</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><span>Edit</span></li>
            </ul>
        </div>

        <a href="{{ route('master.coa-items.index', ['mak_id' => $mak->id, 'tahun' => $tahun]) }}" class="btn btn-light btn-sm">
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

<form action="{{ route('master.coa-items.bulk-update') }}" method="POST" autocomplete="off">
@csrf

<div class="card">
    <div class="card-body">

        <input type="hidden" name="mak_id" value="{{ $mak->id }}">
        <input type="hidden" name="tahun_anggaran" value="{{ $tahun }}">

        <div class="alert alert-info mb-3">
            <div class="fw-bold">MAK / Akun</div>
            <div>
                <strong>{{ $mak->akun?->kode_akun }}</strong> - {{ $mak->akun?->nama_akun }}
                <br>
                MAK: <strong>{{ $mak->kode_mak }}</strong> {{ $mak->nama_mak }}
                <br>
                Tahun: <strong>{{ $tahun }}</strong>
            </div>
            <hr class="my-2">
            <div class="small">
                <b>Cara input Uraian (biar tidak keliru)</b><br>
                1) Item biasa (langsung di bawah MAK): tulis normal<br>
                <code>Pengadaan Pelaporan</code><br><br>

                2) Penanda level (opsional):<br>
                <code>&gt; Biaya konsumsi rapat pembahasan</code> (Lv 1) → dianggap <b>pengelompokan</b> (dikunci otomatis)<br>
                <code>&gt;&gt; Kudapan/snack</code> (Lv 2) → tetap bisa isi angka<br>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-2">
            <button type="button" id="btnAdd" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Tambah Baris
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="tblEdit">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px" class="text-center">No</th>
                        <th style="min-width:420px">Uraian</th>
                        <th style="width:110px" class="text-center">Level</th>
                        <th style="width:110px" class="text-center">Vol</th>
                        <th style="width:140px">Satuan</th>
                        <th style="width:180px" class="text-end">Harga</th>
                        <th style="width:180px" class="text-end">Jumlah</th>
                        <th style="width:110px" class="text-center">Hapus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coaItems as $i => $c)
                    <tr>
                        <td class="text-center row-no">{{ $i+1 }}</td>

                        <td>
                            <input type="hidden" name="items[{{ $i }}][id]" value="{{ $c->id }}">
                            <input type="text"
                                   name="items[{{ $i }}][uraian]"
                                   class="form-control uraian"
                                   value="{{ old("items.$i.uraian", str_repeat('>', (int)$c->level).' '.$c->uraian) }}"
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
                                   value="{{ old("items.$i.volume", (int)$c->volume) }}" min="0">
                        </td>

                        <td>
                            <input type="text"
                                   name="items[{{ $i }}][satuan]"
                                   class="form-control satuan"
                                   style="min-width:120px"
                                   value="{{ old("items.$i.satuan", $c->satuan) }}"
                                   placeholder="Pcs / Paket / Kali">
                        </td>

                        <td>
                            <input type="number"
                                   name="items[{{ $i }}][harga_satuan]"
                                   class="form-control harga text-end"
                                   value="{{ old("items.$i.harga_satuan", (float)$c->harga_satuan) }}"
                                   min="0" step="1">
                        </td>

                        <td class="text-end">
                            <input type="text" class="form-control text-end jumlah_display"
                                   value="{{ number_format((float)$c->jumlah, 0, ',', '.') }}" readonly>
                        </td>

                        <td class="text-center">
                            <label class="d-flex align-items-center justify-content-center gap-2 m-0">
                                <input type="checkbox" name="items[{{ $i }}][_delete]" value="1">
                                <span class="small text-muted">hapus</span>
                            </label>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <div class="card-footer d-flex justify-content-end">
        <button class="btn btn-primary">
            <i class="fa fa-save"></i> Simpan Perubahan
        </button>
    </div>
</div>

</form>

@push('scripts')
<script>
$(function(){

    function formatIDR(num){
        return (Number(num || 0)).toLocaleString('id-ID');
    }

    function detectLevel(text) {
        const t = (text || '').trim();
        if (t.startsWith('>>')) return 2;
        if (t.startsWith('>')) return 1;
        return 0;
    }

    function recalcRow($tr){
        const vol = Number($tr.find('.volume').val() || 0);
        const harga = Number($tr.find('.harga').val() || 0);
        $tr.find('.jumlah_display').val(formatIDR(vol * harga));
    }

    function applyRowRule($tr){
        const uraian = $tr.find('.uraian').val() || '';
        const level = detectLevel(uraian);

        const $vol   = $tr.find('.volume');
        const $sat   = $tr.find('.satuan');
        const $harga = $tr.find('.harga');
        const $badge = $tr.find('.level-badge');

        // reset row style
        $tr.removeClass('table-warning table-info table-light');

        // default enable
        $vol.prop('disabled', false);
        $sat.prop('disabled', false);
        $harga.prop('disabled', false);

        if (level === 1) {
            // Lv1 = pengelompokan -> lock
            $badge.text('Lv 1').removeClass().addClass('badge bg-warning text-dark level-badge');
            $tr.addClass('table-warning');

            $vol.val(0).prop('disabled', true);
            $sat.val('').prop('disabled', true);
            $harga.val(0).prop('disabled', true);

            $tr.find('.jumlah_display').val('0');
            return;
        }

        if (level === 2) {
            $badge.text('Lv 2').removeClass().addClass('badge bg-info text-dark level-badge');
            $tr.addClass('table-info');
            recalcRow($tr);
            return;
        }

        $badge.text('Lv 0').removeClass().addClass('badge bg-secondary level-badge');
        recalcRow($tr);
    }

    function reIndex(){
        $('#tblEdit tbody tr').each(function(idx){
            $(this).find('.row-no').text(idx+1);

            $(this).find('input[name*="[id]"]').attr('name', `items[${idx}][id]`);
            $(this).find('input[name*="[uraian]"]').attr('name', `items[${idx}][uraian]`);
            $(this).find('input[name*="[volume]"]').attr('name', `items[${idx}][volume]`);
            $(this).find('input[name*="[satuan]"]').attr('name', `items[${idx}][satuan]`);
            $(this).find('input[name*="[harga_satuan]"]').attr('name', `items[${idx}][harga_satuan]`);

            $(this).find('input[type="checkbox"]').attr('name', `items[${idx}][_delete]`);
        });
    }

    // init semua row (apply lock + warna + jumlah)
    $('#tblEdit tbody tr').each(function(){
        applyRowRule($(this));
    });

    // uraian berubah -> apply rule
    $('#tblEdit').on('input', '.uraian', function(){
        applyRowRule($(this).closest('tr'));
    });

    // angka berubah -> hitung ulang (kalau tidak dikunci)
    $('#tblEdit').on('input', '.volume,.harga', function(){
        applyRowRule($(this).closest('tr'));
    });

    $('#btnAdd').on('click', function(){
        const idx = $('#tblEdit tbody tr').length;

        $('#tblEdit tbody').append(`
            <tr>
                <td class="text-center row-no">${idx+1}</td>

                <td>
                    <input type="hidden" name="items[${idx}][id]" value="">
                    <input type="text" name="items[${idx}][uraian]" class="form-control uraian"
                           placeholder="Contoh: Pengadaan Pelaporan / > Biaya konsumsi rapat pembahasan" required>
                </td>

                <td class="text-center">
                    <span class="badge bg-secondary level-badge">Lv 0</span>
                </td>

                <td>
                    <input type="number" name="items[${idx}][volume]" class="form-control volume text-center" value="1" min="0">
                </td>

                <td>
                    <input type="text" name="items[${idx}][satuan]" class="form-control satuan" style="min-width:120px" value="Pcs">
                </td>

                <td>
                    <input type="number" name="items[${idx}][harga_satuan]" class="form-control harga text-end" value="0" min="0" step="1">
                </td>

                <td class="text-end">
                    <input type="text" class="form-control text-end jumlah_display" value="0" readonly>
                </td>

                <td class="text-center">
                    <label class="d-flex align-items-center justify-content-center gap-2 m-0">
                        <input type="checkbox" name="items[${idx}][_delete]" value="1">
                        <span class="small text-muted">hapus</span>
                    </label>
                </td>
            </tr>
        `);

        const $last = $('#tblEdit tbody tr:last');
        applyRowRule($last);
    });

});
</script>
@endpush

@endsection
