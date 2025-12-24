@extends('layouts.index')

@section('content')
<div class="container-fluid">

    <a href="{{ url()->previous() }}" class="btn btn-light mb-3">
        ← Kembali
    </a>

    {{-- Header --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5>{{ $header->nama_kegiatan }}</h5>

            <div class="row">
                <div class="col-md-4">
                    <small>Kode PLO</small>
                    <div class="fw-bold">{{ $header->kode_unik_plo }}</div>
                </div>
                <div class="col-md-4">
                    <small>Status</small>
                    <div class="fw-bold">{{ $header->status_flow }}</div>
                </div>
                <div class="col-md-4">
                    <small>Total</small>
                    <div class="fw-bold text-success">
                        Rp {{ number_format($header->total, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Line --}}
    <div class="card mb-4">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Penerima</th>
                        <th>Uraian</th>
                        <th>Jumlah</th>
                        <th>PPN</th>
                        <th>PPh</th>
                        <th>Total Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($header->lines as $line)
                    <tr>
                        <td>{{ $line->penerima_penyedia }}</td>
                        <td>{{ $line->uraian }}</td>
                        <td>Rp {{ number_format($line->jumlah,0,',','.') }}</td>
                        <td>Rp {{ number_format($line->ppn,0,',','.') }}</td>
                        <td>Rp {{ number_format($line->pph_total,0,',','.') }}</td>
                        <td class="fw-bold">
                            Rp {{ number_format($line->total_bersih,0,',','.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Timeline Status --}}
    <div class="card">
        <div class="card-body">
            <h6>Riwayat Status</h6>
            <ul class="list-group">
                @foreach($header->logs as $log)
                <li class="list-group-item">
                    <strong>{{ $log->actor_role }}</strong>
                    — {{ $log->status }}
                    <br>
                    <small class="text-muted">
                        {{ $log->created_at->format('d M Y H:i') }}
                    </small>
                    <div>{{ $log->catatan }}</div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

</div>
@endsection