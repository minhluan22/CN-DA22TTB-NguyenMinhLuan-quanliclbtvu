@extends('layouts.admin')

@section('title', 'Thông tin website')

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
        <i class="bi bi-globe"></i> Thông tin Website
    </h3>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.system-config.website.update') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Tên website <span class="text-danger">*</span></label>
                        <input type="text" name="website_name" class="form-control" 
                               value="{{ old('website_name', $configs['website_name']) }}" 
                               placeholder="Ví dụ: Hệ thống quản lý CLB" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Mô tả website</label>
                        <textarea name="website_description" class="form-control" rows="3" 
                                  placeholder="Mô tả ngắn gọn về website...">{{ old('website_description', $configs['website_description']) }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Từ khóa (Keywords)</label>
                        <input type="text" name="website_keywords" class="form-control" 
                               value="{{ old('website_keywords', $configs['website_keywords']) }}" 
                               placeholder="Từ khóa cách nhau bằng dấu phẩy (VD: CLB, sinh viên, hoạt động)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tác giả</label>
                        <input type="text" name="website_author" class="form-control" 
                               value="{{ old('website_author', $configs['website_author']) }}" 
                               placeholder="Tên tác giả hoặc tổ chức">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email liên hệ</label>
                        <input type="email" name="website_email" class="form-control" 
                               value="{{ old('website_email', $configs['website_email']) }}" 
                               placeholder="contact@example.com">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số điện thoại</label>
                        <input type="text" name="website_phone" class="form-control" 
                               value="{{ old('website_phone', $configs['website_phone']) }}" 
                               placeholder="0123456789">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" name="website_address" class="form-control" 
                               value="{{ old('website_address', $configs['website_address']) }}" 
                               placeholder="Địa chỉ trụ sở">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Footer text</label>
                        <textarea name="website_footer" class="form-control" rows="2" 
                                  placeholder="Nội dung hiển thị ở chân trang...">{{ old('website_footer', $configs['website_footer']) }}</textarea>
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
</div>

@endsection
