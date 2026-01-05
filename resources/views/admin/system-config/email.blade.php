@extends('layouts.admin')

@section('title', 'Email hệ thống')

@section('content')
<div class="container-fluid mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h3 class="fw-bold mb-4">
        <i class="bi bi-envelope"></i> Cấu hình Email hệ thống
    </h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-info-circle"></i> Hướng dẫn</h5>
            <p class="card-text mb-0">
                Cấu hình SMTP để hệ thống có thể gửi email thông báo. Sau khi cấu hình, bạn có thể test bằng cách gửi email thử nghiệm.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.system-config.email.update') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mail Driver <span class="text-danger">*</span></label>
                        <select name="mail_mailer" class="form-select" required>
                            <option value="smtp" {{ old('mail_mailer', $configs['mail_mailer']) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="mailgun" {{ old('mail_mailer', $configs['mail_mailer']) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="ses" {{ old('mail_mailer', $configs['mail_mailer']) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                            <option value="postmark" {{ old('mail_mailer', $configs['mail_mailer']) == 'postmark' ? 'selected' : '' }}>Postmark</option>
                            <option value="log" {{ old('mail_mailer', $configs['mail_mailer']) == 'log' ? 'selected' : '' }}>Log (Test)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">SMTP Host</label>
                        <input type="text" name="mail_host" class="form-control" 
                               value="{{ old('mail_host', $configs['mail_host']) }}" 
                               placeholder="smtp.gmail.com">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">SMTP Port</label>
                        <input type="number" name="mail_port" class="form-control" 
                               value="{{ old('mail_port', $configs['mail_port']) }}" 
                               placeholder="587">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Encryption</label>
                        <select name="mail_encryption" class="form-select">
                            <option value="">None</option>
                            <option value="tls" {{ old('mail_encryption', $configs['mail_encryption']) == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('mail_encryption', $configs['mail_encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Username</label>
                        <input type="text" name="mail_username" class="form-control" 
                               value="{{ old('mail_username', $configs['mail_username']) }}" 
                               placeholder="your-email@gmail.com">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="mail_password" class="form-control" 
                               value="{{ old('mail_password', $configs['mail_password']) }}" 
                               placeholder="Mật khẩu email hoặc App Password">
                        <small class="text-muted">Đối với Gmail, sử dụng App Password thay vì mật khẩu thông thường</small>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">From Address <span class="text-danger">*</span></label>
                        <input type="email" name="mail_from_address" class="form-control" 
                               value="{{ old('mail_from_address', $configs['mail_from_address']) }}" 
                               placeholder="noreply@example.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">From Name <span class="text-danger">*</span></label>
                        <input type="text" name="mail_from_name" class="form-control" 
                               value="{{ old('mail_from_name', $configs['mail_from_name']) }}" 
                               placeholder="Hệ thống quản lý CLB" required>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Lưu cấu hình
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TEST EMAIL FORM --}}
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-send"></i> Test gửi email</h5>
            <form action="{{ route('admin.system-config.email.test') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <input type="email" name="test_email" class="form-control" 
                               placeholder="Nhập email để test..." required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-send-fill"></i> Gửi email test
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
