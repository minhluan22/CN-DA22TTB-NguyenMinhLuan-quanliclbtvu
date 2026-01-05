<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Quản trị Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- 🔥 CSRF Token bắt buộc cho mọi request -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ICONS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    {{-- BOOTSTRAP CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- CSS Gốc --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
    
    {{-- Hệ thống màu chuẩn --}}
    <link rel="stylesheet" href="{{ asset('css/color-system.css') }}?v={{ time() }}">

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')
</head>

<body>

    {{-- SIDEBAR --}}
    @include('admin.sidebar')

    {{-- MAIN CONTENT --}}
    <div class="main">
        @yield('content')
    </div>

    {{-- FOOTER --}}
    @include('admin.footer')

    {{-- SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- BOOTSTRAP JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- ADMIN JS --}}
    <script src="{{ asset('js/admin.js') }}"></script>

    {{-- PAGE-LEVEL SCRIPTS --}}
    @stack('scripts')
    @yield('scripts')

</body>
</html>
