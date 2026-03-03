@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    .wallet-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        font-size: 20px;
    }

    .dashboard-card {
        border-radius: 24px;
        padding: 28px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .card-blue {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border-radius: 28px;
        padding: 32px;
        position: relative;
        overflow: hidden;
    }

    .card-blue small {
        opacity: .85;
    }

    .stat-badge {
        background: #eef2ff;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
    }

.rekening-card {
    flex: 0 0 280px;
    height: 150px; /* lebih pendek */

    border-radius: 18px;
    padding: 16px; /* lebih tipis */
    background: #ffffff;

    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

    .wallet-icon {
        width: 38px;
        height: 38px;
        font-size: 18px;
        border-radius: 100%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1d4ed8;
    }

    .rekening-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        margin: 4px 0;
    }

    .rekening-scroll-wrapper {
        max-width: calc(280px * 4 + 24px * 3);
        overflow-x: auto;
        margin: 0 auto;
        /* biar center */
    }

    .rekening-scroll-wrapper::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari */
    }

    .rekening-scroll {
        display: flex;
        gap: 24px;
        scroll-snap-type: x mandatory;
        align-items: stretch;
    }

    .rekening-scroll-wrapper::-webkit-scrollbar {
        height: 8px;
    }

    .rekening-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .rekening-card h6 {
        font-size: 14px;
        margin-bottom: 2px;
    }

    .rekening-card small {
        font-size: 12px;
    }

    .rekening-card h5 {
        font-size: 16px;
    }

    .stat-badge {
        font-size: 12px;
        padding: 4px 12px;
    }
</style>

<h3 class="mb-4 fw-bold">Dashboard</h3>

{{-- TOP SUMMARY --}}
<div class="row mb-4 g-4">

    {{-- TOTAL SALDO --}}
    <div class="col-lg-4">
        <div class="card-blue h-100">
            <small>Total Saldo Aktif</small>
            <h2 class="fw-bold mt-2">
                Rp {{ number_format($totalSaldo, 0, ',', '.') }}
            </h2>

            <div class="mt-3">
                @php
                $naik = $persenPemasukan >= 0;
                @endphp

                <span class="badge bg-light rounded-pill px-3 py-2 
                    {{ $naik ? 'text-success' : 'text-danger' }}">
                    {{ $naik ? '↑' : '↓' }}
                    {{ abs($persenPemasukan) }}% dari bulan lalu
                </span>
            </div>
        </div>
    </div>

    {{-- PEMASUKAN --}}
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-badge mb-3">Bulan Ini</div>
                    <h6 class="text-muted">Total Pemasukan</h6>
                    <h3 class="fw-bold">
                        Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}
                    </h3>
                    <small class="{{ $persenPemasukan >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $persenPemasukan >= 0 ? '↑' : '↓' }}
                        {{ abs($persenPemasukan) }}% dari bulan lalu
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- PENGELUARAN --}}
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-badge mb-3 bg-danger-subtle text-danger">
                        Bulan Ini
                    </div>
                    <h6 class="text-muted">Total Pengeluaran</h6>
                    <h3 class="fw-bold">
                        Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                    </h3>
                    <small class="{{ $persenPengeluaran >= 0 ? 'text-danger' : 'text-success' }}">
                        {{ $persenPengeluaran >= 0 ? '↑' : '↓' }}
                        {{ abs($persenPengeluaran) }}% dari bulan lalu
                    </small>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SALDO REKENING --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold">Saldo Rekening</h5>
    <a href="master-data?tab=rekening" class="fw-semibold text-primary text-decoration-none">
        Lihat Semua
    </a>
</div>

<div class="rekening-scroll-wrapper">
    <div class="rekening-scroll d-flex gap-3">

        @forelse($rekenings as $rek)
        <div class="rekening-card">

            {{-- HEADER DENGAN ICON --}}
            <div class="d-flex align-items-center justify-content-between mb-2">

                <div class="d-flex align-items-center gap-2">

                    <div class="wallet-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <div>
                        <h6 class="fw-bold mb-0">
                            {{ $rek->name_rek }}
                        </h6>
                        <small class="text-muted">
                            {{ $rek->no_rek }}
                        </small>
                    </div>

                </div>

                <span class="badge bg-success-subtle text-success rounded-pill">
                    Aktif
                </span>
            </div>

            <div class="rekening-divider"></div>

            {{-- SALDO --}}
            <div>
                <small class="text-muted">Total Dana</small>
                <h5 class="fw-bold mt-0
                    {{ $rek->saldo_akhir < 0 ? 'text-danger' : '' }}">
                    Rp {{ number_format($rek->saldo_akhir, 0, ',', '.') }}
                </h5>
            </div>

        </div>

        @empty
        <div class="col-12">
            <div class="alert alert-light text-center rounded-4">
                Belum ada rekening terdaftar
            </div>
        </div>
        @endforelse

    </div>
</div>
@endsection