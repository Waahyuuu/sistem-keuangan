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

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Css --}}
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
</head>

<body>

    <div class="d-flex">
        {{-- Toast --}}
        @include('components.toast')

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Content --}}
        <div class="flex-fill p-4 main-content">
            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>

    </div>

    <script>
        window.loginTime = "{{ session('login_time') ? \Carbon\Carbon::parse(session('login_time'))->toIso8601String() : '' }}";
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- JS --}}
    <script src="{{ asset('js/style.js') }}"></script>
    <script src="{{ asset('js/components.js') }}"></script>

    {{-- Custom Page Scripts --}}
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

</body>

</html>