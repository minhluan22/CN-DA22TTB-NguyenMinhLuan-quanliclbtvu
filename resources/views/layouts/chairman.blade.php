<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Chủ nhiệm CLB')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/color-system.css') }}">
    {{-- CSS Admin cho sidebar giống admin --}}
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
    <style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #0033A0;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF3A0;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
            --card: #ffffff;
            --muted: #6b7280;
            --border: #e5e7eb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .content {
            margin-left: 260px;
            padding: 24px;
            min-height: 100vh;
            width: calc(100% - 260px);
            max-width: 100%;
            box-sizing: border-box;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        
        /* Khi sidebar đóng */
        body.sidebar-closed .content {
            margin-left: 0;
            width: 100%;
        }
        
        @media (max-width: 1200px) {
            .content {
                margin-left: 0;
                padding: 16px;
                width: 100%;
            }
            
            body.sidebar-closed .content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('student.chairman.sidebar')

    <main class="content">
        @if(session('success'))
            <div class="alert alert-success" style="background: #8EDC6E; color: #1f1f1f; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #5FB84A;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="background: #FFF3A0; color: #B84A5F; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #B84A5F;">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @include('student.chairman.footer')

    @stack('scripts')
    
    <style>
    /* Force override để đảm bảo thanh trượt hoạt động trên tất cả các trang chairman */
    aside.sidebar > nav.nav,
    .sidebar > nav.nav,
    aside.sidebar nav.nav,
    .sidebar nav.nav,
    aside.sidebar .nav,
    .sidebar .nav,
    body aside.sidebar .nav,
    body .sidebar .nav,
    html body aside.sidebar .nav,
    html body .sidebar .nav,
    nav.nav {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 !important;
        min-height: 0 !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 16px !important;
        padding-top: 88px !important;
        padding-right: 8px !important;
        padding-bottom: 0 !important;
        gap: 0 !important;
        scrollbar-width: thin !important;
        scrollbar-color: rgba(255, 255, 255, 0.3) rgba(255, 255, 255, 0.1) !important;
        height: 0 !important;
        max-height: none !important;
    }
    aside.sidebar > nav.nav::-webkit-scrollbar,
    .sidebar > nav.nav::-webkit-scrollbar,
    aside.sidebar nav.nav::-webkit-scrollbar,
    .sidebar nav.nav::-webkit-scrollbar,
    aside.sidebar .nav::-webkit-scrollbar,
    .sidebar .nav::-webkit-scrollbar,
    body aside.sidebar .nav::-webkit-scrollbar,
    body .sidebar .nav::-webkit-scrollbar,
    html body aside.sidebar .nav::-webkit-scrollbar,
    html body .sidebar .nav::-webkit-scrollbar,
    nav.nav::-webkit-scrollbar {
        width: 6px !important;
        display: block !important;
    }
    aside.sidebar > nav.nav::-webkit-scrollbar-track,
    .sidebar > nav.nav::-webkit-scrollbar-track,
    aside.sidebar nav.nav::-webkit-scrollbar-track,
    .sidebar nav.nav::-webkit-scrollbar-track,
    aside.sidebar .nav::-webkit-scrollbar-track,
    .sidebar .nav::-webkit-scrollbar-track,
    body aside.sidebar .nav::-webkit-scrollbar-track,
    body .sidebar .nav::-webkit-scrollbar-track,
    html body aside.sidebar .nav::-webkit-scrollbar-track,
    html body .sidebar .nav::-webkit-scrollbar-track,
    nav.nav::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1) !important;
        border-radius: 3px;
    }
    aside.sidebar > nav.nav::-webkit-scrollbar-thumb,
    .sidebar > nav.nav::-webkit-scrollbar-thumb,
    aside.sidebar nav.nav::-webkit-scrollbar-thumb,
    .sidebar nav.nav::-webkit-scrollbar-thumb,
    aside.sidebar .nav::-webkit-scrollbar-thumb,
    .sidebar .nav::-webkit-scrollbar-thumb,
    body aside.sidebar .nav::-webkit-scrollbar-thumb,
    body .sidebar .nav::-webkit-scrollbar-thumb,
    html body aside.sidebar .nav::-webkit-scrollbar-thumb,
    html body .sidebar .nav::-webkit-scrollbar-thumb,
    nav.nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3) !important;
        border-radius: 3px;
    }
    aside.sidebar > nav.nav::-webkit-scrollbar-thumb:hover,
    .sidebar > nav.nav::-webkit-scrollbar-thumb:hover,
    aside.sidebar nav.nav::-webkit-scrollbar-thumb:hover,
    .sidebar nav.nav::-webkit-scrollbar-thumb:hover,
    aside.sidebar .nav::-webkit-scrollbar-thumb:hover,
    .sidebar .nav::-webkit-scrollbar-thumb:hover,
    body aside.sidebar .nav::-webkit-scrollbar-thumb:hover,
    body .sidebar .nav::-webkit-scrollbar-thumb:hover,
    html body aside.sidebar .nav::-webkit-scrollbar-thumb:hover,
    html body .sidebar .nav::-webkit-scrollbar-thumb:hover,
    nav.nav::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5) !important;
    }
    </style>
</body>
</html>

