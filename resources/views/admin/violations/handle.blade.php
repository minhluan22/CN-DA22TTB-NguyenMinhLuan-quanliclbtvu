@extends('layouts.admin')

@section('title', 'Xử lý kỷ luật vi phạm')

@section('content')

<div class="container-fluid mt-3">
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-exclamation-triangle"></i> Xử lý kỷ luật vi phạm
        </h3>
        <a href="{{ route('admin.violations.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    {{-- THÔNG TIN VI PHẠM --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Thông tin vi phạm</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Sinh viên vi phạm:</strong> {{ $violation->user->name ?? 'N/A' }} 
                        <span class="text-muted">({{ $violation->user->student_code ?? 'N/A' }})</span>
                    </p>
                    <p><strong>CLB:</strong> {{ $violation->club->name ?? 'N/A' }} 
                        <span class="text-muted">({{ $violation->club->code ?? 'N/A' }})</span>
                    </p>
                    <p><strong>Nội quy vi phạm:</strong> {{ $violation->regulation->code ?? 'N/A' }} - {{ $violation->regulation->title ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Mức độ:</strong>
                        @if($violation->severity == 'light')
                            <span class="badge bg-success">Nhẹ</span>
                        @elseif($violation->severity == 'medium')
                            <span class="badge bg-warning text-dark">Trung bình</span>
                        @else
                            <span class="badge bg-danger">Nghiêm trọng</span>
                        @endif
                    </p>
                    <p><strong>Thời gian xảy ra:</strong> {{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y H:i') }}</p>
                    <p><strong>Người ghi nhận:</strong> {{ $violation->recorder->name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="mt-3">
                <strong>Mô tả vi phạm:</strong>
                <div class="border p-3 rounded mt-2" style="background-color: #f9fafb; white-space: pre-wrap;">
                    {{ $violation->description }}
                </div>
            </div>
        </div>
    </div>

    {{-- FORM XỬ LÝ KỶ LUẬT --}}
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Xử lý kỷ luật</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.violations.process-discipline', $violation->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Hình thức kỷ luật <span class="text-danger">*</span></label>
                    <select name="discipline_type" class="form-control @error('discipline_type') is-invalid @enderror" required>
                        <option value="">-- Chọn hình thức kỷ luật --</option>
                        <option value="warning" {{ old('discipline_type') == 'warning' ? 'selected' : '' }}>Cảnh cáo</option>
                        <option value="reprimand" {{ old('discipline_type') == 'reprimand' ? 'selected' : '' }}>Khiển trách</option>
                        <option value="suspension" {{ old('discipline_type') == 'suspension' ? 'selected' : '' }}>Đình chỉ</option>
                        <option value="expulsion" {{ old('discipline_type') == 'expulsion' ? 'selected' : '' }}>Buộc rời CLB</option>
                        <option value="ban" {{ old('discipline_type') == 'ban' ? 'selected' : '' }}>Cấm tham gia hoạt động</option>
                    </select>
                    @error('discipline_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Lý do xử lý <span class="text-danger">*</span></label>
                    <textarea name="discipline_reason" class="form-control @error('discipline_reason') is-invalid @enderror" 
                              rows="5" required placeholder="Nhập lý do xử lý kỷ luật...">{{ old('discipline_reason') }}</textarea>
                    @error('discipline_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Thời hạn kỷ luật bắt đầu</label>
                            <input type="date" name="discipline_period_start" 
                                   class="form-control @error('discipline_period_start') is-invalid @enderror"
                                   value="{{ old('discipline_period_start') }}">
                            @error('discipline_period_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Để trống nếu không có thời hạn cụ thể</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Thời hạn kỷ luật kết thúc</label>
                            <input type="date" name="discipline_period_end" 
                                   class="form-control @error('discipline_period_end') is-invalid @enderror"
                                   value="{{ old('discipline_period_end') }}">
                            @error('discipline_period_end')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng thái sau xử lý <span class="text-danger">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="processed" {{ old('status') == 'processed' ? 'selected' : '' }}>Đã xử lý</option>
                        <option value="monitoring" {{ old('status') == 'monitoring' ? 'selected' : '' }}>Đang theo dõi</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Chọn "Đang theo dõi" nếu cần tiếp tục giám sát</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.violations.index') }}" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary" style="background-color: #5FB84A; color: white;">
                        <i class="bi bi-check-circle"></i> Xác nhận xử lý
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

