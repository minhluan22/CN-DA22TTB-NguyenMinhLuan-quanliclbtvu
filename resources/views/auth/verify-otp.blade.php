@extends('layouts.guest')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth-login.css') }}">
@endpush

@section('title', 'Xác nhận mã OTP')

@section('content')
<div class="login-page">
  <div class="login-box">

    <h2>Xác nhận mã OTP</h2>

    {{-- HIỂN THỊ OTP TRÊN WEB --}}
    @if(session('show_otp'))
      <div class="otp-box" style="
          padding: 10px;
          background: #FFF7B0; 
          border: 1px solid #FFE600;
          border-radius: 8px;
          margin-bottom: 15px;
          font-size: 18px;
          font-weight: bold;
          text-align: center;
      ">
        Mã OTP của bạn: 
        <span style="font-size: 22px; letter-spacing: 3px;">
            {{ session('show_otp') }}
        </span>
        <div style="font-size: 12px; margin-top: 5px; color: #333;">
            Mã hết hạn sau 10 phút
        </div>
      </div>
    @endif


    {{-- THÔNG BÁO SUCCESS --}}
    @if(session('success'))
      <div class="success-box">{{ session('success') }}</div>
    @endif

    {{-- THÔNG BÁO LỖI --}}
    @if ($errors->any())
      <div class="error-box">
        <ul>
          @foreach ($errors->all() as $error)
            <li>• {{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif


    {{-- FORM NHẬP OTP --}}
    <form method="POST" action="{{ route('verify.otp.post') }}">
      @csrf

      <label>Nhập mã OTP</label>
      <input type="text" name="otp" placeholder="6 chữ số" required maxlength="6" autocomplete="one-time-code">

      <button type="submit" class="btn-login-submit">Xác nhận</button>
    </form>


    {{-- RESEND OTP --}}
    <form method="POST" action="{{ route('resend.otp') }}" style="margin-top:12px;">
      @csrf
      <button type="submit" class="btn-link">Gửi lại mã (nếu chưa nhận)</button>
    </form>

    <p class="auth-footer-text">
      <a href="{{ route('register') }}">Quay lại đăng ký</a>
    </p>

  </div>
</div>
@endsection
