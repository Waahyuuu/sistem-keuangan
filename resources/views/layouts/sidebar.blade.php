<div class="bg-white border-end shadow-sm p-3 d-flex flex-column sidebar-fixed">

    <h4 class="text-primary fw-bold text-center">
        <i class="bi bi-cash-coin me-2"></i> Keuangan
    </h4>

    <hr>

    <!-- MENU -->
    <ul class="nav flex-column sidebar-menu">

        <li class="nav-item mb-2">
            <a href="{{ route('dashboard') }}"
                class="nav-link text-dark {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('transaksi.index') }}"
                class="nav-link text-dark {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-left-right me-2"></i> Transaksi
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('master.data') }}"
                class="nav-link text-dark {{ request()->routeIs('master.*') ? 'active' : '' }}">
                <i class="bi bi-collection me-2"></i> Master Data
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('laporan') }}"
                class="nav-link text-dark {{ request()->routeIs('laporan') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text me-2"></i> Laporan
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('pengguna.index') }}"
                class="nav-link text-dark {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i> Pengaturan Akun
            </a>
        </li>

    </ul>

    <hr>

    <!-- SESSION CARD -->
    <div class="session-card mb-3">

        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-clock-history me-2"></i>
            <small class="fw-semibold">Sisa Waktu Sesi</small>
        </div>

        <h4 id="session-timer" class="fw-bold text-primary" style="visibility:hidden">
            00:00:00
        </h4>

        <div class="progress mt-2">
            <div id="session-progress" class="progress-bar bg-primary"></div>
        </div>

    </div>

    <!-- USER INFO -->
    <div class="user-card mb-3">

        <div class="fw-semibold">
            <i class="bi bi-person-circle me-1"></i>
            {{ Auth::user()->name }}
        </div>

        <small class="text-muted">
            Role: {{ ucfirst(Auth::user()->role) }}
        </small>

    </div>

    <!-- LOGOUT -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="mt-auto">
        @csrf
        <button class="btn btn-outline-danger w-100">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
        </button>
    </form>

</div>