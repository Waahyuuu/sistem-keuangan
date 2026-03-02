@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<h3 class="mb-4">Dashboard</h3>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-body">
                <h6 class="text-muted">Total Saldo</h6>
                <h3 class="fw-bold text-primary">
                    Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
</div>

{{-- Saldo Per Rekening --}}
<div class="row">
    @forelse($rekenings as $rek)
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm rounded-4 border-0 h-100">
            <div class="card-body">
                <h6 class="fw-semibold">
                    {{ $rek->name_rek }}
                </h6>

                <p class="mb-1 text-muted">
                    Saldo Saat Ini
                </p>

                <h5 class="fw-bold
                        {{ $rek->saldo_akhir < 0 ? 'text-danger' : 'text-success' }}">
                    Rp {{ number_format($rek->saldo_akhir, 0, ',', '.') }}
                </h5>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            Belum ada rekening terdaftar
        </div>
    </div>
    @endforelse
</div>

@endsection