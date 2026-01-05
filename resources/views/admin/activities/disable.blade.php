@extends('layouts.admin')

@section('title', 'Vô hiệu hóa hoạt động')

@section('content')

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-x-circle"></i> Vô hiệu hóa hoạt động
        </h3>
        <a href="{{ $backUrl ?? route('admin.activities.violations') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- THÔNG TIN HOẠT ĐỘNG --}}
    <div class="card mb-4">
        <div class="card-header" style="background: var(--primary-blue); color: white;">
            <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Thông tin hoạt động</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>Bạn đang vô hiệu hóa hoạt động:</strong> {{ $activity->title }}
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>CLB:</strong> {{ $activity->club_name }} ({{ $activity->club_code }})</p>
                    <p><strong>Người tạo:</strong> 
                        {{ $activity->creator_name ?? 'N/A' }}
                        @if($activity->creator_student_code)
                            ({{ $activity->creator_student_code }})
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Thời gian:</strong> 
                        {{ \Carbon\Carbon::parse($activity->start_at)->format('d/m/Y H:i') }} - 
                        {{ \Carbon\Carbon::parse($activity->end_at)->format('d/m/Y H:i') }}
                    </p>
                    <p><strong>Địa điểm:</strong> {{ $activity->location ?? 'Chưa cập nhật' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM VÔ HIỆU HÓA --}}
    <div class="card">
        <div class="card-header" style="background: var(--primary-blue); color: white;">
            <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Thông tin vi phạm</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.activities.disable', $activity->id) }}" method="POST">
                @csrf
                @if(isset($backUrl) && $backUrl)
                    @php
                        $queryString = parse_url($backUrl, PHP_URL_QUERY);
                    @endphp
                    <input type="hidden" name="back_query" value="{{ $queryString }}">
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">Loại vi phạm <span class="text-danger">*</span></label>
                    <input type="text" name="violation_type" class="form-control" required
                           placeholder="Ví dụ: Tổ chức sai nội dung, không xin phép, vi phạm nội quy..."
                           value="{{ old('violation_type', $activity->violation_type ?? '') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mức độ vi phạm <span class="text-danger">*</span></label>
                    <select name="violation_severity" class="form-select" required>
                        <option value="">-- Chọn mức độ --</option>
                        <option value="light" {{ old('violation_severity', $activity->violation_severity ?? '') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                        <option value="medium" {{ old('violation_severity', $activity->violation_severity ?? '') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="serious" {{ old('violation_severity', $activity->violation_severity ?? '') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Lý do vi phạm <span class="text-danger">*</span></label>
                    <textarea name="violation_notes" class="form-control" rows="6" required
                              placeholder="Mô tả chi tiết lý do vi phạm...">{{ old('violation_notes', $activity->violation_notes ?? '') }}</textarea>
                    <small class="text-muted">Lý do này sẽ được lưu lại và hiển thị trong danh sách hoạt động vi phạm.</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ $backUrl ?? route('admin.activities.violations') }}" class="btn btn-secondary">
                        <i class="bi bi-x"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle"></i> Vô hiệu hóa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

