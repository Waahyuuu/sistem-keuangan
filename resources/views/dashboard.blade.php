@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<h3 class="mb-4 fw-bold">Dashboard</h3>

<div class="row mb-4 g-4">

    {{-- TOTAL SALDO --}}
    <div class="col-lg-4">
        <div class="card-blue h-100 p-4">
            <div class="d-flex justify-content-between align-items-start">

                <div>
                    <h6 class="fw-semibold text-white-50">Total Saldo Aktif</h6>

                    <h2 class="fw-bold text-white mt-2 mb-3">
                        Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                    </h2>

                    @php
                    $naik = $persenPemasukan >= 0;
                    @endphp

                    <span class="badge bg-light rounded-pill px-3 py-2 
                        {{ $naik ? 'text-success' : 'text-danger' }}">
                        {{ $naik ? '↑' : '↓' }}
                        {{ abs($persenPemasukan) }}% dari bulan lalu
                    </span>
                </div>

                <div class="saldo-icon">
                    <i class="bi bi-wallet-fill"></i>
                </div>

            </div>
        </div>
    </div>


    {{-- PEMASUKAN --}}
    <div class="col-lg-4">
        <div class="dashboard-card h-100 p-4">

            <div class="d-flex justify-content-between align-items-start">
                <div class="icon-box bg-success-subtle text-success">
                    <i class="bi bi-arrow-down-left"></i>
                </div>

                <span class="stat-badge">Bulan Ini</span>
            </div>

            <div class="mt-4">
                <h6 class="text-muted fw-semibold">Total Pemasukan</h6>

                <h2 class="fw-bold mb-2">
                    Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}
                </h2>
            </div>

        </div>
    </div>


    {{-- PENGELUARAN --}}
    <div class="col-lg-4">
        <div class="dashboard-card h-100 p-4">

            <div class="d-flex justify-content-between align-items-start">
                <div class="icon-box bg-danger-subtle text-danger">
                    <i class="bi bi-arrow-up-right"></i>
                </div>

                <span class="stat-badge">Bulan Ini</span>
            </div>

            <div class="mt-4">
                <h6 class="text-muted fw-semibold">Total Pengeluaran</h6>

                <h2 class="fw-bold mb-2">
                    Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                </h2>
            </div>

        </div>
    </div>

</div>

{{-- SALDO REKENING --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold">Saldo Rekening</h5>
    <a href="master-data?tab=rekening" class="fw-semibold text-primary text-decoration-none small">
        Lihat Semua
    </a>
</div>

<div class="rekening-scroll-wrapper">
    <div class="rekening-scroll d-flex gap-3 ">

        @forelse($rekenings as $rek)
        <div class="rekening-card">
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

{{-- ANALITIK ARUS KAS --}}
<div class="mt-5 p-4 rounded-4 shadow-sm position-relative bg-white">

    <!-- LOADING -->
    <div id="chartLoading" class="chart-loading d-none">
        <div class="spinner-border text-primary"></div>
        <span class="ms-2">Memuat data...</span>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Analitik Arus Kas</h5>
            <small class="text-muted">
                Perbandingan Pemasukan & Pengeluaran
            </small>
        </div>

        <form method="GET" id="filterForm" class="d-flex gap-2">

            {{-- DEPARTEMEN --}}
            <select name="departemen" class="form-select">
                <option value="all">All</option>
                @foreach($departemens as $dep)
                <option value="{{ $dep->id }}" {{ $selectedDepartemen==$dep->id ? 'selected' : '' }}>
                    {{ $dep->name_dep }}
                </option>
                @endforeach
            </select>

            {{-- TAHUN --}}
            <select name="tahun" class="form-select">
                @foreach($years as $year)
                <option value="{{ $year }}" {{ $selectedYear==$year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
                @endforeach
            </select>

        </form>
    </div>

    <canvas id="arusKasChart" height="90"></canvas>
</div>

{{-- TRANSAKSI TERBARU --}}
<div class="transaksi-card mt-4">

    {{-- HEADER --}}
    <div class="transaksi-header">
        <h5>Transaksi Terbaru</h5>

        <a class="fw-semibold text-primary text-decoration-none small" href="{{ route('transaksi.index') }}">
            Lihat Semua
        </a>
    </div>

    {{-- TABLE HEADER --}}
    <div class="transaksi-table-header">
        <span>Keterangan</span>
        <span>Nominal</span>
    </div>

    {{-- LIST TRANSAKSI --}}
    @forelse($transaksiTerbaru as $trx)

    <div class="transaksi-item">

        <div class="trx-left">

            <div class="trx-icon
                {{ $trx->type_transaksi == 'pemasukan' ? 'icon-masuk' : 'icon-keluar' }}">

                <i class="bi 
                {{ $trx->type_transaksi == 'pemasukan' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }}">
                </i>

            </div>

            <div>
                <div class="trx-title">
                    {{ \Illuminate\Support\Str::limit($trx->keterangan,30) }}
                </div>

                <small>
                    {{ $trx->created_at->format('d M, H:i') }}
                </small>
            </div>

        </div>

        <div class="trx-nominal
            {{ $trx->type_transaksi == 'pemasukan' ? 'text-success' : 'text-danger' }}">

            {{ $trx->type_transaksi == 'pemasukan' ? '+' : '-' }}
            Rp {{ number_format($trx->nominal_transaksi,0,',','.') }}

        </div>

    </div>

    @empty
    <div class="p-4 text-center text-muted">
        Belum ada transaksi
    </div>
    @endforelse

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('arusKasChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            'Jan','Feb','Mar','Apr','Mei','Jun',
            'Jul','Agu','Sep','Okt','Nov','Des'
        ],
        datasets: [
            {
                label: 'Pemasukan',
                data: @json($dataPemasukan),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.2)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#22c55e'
            },
            {
                label: 'Pengeluaran',
                data: @json($dataPengeluaran),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,0.2)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#ef4444'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

const filterForm = document.getElementById('filterForm');

if (filterForm) {

    const selects = filterForm.querySelectorAll('select');
    const loading = document.getElementById('chartLoading');

    selects.forEach(select => {

        select.addEventListener('change', () => {

            if (loading) {
                loading.classList.remove('d-none');
            }

            setTimeout(() => {
                filterForm.submit();
            }, 200);

        });

    });

}
</script>
@endsection