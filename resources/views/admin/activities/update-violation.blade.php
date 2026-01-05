@extends('layouts.admin')

@section('title', 'Ghi nhận & cập nhật xử lý vi phạm')

@section('content')

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-pencil-square"></i> Ghi nhận & cập nhật xử lý vi phạm
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
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Hoạt động:</strong> {{ $violation->title }}</p>
                    <p><strong>CLB:</strong> {{ $violation->club_name }}</p>
                    <p><strong>Người tạo:</strong> 
                        {{ $violation->creator_name ?? 'N/A' }}
                        @if($violation->creator_student_code)
                            ({{ $violation->creator_student_code }})
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Thời gian:</strong> 
                        {{ \Carbon\Carbon::parse($violation->start_at)->format('d/m/Y H:i') }} - 
                        {{ \Carbon\Carbon::parse($violation->end_at)->format('d/m/Y H:i') }}
                    </p>
                    @if($violation->violation_detected_at)
                        <p><strong>Phát hiện:</strong> 
                            {{ \Carbon\Carbon::parse($violation->violation_detected_at)->format('d/m/Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- FORM CẬP NHẬT XỬ LÝ --}}
    <div class="card">
        <div class="card-header" style="background: var(--primary-blue); color: white;">
            <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Thông tin xử lý</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.activities.update-violation', $violation->id) }}" method="POST">
                @csrf
                @method('PUT')
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
                    <label class="form-label fw-bold">Trạng thái xử lý <span class="text-danger">*</span></label>
                    <select name="violation_status" class="form-select" required>
                        <option value="pending" {{ old('violation_status', $violation->violation_status ?? 'pending') == 'pending' ? 'selected' : '' }}>Chưa xử lý</option>
                        <option value="processing" {{ old('violation_status', $violation->violation_status ?? '') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="processed" {{ old('violation_status', $violation->violation_status ?? '') == 'processed' ? 'selected' : '' }}>Đã xử lý</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mức độ vi phạm</label>
                    <select name="violation_severity" class="form-select">
                        <option value="">-- Giữ nguyên --</option>
                        <option value="light" {{ old('violation_severity', $violation->violation_severity ?? '') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                        <option value="medium" {{ old('violation_severity', $violation->violation_severity ?? '') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="serious" {{ old('violation_severity', $violation->violation_severity ?? '') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Ghi chú xử lý</label>
                    <textarea name="violation_notes" class="form-control" rows="6"
                              placeholder="Nhập ghi chú về quá trình xử lý vi phạm...">{{ old('violation_notes', '') }}</textarea>
                    <small class="text-muted">Ghi chú này sẽ được thêm vào lịch sử xử lý.</small>
                </div>

                @if($violation->violation_notes)
                    <div class="alert alert-info mb-3">
                        <strong>Ghi chú hiện tại:</strong><br>
                        <small>{!! nl2br(e(\Illuminate\Support\Str::limit($violation->violation_notes, 500))) !!}</small>
                    </div>
                @endif

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ $backUrl ?? route('admin.activities.violations') }}" class="btn btn-secondary">
                        <i class="bi bi-x"></i> Hủy
                    </a>
                    <button type="submit" class="btn" style="background-color: #5FB84A; color: white;">
                        <i class="bi bi-check-circle"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

