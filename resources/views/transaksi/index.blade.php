@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-4">Transaksi</h3>
</div>

<form method="GET" id="filterForm" class="mb-3 d-flex gap-2 align-items-center">

    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" style="max-width:200px;">

    <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
        Hari Ini
    </a>

    <span id="loading" class="ms-2 d-none">
        <div class="spinner-border spinner-border-sm text-primary"></div>
    </span>

</form>

<div class="d-flex justify-content-between align-items-center mb-4">

    @php
    \Carbon\Carbon::setLocale('id');
    $tgl = \Carbon\Carbon::parse($tanggal);
    $isToday = $tgl->isToday();
    @endphp

    <div>
        <h4 class="fw-bold mb-1">
            @if($isToday)
            Hari Ini
            @else
            {{ $tgl->translatedFormat('l') }}
            @endif
        </h4>

        <small class="text-muted">
            @if($isToday)
            {{ $tgl->translatedFormat('l, d M Y') }}
            @else
            {{ $tgl->translatedFormat('d M Y') }}
            @endif
        </small>
    </div>

    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTransaksi">
        + Add Transaksi
    </button>

</div>

{{-- Daftar Transaksi --}}
@forelse($transaksis as $trx)

@php
$isPemasukan = $trx->type_transaksi === 'pemasukan';
$isPengeluaran = $trx->type_transaksi === 'pengeluaran';
$isTransfer = $trx->type_transaksi === 'transfer';
@endphp

<div class="modern-card" data-id="{{ $trx->id }}" data-bs-toggle="modal" data-bs-target="#modalDetailTransaksi">

    <div class="d-flex justify-content-between align-items-center">

        <div class="d-flex align-items-center gap-4">

            <div class="modern-icon
                {{ $isPemasukan ? 'icon-success' : '' }}
                {{ $isPengeluaran ? 'icon-danger' : '' }}
                {{ $isTransfer ? 'icon-primary' : '' }}
            ">

                @if($isPemasukan)
                <i class="bi bi-arrow-down-left"></i>
                @elseif($isPengeluaran)
                <i class="bi bi-arrow-up-right"></i>
                @else
                <i class="bi bi-arrow-left-right"></i>
                @endif
            </div>

            <div>
                <h6 class="fw-bold mb-1">
                    @if($isTransfer)
                    {{ $trx->keterangan ?? 'Internal Transfer' }}
                    @else
                    {{ $trx->keterangan ?? 'Tanpa Keterangan' }}
                    @endif
                </h6>

                <small class="text-muted">
                    @if($isTransfer)

                    {{ $trx->rekening_nama
                    ?? optional($trx->rekening)->name_rek
                    ?? '-' }}

                    →

                    {{ $trx->rekening_tujuan_nama
                    ?? optional($trx->rekeningTujuan)->name_rek
                    ?? '-' }}

                    @else

                    {{ $trx->departemen_nama
                    ?? optional($trx->departemen)->name_dep
                    ?? '-' }}

                    •

                    {{ $trx->program_nama
                    ?? optional($trx->program)->name_prog
                    ?? '-' }}

                    @endif
                </small>
            </div>

        </div>

        <div class="text-end">

            <div class="modern-amount
                {{ $isPemasukan ? 'text-success' : '' }}
                {{ $isPengeluaran ? 'text-danger' : '' }}
            ">

                @if($isPemasukan)
                + Rp {{ number_format($trx->nominal_transaksi, 0, ',', '.') }}
                @elseif($isPengeluaran)
                - Rp {{ number_format($trx->nominal_transaksi, 0, ',', '.') }}
                @else
                Rp {{ number_format($trx->nominal_transaksi, 0, ',', '.') }}
                @endif
            </div>

            <div class="modern-badge">
                @if($isTransfer)
                Internal Transfer
                @else
                {{ $trx->rekening_nama ?? '-' }}
                @endif
            </div>

        </div>

    </div>
</div>

@empty
@if($isFilter && $tanggal != now()->toDateString())
<div class="alert alert-light text-center rounded-4">
    Belum ada transaksi ditanggal ini
</div>
@else
<div class="alert alert-light text-center rounded-4">
    Belum ada transaksi
</div>
@endif
@endforelse

