@extends('layouts.admin')

@section('title', 'Logo – Banner')

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

    <h3 class="fw-bold mb-4">
        <i class="bi bi-image"></i> Quản lý Logo & Banner
    </h3>

    {{-- LOGO --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Logo Website</h5>
            @if($configs['logo'])
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $configs['logo']) }}" alt="Logo" 
                         style="max-height: 150px; max-width: 300px; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                </div>
                <form action="{{ route('admin.system-config.logo.delete', 'logo') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa logo?');">
                        <i class="bi bi-trash"></i> Xóa logo
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.system-config.logo.upload-logo') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Upload logo mới</label>
                    <input type="file" name="logo" class="form-control" accept="image/jpeg,image/jpg,image/png,image/svg+xml" required>
                    <small class="text-muted">Định dạng: JPEG, JPG, PNG, SVG. Kích thước tối đa: 2MB</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Upload logo
                </button>
            </form>
        </div>
    </div>

    {{-- FAVICON --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Favicon</h5>
            @if($configs['favicon'])
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $configs['favicon']) }}" alt="Favicon" 
                         style="max-height: 64px; max-width: 64px; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                </div>
                <form action="{{ route('admin.system-config.logo.delete', 'favicon') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa favicon?');">
                        <i class="bi bi-trash"></i> Xóa favicon
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.system-config.logo.upload-favicon') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Upload favicon mới</label>
                    <input type="file" name="favicon" class="form-control" accept="image/jpeg,image/jpg,image/png,image/x-icon,image/svg+xml" required>
                    <small class="text-muted">Định dạng: JPEG, JPG, PNG, ICO, SVG. Kích thước tối đa: 512KB</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Upload favicon
                </button>
            </form>
        </div>
    </div>

    {{-- BANNER TRANG CHỦ --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Banner Trang Chủ</h5>
            @if($configs['banner_home'])
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $configs['banner_home']) }}" alt="Banner trang chủ" 
                         style="max-height: 200px; max-width: 100%; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                </div>
                <form action="{{ route('admin.system-config.logo.delete', 'banner_home') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa banner trang chủ?');">
                        <i class="bi bi-trash"></i> Xóa banner
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.system-config.logo.upload-banner-home') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Upload banner trang chủ mới</label>
                    <input type="file" name="banner_home" class="form-control" accept="image/jpeg,image/jpg,image/png" required>
                    <small class="text-muted">Định dạng: JPEG, JPG, PNG. Kích thước tối đa: 5MB</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Upload banner
                </button>
            </form>
        </div>
    </div>

    {{-- BANNER TRANG ĐĂNG NHẬP --}}
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Banner Trang Đăng Nhập</h5>
            @if($configs['banner_login'])
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $configs['banner_login']) }}" alt="Banner trang đăng nhập" 
                         style="max-height: 200px; max-width: 100%; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                </div>
                <form action="{{ route('admin.system-config.logo.delete', 'banner_login') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa banner trang đăng nhập?');">
                        <i class="bi bi-trash"></i> Xóa banner
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.system-config.logo.upload-banner-login') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Upload banner trang đăng nhập mới</label>
                    <input type="file" name="banner_login" class="form-control" accept="image/jpeg,image/jpg,image/png" required>
                    <small class="text-muted">Định dạng: JPEG, JPG, PNG. Kích thước tối đa: 5MB</small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Upload banner
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
