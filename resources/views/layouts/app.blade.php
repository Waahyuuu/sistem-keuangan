<!DOCTYPE html>
<html>

<head>
    <title>
        @hasSection('title')
        @yield('title') - Keuangan
        @else
        Keuangan
        @endif
    </title>

    {{-- meta --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Css Umum --}}
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>

    <div class="d-flex">

        {{-- Sidebar --}}
        <div class="bg-light p-3" style="width:250px; min-height:100vh;">
            <h4 class="text-primary">Keuangan</h4>
            <hr>

            <a href="{{ route('dashboard') }}" class="d-block mb-2">Dashboard</a>
            <a href="{{ route('transaksi') }}" class="d-block mb-2">Transaksi</a>
            <a href="{{ route('master.data') }}" class="d-block mb-2">Master Data</a>
            <a href="{{ route('laporan') }}" class="d-block mb-2">Laporan</a>
            <a href="{{ route('pengaturan') }}" class="d-block mb-2">Pengaturan Akun</a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger btn-sm mt-3">Logout</button>
            </form>
        </div>

        {{-- Content --}}
        <div class="flex-fill p-4">
            @yield('content')
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- JS Umum --}}
    <script src="{{ asset('js/style.js') }}"></script>

</body>

</html>