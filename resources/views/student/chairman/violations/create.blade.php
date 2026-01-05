@extends('layouts.chairman')

@section('title', 'Ghi nhận vi phạm - Chủ nhiệm CLB')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #FFF3A0;
    }
    
    .dashboard-container {
        padding: 24px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    /* Page Header */
    .page-header {
        background: white;
        padding: 24px 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #0033A0;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    /* Club Info Card */
    .club-info-card {
        background: linear-gradient(135deg, #0033A0 0%, #0B3D91 100%);
        padding: 24px 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(0,51,160,0.2);
        color: white;
    }
    
    .club-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
    }
    
    .club-info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .club-info-label {
        font-size: 13px;
        opacity: 0.85;
        font-weight: 500;
    }
    
    .club-info-value {
        font-size: 18px;
        font-weight: 700;
    }
    
    .form-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-plus-circle"></i>
            Ghi nhận vi phạm
        </h1>
    </div>

    <!-- Club Info Card -->
    @if($chairmanClub)
        @php
            $clubModel = \App\Models\Club::find($chairmanClub->id);
        @endphp
        <div class="club-info-card">
            <div class="club-info-grid">
                <div class="club-info-item">
                    <span class="club-info-label">Tên Câu lạc bộ</span>
                    <span class="club-info-value">{{ $chairmanClub->name }}</span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Mã CLB</span>
                    <span class="club-info-value">{{ $chairmanClub->code }}</span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Trạng thái</span>
                    <span class="club-info-value">
                        @if($clubModel && $clubModel->status === 'active')
                            ✅ Hoạt động
                        @else
                            🔒 Ngừng hoạt động
                        @endif
                    </span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Vai trò của bạn</span>
                    <span class="club-info-value">Chủ nhiệm CLB</span>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> Vui lòng kiểm tra lại thông tin.
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('student.chairman.violations.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Sinh viên vi phạm <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Chọn sinh viên --</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->name }} ({{ $member->student_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Nội quy vi phạm <span class="text-danger">*</span></label>
                    <select name="regulation_id" class="form-select @error('regulation_id') is-invalid @enderror" required>
                        <option value="">-- Chọn nội quy --</option>
                        @foreach($regulations as $regulation)
                            <option value="{{ $regulation->id }}" {{ old('regulation_id') == $regulation->id ? 'selected' : '' }}>
                                {{ $regulation->code }} - {{ $regulation->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('regulation_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Mức độ vi phạm <span class="text-danger">*</span></label>
                    <select name="severity" class="form-select @error('severity') is-invalid @enderror" required>
                        <option value="">-- Chọn mức độ --</option>
                        <option value="light" {{ old('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                        <option value="medium" {{ old('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="serious" {{ old('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                    </select>
                    @error('severity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Thời gian xảy ra vi phạm <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="violation_date" 
                           class="form-control @error('violation_date') is-invalid @enderror" 
                           value="{{ old('violation_date', now()->format('Y-m-d\TH:i')) }}" 
                           required>
                    @error('violation_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mô tả hành vi vi phạm <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                          rows="5" 
                          placeholder="Mô tả chi tiết hành vi vi phạm..." 
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('student.chairman.violations.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Ghi nhận vi phạm
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