{{-- Modal Transaksi --}}
<div class="modal fade" id="modalTransaksi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="transaksiTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pemasukan">
                            Pemasukan
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#pengeluaran">
                            Pengeluaran
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transfer">
                            Transfer
                        </button>
                    </li>
                    {{-- <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#utang">
                            Utang
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#piutang">
                            Piutang
                        </button>
                    </li> --}}
                </ul>

                <div class="tab-content pt-3">

                    {{-- Pemasukan --}}
                    <div class="tab-pane fade show active" id="pemasukan">
                        <form method="POST" action="{{ route('transaksi.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type_transaksi" value="pemasukan">
                            @include('transaksi.partials.form')
                            <button class="btn btn-primary mt-2">Simpan Pemasukan</button>
                        </form>
                    </div>

                    {{-- Pengeluaran --}}
                    <div class="tab-pane fade" id="pengeluaran">
                        <form method="POST" action="{{ route('transaksi.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type_transaksi" value="pengeluaran">
                            @include('transaksi.partials.form')
                            <button class="btn btn-primary mt-2">Simpan Pengeluaran</button>
                        </form>
                    </div>

                    {{-- Transfer --}}
                    <div class="tab-pane fade" id="transfer">
                        <form method="POST" action="{{ route('transaksi.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type_transaksi" value="transfer">

                            <div class="mb-3">
                                <label class="form-label">Tanggal Transaksi</label>
                                <input type="date" name="tgl_transaksi" class="form-control" value="{{ date('Y-m-d') }}"
                                    max="{{ date('Y-m-d') }}" required>
                                <small class="text-muted">
                                    Tidak boleh melebihi hari ini
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Rekening Asal</label>
                                <select name="rekening_id" id="rekening_asal" class="form-select" required>
                                    <option value="">-- Pilih Rekening Asal --</option>
                                    @foreach($rekenings as $rek)
                                    <option value="{{ $rek->id }}">
                                        {{ $rek->name_rek }} -
                                        Saldo: {{ number_format($rek->saldo_akhir,0,',','.') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Rekening Tujuan</label>
                                <select name="rekening_tujuan_id" id="rekening_tujuan" class="form-select" required>
                                    <option value="">-- Pilih Rekening Tujuan --</option>
                                    @foreach($rekenings as $rek)
                                    <option value="{{ $rek->id }}">
                                        {{ $rek->name_rek }} -
                                        Saldo: {{ number_format($rek->saldo_akhir,0,',','.') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nominal</label>
                                <input type="number" name="nominal_transaksi" class="form-control" min="1" step="1"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control"></textarea>
                            </div>

                            {{-- Upload Bukti --}}
                            <div class="mb-3">
                                <label class="form-label">Upload Bukti Transfer</label>
                                <input type="file" name="bukti_nota" class="form-control" accept="image/*,.pdf">
                                <small class="text-muted">
                                    Format: JPG, PNG, PDF (Max 2MB)
                                </small>
                            </div>

                            <button class="btn btn-primary mt-2">Simpan Transfer</button>
                        </form>
                    </div>

                    {{-- Utang --}}
                    {{-- <div class="tab-pane fade" id="utang">
                        <form method="POST" action="{{ route('transaksi.store') }}">
                            @csrf
                            <input type="hidden" name="type_transaksi" value="utang">

                            <div class="mb-3">
                                <label class="form-label">Nominal</label>
                                <input type="number" name="nominal_transaksi" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control"></textarea>
                            </div>

                            <button class="btn btn-primary mt-2">Simpan Utang</button>
                        </form>
                    </div> --}}

                    {{-- Piutang --}}
                    {{-- <div class="tab-pane fade" id="piutang">
                        <form method="POST" action="{{ route('transaksi.store') }}">
                            @csrf
                            <input type="hidden" name="type_transaksi" value="piutang">

                            <div class="mb-3">
                                <label class="form-label">Nominal</label>
                                <input type="number" name="nominal_transaksi" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control"></textarea>
                            </div>

                            <button class="btn btn-primary mt-2">Simpan Piutang</button>
                        </form>
                    </div> --}}

                </div>

            </div>

        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetailTransaksi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Loading...
            </div>
        </div>
    </div>
</div>

@endsection