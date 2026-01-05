@extends('layouts.chairman')

@section('title', 'Tạo hoạt động mới - Chủ nhiệm')

@push('styles')
<style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #072C6A;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF7B0;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .form-card {
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .info-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .form-label {
            margin-bottom: 8px;
        }
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(11, 61, 145, 0.25);
        }
    </style>
@endpush

@section('content')
<div class="fade-in">
        <div class="page-header">
            <h3 class="fw-bold mb-0">
                <i class="bi bi-plus-circle"></i>
                Tạo hoạt động mới
            </h3>
            <div class="badge bg-primary" style="font-size: 14px; padding: 10px 16px;">
                <i class="bi bi-building"></i> CLB: {{ $club->name }} ({{ $club->code }})
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
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

        <div class="form-card" style="background: white; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <form action="{{ route('student.chairman.store-event') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="club_id" value="{{ $club->id }}">

                {{-- CLB (Hiển thị thông tin) --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">CLB <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="{{ $club->name }} ({{ $club->code }})" readonly 
                           style="background-color: #e9ecef;">
                </div>

                {{-- TÊN HOẠT ĐỘNG --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên hoạt động <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" 
                           placeholder="Nhập tên hoạt động ngắn gọn" required>
                </div>

                {{-- LOẠI HOẠT ĐỘNG --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Loại hoạt động <span class="text-danger">*</span></label>
                    <select name="activity_type" class="form-control" required>
                        <option value="">-- Chọn loại hoạt động --</option>
                        <option value="academic" {{ old('activity_type') == 'academic' ? 'selected' : '' }}>Học thuật</option>
                        <option value="arts" {{ old('activity_type') == 'arts' ? 'selected' : '' }}>Văn nghệ</option>
                        <option value="volunteer" {{ old('activity_type') == 'volunteer' ? 'selected' : '' }}>Tình nguyện</option>
                        <option value="other" {{ old('activity_type') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>

                {{-- MỤC TIÊU --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Mục tiêu <span class="text-danger">*</span></label>
                    <textarea name="goal" class="form-control" rows="3" 
                              placeholder="Nhập mục đích của hoạt động" required>{{ old('goal') }}</textarea>
                </div>

                {{-- NỘI DUNG CHI TIẾT --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung chi tiết <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="5" 
                              placeholder="Nhập kế hoạch chi tiết của hoạt động" required>{{ old('description') }}</textarea>
                </div>

                {{-- THỜI GIAN --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="start_at" class="form-control" 
                               value="{{ old('start_at') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Thời gian kết thúc</label>
                        <input type="datetime-local" name="end_at" class="form-control" 
                               value="{{ old('end_at') }}">
                    </div>
                </div>

                {{-- ĐỊA ĐIỂM --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
                    <input type="text" name="location" class="form-control" 
                           value="{{ old('location') }}" 
                           placeholder="Nhập nơi tổ chức hoạt động" required>
                </div>

                {{-- SỐ LƯỢNG DỰ KIẾN (TÙY CHỌN) --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Số lượng dự kiến</label>
                    <input type="number" name="expected_participants" class="form-control" 
                           value="{{ old('expected_participants') }}" 
                           placeholder="Ước tính số người tham gia" min="1">
                </div>

                {{-- KINH PHÍ DỰ KIẾN (TÙY CHỌN) --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Kinh phí dự kiến</label>
                    <input type="number" name="expected_budget" class="form-control" 
                           value="{{ old('expected_budget') }}" 
                           placeholder="Nhập kinh phí dự kiến (VNĐ)" min="0" step="1000">
                </div>

                {{-- FILE ĐÍNH KÈM (TÙY CHỌN) --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">File đính kèm</label>
                    <input type="file" name="attachment" class="form-control" 
                           accept=".pdf,.doc,.docx">
                    <small class="text-muted">Chấp nhận file PDF, DOC, DOCX (tối đa 5MB)</small>
                </div>

                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Hoạt động sẽ được tạo trực tiếp và hiển thị ngay trong danh sách hoạt động.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Tạo hoạt động
                    </button>
                    <a href="{{ route('student.chairman.approved-events') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
</div>
@endsection

