@extends('layouts.index')
@section('page-header')
<div class="page-header">
    <h4 class="page-title">Ubah Ini</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Pages</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Starter Page</a>
        </li>
    </ul>
</div>
@endsection
@section('content')
<div style="margin:12px 0;">
    <a href="{{ route('master.pagu-lines.create') }}">+ Tambah Pagu MAK</a>
</div>

<form method="GET" style="margin-bottom:12px; display:flex; gap:8px; flex-wrap:wrap;">
    <select name="sub_komponen_id">
        <option value="">-- Semua Sub Komponen --</option>
        @foreach($subKomponens as $s)
        <option value="{{ $s->id }}" @selected((string)$subKomponenId===(string)$s->id)>
            {{ $s->kode_subkomponen }} - {{ $s->nama_subkomponen }}
        </option>
        @endforeach
    </select>

    <select name="mak_id">
        <option value="">-- Semua MAK --</option>
        @foreach($maks as $m)
        <option value="{{ $m->id }}" @selected((string)$makId===(string)$m->id)>
            {{ $m->kode_mak }} - {{ $m->nama_mak }}
        </option>
        @endforeach
    </select>

    <input type="number" name="tahun" placeholder="Tahun" value="{{ $tahun }}">
    <button type="submit">Filter</button>
    <a href="{{ route('master.pagu-lines.index') }}">Reset</a>
</form>

<table id="dtPagu" class="display" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Sub Komponen</th>
            <th>MAK</th>
            <th>Pagu MAK</th>
            <th>Tahun</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($paguLines as $i => $p)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $p->subKomponen?->kode_subkomponen }}</td>
            <td>{{ $p->mak?->kode_mak }}</td>
            <td>{{ number_format((float)$p->pagu_mak, 0, ',', '.') }}</td>
            <td>{{ $p->tahun_anggaran }}</td>
            <td>
                <a href="{{ route('master.pagu-lines.edit', $p) }}">Edit</a>
                <form action="{{ route('master.pagu-lines.destroy', $p) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
                    @csrf @method('DELETE')
                    <button type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#dtPagu').DataTable({
            pageLength: 25,
            order: [
                [3, 'desc']
            ]
        });
    });
</script>
@endpush