@extends('layouts.admin')

@section('title', 'Cấu hình điểm hoạt động')

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
        <i class="bi bi-star"></i> Cấu hình điểm hoạt động
    </h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-info-circle"></i> Hướng dẫn</h5>
            <p class="card-text mb-0">
                Cấu hình các mức điểm hoạt động cho sinh viên. Các giá trị này sẽ được áp dụng tự động cho toàn bộ hệ thống.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.system-config.points.update') }}" method="POST">
                @csrf

                <h5 class="mb-4">Mức điểm cơ bản</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Điểm tham gia sự kiện <span class="text-danger">*</span></label>
                        <input type="number" name="points_attend_event" class="form-control" 
                               value="{{ old('points_attend_event', $configs['points_attend_event']) }}" 
                               min="0" max="100" required>
                        <small class="text-muted">Điểm được cộng khi sinh viên tham gia sự kiện (0-100 điểm)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Điểm tổ chức hoạt động <span class="text-danger">*</span></label>
                        <input type="number" name="points_organize_event" class="form-control" 
                               value="{{ old('points_organize_event', $configs['points_organize_event']) }}" 
                               min="0" max="100" required>
                        <small class="text-muted">Điểm được cộng khi sinh viên tổ chức hoạt động (0-100 điểm)</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Điểm thưởng thường xuyên <span class="text-danger">*</span></label>
                        <input type="number" name="points_regular_reward" class="form-control" 
                               value="{{ old('points_regular_reward', $configs['points_regular_reward']) }}" 
                               min="0" max="50" required>
                        <small class="text-muted">Điểm thưởng cho các hoạt động thường xuyên (0-50 điểm)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Điểm trừ vi phạm <span class="text-danger">*</span></label>
                        <input type="number" name="points_violation_deduct" class="form-control" 
                               value="{{ old('points_violation_deduct', $configs['points_violation_deduct']) }}" 
                               min="-100" max="0" required>
                        <small class="text-muted">Điểm bị trừ khi vi phạm nội quy (-100 đến 0 điểm)</small>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-4">Giới hạn điểm</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Giới hạn điểm theo học kỳ <span class="text-danger">*</span></label>
                        <input type="number" name="points_limit_semester" class="form-control" 
                               value="{{ old('points_limit_semester', $configs['points_limit_semester']) }}" 
                               min="0" max="1000" required>
                        <small class="text-muted">Giới hạn tối đa điểm có thể tích lũy trong 1 học kỳ (0-1000 điểm)</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Giới hạn điểm theo năm học <span class="text-danger">*</span></label>
                        <input type="number" name="points_limit_year" class="form-control" 
                               value="{{ old('points_limit_year', $configs['points_limit_year']) }}" 
                               min="0" max="2000" required>
                        <small class="text-muted">Giới hạn tối đa điểm có thể tích lũy trong 1 năm học (0-2000 điểm)</small>
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
