@extends('layouts.admin')

@section('title', 'Tạo nội quy mới')

@section('content')

<div class="container-fluid mt-3">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>❌ Lỗi!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-plus-circle"></i> Tạo nội quy mới
        </h3>
        <a href="{{ route('admin.regulations.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.regulations.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Mã nội quy <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" 
                           value="{{ old('code') }}" 
                           placeholder="VD: NQ001, NQ-HN-2025" required>
                    <small class="text-muted">Mã duy nhất để nhận diện nội quy</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Ngày ban hành <span class="text-danger">*</span></label>
                    <input type="date" name="issued_date" class="form-control" 
                           value="{{ old('issued_date', date('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tiêu đề nội quy <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title') }}" 
                       placeholder="Nhập tiêu đề nội quy" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Nội dung chi tiết <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="8" 
                          placeholder="Nhập nội dung chi tiết của nội quy..." required>{{ old('content') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Phạm vi áp dụng <span class="text-danger">*</span></label>
                    <select name="scope" class="form-control" id="scope-select" required>
                        <option value="">-- Chọn phạm vi --</option>
                        <option value="all_clubs" {{ old('scope') == 'all_clubs' ? 'selected' : '' }}>Toàn hệ thống CLB</option>
                        <option value="specific_club" {{ old('scope') == 'specific_club' ? 'selected' : '' }}>CLB cụ thể</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3" id="club-select-wrapper" style="display: none;">
                    <label class="form-label fw-bold">CLB <span class="text-danger">*</span></label>
                    <select name="club_id" class="form-control" id="club-select">
                        <option value="">-- Chọn CLB --</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->code }} - {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Mức độ <span class="text-danger">*</span></label>
                    <select name="severity" class="form-control" required>
                        <option value="">-- Chọn mức độ --</option>
                        <option value="light" {{ old('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                        <option value="medium" {{ old('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="serious" {{ old('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Đang áp dụng</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Ngừng áp dụng</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn" style="background: #0B3D91; color: white; border: none;">
                    <i class="bi bi-save"></i> Tạo nội quy
                </button>
                <a href="{{ route('admin.regulations.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x"></i> Hủy
                </a>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scopeSelect = document.getElementById('scope-select');
    const clubSelectWrapper = document.getElementById('club-select-wrapper');
    const clubSelect = document.getElementById('club-select');

    function toggleClubSelect() {
        if (scopeSelect.value === 'specific_club') {
            clubSelectWrapper.style.display = 'block';
            clubSelect.setAttribute('required', 'required');
        } else {
            clubSelectWrapper.style.display = 'none';
            clubSelect.removeAttribute('required');
            clubSelect.value = '';
        }
    }

    scopeSelect.addEventListener('change', toggleClubSelect);
    toggleClubSelect(); // Initialize on page load
});
</script>

@endsection

