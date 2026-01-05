@extends('layouts.guest')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth-login.css') }}">
@endpush

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    // Xác định intended_role dựa trên referer và set vào hidden input
    // Điều này giúp controller biết user muốn đăng nhập với role nào
    (function() {
        var referer = document.referrer;
        var intendedRole = '';
        
        // Kiểm tra referer
        if (referer && (referer.indexOf('/admin') !== -1 || referer.indexOf('admin') !== -1)) {
            intendedRole = 'admin';
        } else if (referer && (referer.indexOf('/student') !== -1 || referer.indexOf('student') !== -1)) {
            intendedRole = 'student';
        }
        // Nếu không có referer, kiểm tra cookie hiện tại
        else {
            var existingRole = getCookie('auth_role');
            if (existingRole) {
                intendedRole = existingRole;
            }
        }
        
        // Set vào hidden input
        if (intendedRole) {
            document.getElementById('intendedRoleInput').value = intendedRole;
        }
    })();
    
    // Helper function để get cookie
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Toggle password visibility
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }
</script>
@endpush

@section('title', 'Đăng nhập tài khoản')

@section('content')


<div class="login-page">
    <div class="login-box">

        <h2>Đăng nhập hoặc tạo tài khoản</h2>
                {{-- Hiện thông báo success --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Hiện thông báo lỗi chung (nếu có) --}}
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        {{-- Hiển thị các validation errors cũ (nếu chưa có) --}}
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="login-desc">
            Đăng ký miễn phí hoặc đăng nhập để nhận được các ưu đãi và quyền lợi hấp dẫn!
        </p>

        @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf
            
            {{-- Hidden input để truyền intended_role --}}
            <input type="hidden" name="intended_role" id="intendedRoleInput" value="">

            <label>MSSV</label>
            <input type="text" name="student_code" placeholder="VD: 110122109" value="{{ old('student_code') }}" required>


            <label>Mật khẩu</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="********" required>
                <i class="bi bi-eye-slash toggle-password" id="togglePassword" onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
            </div>

            {{-- Google reCAPTCHA --}}
            @php
                $recaptchaSiteKey = config('services.recaptcha.site_key');
            @endphp
            @if(!empty($recaptchaSiteKey))
            <div class="recaptcha-wrapper">
                <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                @error('g-recaptcha-response')
                    <span class="text-danger" style="color: #B84A5F; font-size: 14px; display: block; margin-top: 5px;">
                        {{ $message }}
                    </span>
                @enderror
            </div>
            @endif

            <button type="submit" class="btn-login-submit">Đăng Nhập</button>

        </form>

    <p class="auth-footer-text">
        <a href="{{ route('register') }}">Đăng ký tài khoản mới</a>
    </p>


    </div>
</div>

@endsection
