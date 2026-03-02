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
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTransaksi">
        + Tambah Transaksi
    </button>
</div>

<!-- Daftar Transaksi -->

@forelse($transaksis as $trx)

<div class="card bg-light shadow-sm rounded-4 card-hover p-3 mb-3">
    <div class="d-flex justify-content-between">
        <div>
            <h6 class="fw-bold">{{ ucfirst($trx->type_transaksi) }}</h6>
            <small>{{ $trx->keterangan ?? '-' }}</small>
        </div>
        <div class="text-end">
            <span class="fw-bold text-primary">
                Rp {{ number_format($trx->nominal_transaksi, 0, ',', '.') }}
            </span>
            <br>
            <small>{{ $trx->tgl_transaksi->format('d M Y') }}</small>
        </div>
    </div>

    <div class="mt-2">
        <span class="badge bg-secondary">
            Departemen: {{ $trx->departemen->name_dep ?? '-' }}
        </span>
        <span class="badge bg-info">
            Program: {{ $trx->program->name_prog ?? '-' }}
        </span>
        <span class="badge bg-warning text-dark">
            Kategori: {{ $trx->kategori->name_ktgr ?? '-' }}
        </span>
    </div>

    <div class="mt-3 d-flex justify-content-end gap-2">
        <button class="btn btn-sm btn-outline-info detail-btn" data-id="{{ $trx->id }}" data-bs-toggle="modal"
            data-bs-target="#modalDetailTransaksi">
            Detail
        </button>
    </div>
</div>
@empty
<div class="col-12">
    <div class="alert alert-info text-center">Belum ada transaksi</div>
</div>
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

@endsection