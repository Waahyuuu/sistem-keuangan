@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
    @if(session('success'))
    <div class="toast align-items-center text-bg-success border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast align-items-center text-bg-warning border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">{{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-4">Transaksi</h3>
</div>

<form method="GET" class="mb-3 d-flex gap-2 align-items-center">
    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" style="max-width:200px;">

    <button class="btn btn-primary">
        Filter
    </button>

    <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">
        Hari Ini
    </a>
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

        {{-- LEFT --}}
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
                    {{ $trx->rekening->name_rek ?? '-' }}
                    →
                    {{ $trx->rekeningTujuan->name_rek ?? '-' }}
                    @else
                    {{ $trx->departemen->name_dep ?? '-' }}
                    •
                    {{ $trx->program->name_prog ?? '-' }}
                    @endif
                </small>
            </div>

        </div>

        {{-- RIGHT --}}
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
                {{ $trx->rekening->name_rek ?? '-' }}
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
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#utang">
                            Utang
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#piutang">
                            Piutang
                        </button>
                    </li>
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
                        <form method="POST" action="{{ route('transaksi.store') }}">
                            @csrf
                            <input type="hidden" name="type_transaksi" value="transfer">

                            <div class="mb-3">
                                <label class="form-label">Rekening Asal</label>
                                <select name="rekening_id" id="rekeningAsal" class="form-select" required>
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
                                <select name="rekening_tujuan_id" id="rekeningTujuan" class="form-select" required>
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

                            <button class="btn btn-primary mt-2">Simpan Transfer</button>
                        </form>
                    </div>

                    {{-- Utang --}}
                    <div class="tab-pane fade" id="utang">
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
                    </div>

                    {{-- Piutang --}}
                    <div class="tab-pane fade" id="piutang">
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
                    </div>

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

<script>
    function openImagePreview(src) {
    const overlay = document.getElementById('imagePreviewOverlay');
    const img = document.getElementById('previewImage');

    img.src = src;
    overlay.style.display = 'flex';

    setTimeout(() => {
        overlay.classList.add('show');
    }, 10);

    document.body.style.overflow = 'hidden';
}

function closeImagePreview() {
    const overlay = document.getElementById('imagePreviewOverlay');

    overlay.classList.remove('show');
    overlay.classList.add('closing');

    setTimeout(() => {
        overlay.classList.remove('closing');
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 250);
}

// klik background untuk close
document.getElementById('imagePreviewOverlay')
    .addEventListener('click', function(e) {
        if (e.target === this) {
            closeImagePreview();
        }
});
</script>

@endsection