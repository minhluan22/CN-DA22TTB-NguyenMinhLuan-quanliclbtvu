@extends('layouts.guest')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth-register.css') }}">
@endpush

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
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

@section('title', 'Đăng ký tài khoản')

@section('content')

<div class="login-page">
    <div class="login-box">

        <h2 class="login-title">Tạo tài khoản sinh viên</h2>
        <p class="login-desc">
            Đăng ký miễn phí để tham gia các Câu lạc bộ và hoạt động sinh viên!
        </p>

        {{-- Hiển thị các validation errors --}}
        @if ($errors->any())
            <div class="error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Nút Google giống login --}}
        <a href="#" class="btn-social google">
            <span>G</span> Đăng ký bằng Google
        </a>

        <div class="divider">
            <span>hoặc</span>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <label>Họ và tên</label>
            <input type="text" name="name" placeholder="Nhập họ và tên"
                value="{{ old('name') }}" required>

            <label>MSSV</label>
            <input type="text" name="student_code" placeholder="VD: 110122109"
                value="{{ old('student_code') }}"
                required
                pattern="\d{9}"
                minlength="9"
                maxlength="9"
                inputmode="numeric"
                title="MSSV phải gồm đúng 9 chữ số">


            <label>Email</label>
            <input type="email" name="email" placeholder="VD: 110122109@st.tvu.edu.vn"
                value="{{ old('email') }}" required>

            <label>Mật khẩu</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="********" required>
                <i class="bi bi-eye-slash toggle-password" id="togglePassword" onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
            </div>

            <label>Nhập lại mật khẩu</label>
            <div class="password-wrapper">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="********" required>
                <i class="bi bi-eye-slash toggle-password" id="togglePasswordConfirmation" onclick="togglePasswordVisibility('password_confirmation', 'togglePasswordConfirmation')"></i>
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
            @else
            <div class="recaptcha-wrapper" style="padding: 10px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; color: #856404;">
                ⚠️ reCAPTCHA chưa được cấu hình. Vui lòng kiểm tra RECAPTCHA_SITE_KEY trong file .env
            </div>
            @endif

            <button type="submit" class="btn-submit">
                Đăng ký
            </button>
        </form>


        {{-- Footer giống login --}}

        Đã có tài khoản? 
        <a href="{{ route('login') }}" class="auth-link">Đăng nhập ngay</a>


    </div>
</div>

@endsection
