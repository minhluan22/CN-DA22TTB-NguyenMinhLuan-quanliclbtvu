<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>@yield('title', 'Trang khách - Hệ thống CLB')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">


{{-- Suppress errors from Chrome extensions - Load early --}}
<script>
    // Suppress uncaught promise errors from extensions (load before other scripts)
    (function() {
        const originalAddEventListener = window.addEventListener;
        window.addEventListener = function(type, listener, options) {
            if (type === 'unhandledrejection') {
                const wrappedListener = function(event) {
                    if (event.reason && (
                        (event.reason.stack && (
                            event.reason.stack.includes('onboarding.js') ||
                            event.reason.stack.includes('gads-scrapper')
                        )) ||
                        (typeof event.reason === 'string' && (
                            event.reason.includes('onboarding') ||
                            event.reason.includes('gads-scrapper')
                        ))
                    )) {
                        event.preventDefault();
                        return false;
                    }
                    if (listener) listener(event);
                };
                return originalAddEventListener.call(this, type, wrappedListener, options);
            }
            return originalAddEventListener.call(this, type, listener, options);
        };
    })();
</script>

<link rel="stylesheet" href="{{ asset('css/guest.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@include('guest._color-classes')
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'primary-blue': 'var(--primary-blue)',
                    'accent-yellow': 'var(--accent-yellow)',
                    'soft-yellow': 'var(--soft-yellow)',
                    'text-dark': 'var(--text-dark)',
                    'text-light': 'var(--text-light)',
                }
            }
        }
    }
</script>
@stack('styles')
</head>
<body>


<header class="guest-header">
<div class="container">
<div class="logo-container">
    @if(file_exists(public_path('images/tvu-clubs-logo.png')))
        <img src="{{ asset('images/tvu-clubs-logo.png') }}" alt="TVU CLUBS Logo" class="header-logo-img">
    @elseif(file_exists(public_path('images/logo.png')))
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="header-logo-img">
    @else
        <div class="header-logo-placeholder">🎓</div>
    @endif
    <h1 class="logo">CLB Trường Đại Học Trà Vinh</h1>
</div>
<nav>
<a href="{{ route('guest.home') }}">Trang chủ</a>
<a href="{{ route('guest.clubs') }}">Câu lạc bộ</a>
<a href="{{ route('guest.events') }}">Hoạt động</a>
<a href="{{ route('guest.about') }}">Giới thiệu</a>
<a href="{{ route('guest.contact') }}">Liên hệ</a>
<a href="{{ route('login') }}" class="btn-login">Đăng nhập</a>
<a href="{{ route('register') }}" class="btn-register">Đăng ký</a>
</nav>
</div>
</header>


<main>
@yield('content')
</main>


<footer class="guest-footer">
    <div class="footer-container">
        <div class="footer-column">
            <h3>CLB ĐẠI HỌC TRÀ VINH</h3>
            <p>Nền tảng quản lý và kết nối các câu lạc bộ sinh viên.</p>
            <p>Phát triển kỹ năng – Kết nối đam mê</p>
        </div>

        <div class="footer-column">
            <h4>Liên kết nhanh</h4>
            <a href="{{ route('guest.home') }}">Trang chủ</a>
            <a href="{{ route('guest.clubs') }}">Câu lạc bộ</a>
            <a href="{{ route('guest.events') }}">Hoạt động</a>
            <a href="{{ route('guest.about') }}">Giới thiệu</a>
            <a href="{{ route('guest.contact') }}">Liên hệ</a>
            <a href="{{ route('guest.faq') }}">FAQ</a>
            <a href="{{ route('guest.privacy') }}">Chính sách bảo mật</a>
        </div>

        <div class="footer-column">
            <h4>Liên hệ</h4>
            <p>Email: minhluanngulac@gmail.com</p>
            <p>Hotline: 0123 456 789</p>
            <p>Địa chỉ: Đại Học trà Vinh</p>
        </div>
    </div>

    <!-- Website được thực hiện bởi sinh viên -->
    <div class="footer-credits">
        <p class="credits-main">Website được thực hiện bởi sinh viên Trường Đại học Trà Vinh</p>
        <p class="credits-author">Nguyễn Minh Luân 110122109 DA22TTB</p>
    </div>

    <div class="footer-bottom">
        © 2025 Hệ thống Quản lý Câu lạc bộ – Trường CLB Đại học Trà Vinh
    </div>
</footer>

@stack('scripts')

{{-- Suppress errors from Chrome extensions and external scripts --}}
<script>
    // Suppress uncaught promise errors from extensions
    window.addEventListener('unhandledrejection', function(event) {
        // Check if error is from extension scripts
        if (event.reason && (
            event.reason.message && (
                event.reason.message.includes('onboarding.js') ||
                event.reason.message.includes('gads-scrapper')
            )
        )) {
            event.preventDefault();
            return false;
        }
    });

    // Suppress console errors from extensions (optional - for cleaner console)
    const originalError = console.error;
    console.error = function(...args) {
        const errorMessage = args.join(' ');
        if (errorMessage.includes('onboarding.js') || 
            errorMessage.includes('gads-scrapper') ||
            errorMessage.includes('chrome-extension')) {
            return; // Suppress extension errors
        }
        originalError.apply(console, args);
    };
</script>

</body>
</html>