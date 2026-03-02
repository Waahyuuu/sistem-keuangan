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
        @include('layouts.sidebar')

        {{-- Content --}}
        <div class="flex-fill p-4">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- JS Umum --}}
    <script src="{{ asset('js/style.js') }}"></script>

    {{-- Custom Page Scripts --}}
    @stack('scripts')

</body>

</html>