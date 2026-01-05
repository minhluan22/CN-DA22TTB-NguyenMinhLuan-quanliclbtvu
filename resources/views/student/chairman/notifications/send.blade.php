@extends('layouts.chairman')

@section('title', 'Gửi thông báo nội bộ CLB - Chủ nhiệm')

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
        max-width: 1600px;
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
    
    .page-subtitle {
        font-size: 14px;
        color: #6b7280;
        margin-top: 8px;
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
            <i class="bi bi-send"></i>
            Gửi thông báo nội bộ CLB
        </h1>
        <p class="page-subtitle">Gửi thông báo đến tất cả thành viên CLB của bạn</p>
    </div>

    <!-- Club Info Card -->
    @php
        $club = \App\Models\Club::find($chairmanClub->id);
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
                    @if($club && $club->status === 'active')
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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="form-card">
        <form method="POST" action="{{ route('student.chairman.notifications.store') }}" id="notificationForm">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Tiêu đề thông báo <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" 
                       value="{{ old('title') }}" 
                       placeholder="Nhập tiêu đề thông báo..." 
                       required>
                @error('title')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Nội dung chi tiết <span class="text-danger">*</span></label>
                <textarea name="body" class="form-control" rows="8" 
                          placeholder="Nhập nội dung thông báo..." 
                          required>{{ old('body') }}</textarea>
                @error('body')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Loại thông báo <span class="text-danger">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="">-- Chọn loại --</option>
                        <option value="meeting" {{ old('type') == 'meeting' ? 'selected' : '' }}>Thông báo họp CLB</option>
                        <option value="activity" {{ old('type') == 'activity' ? 'selected' : '' }}>Thông báo hoạt động – sự kiện</option>
                        <option value="reminder" {{ old('type') == 'reminder' ? 'selected' : '' }}>Nhắc nhở tham gia</option>
                    </select>
                    @error('type')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Thời gian gửi <span class="text-danger">*</span></label>
                    <select name="send_option" id="send_option" class="form-control" required>
                        <option value="now" {{ old('send_option') == 'now' ? 'selected' : '' }}>Gửi ngay</option>
                        <option value="scheduled" {{ old('send_option') == 'scheduled' ? 'selected' : '' }}>Lên lịch gửi</option>
                    </select>
                </div>
            </div>

            <div class="mb-3" id="scheduled_at_field" style="display: none;">
                <label class="form-label fw-bold">Thời gian gửi <span class="text-danger">*</span></label>
                <input type="datetime-local" name="scheduled_at" class="form-control" 
                       value="{{ old('scheduled_at') }}">
                @error('scheduled_at')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                <strong>Lưu ý:</strong> Thông báo sẽ được gửi đến tất cả thành viên CLB <strong>{{ $chairmanClub->name }}</strong>.
                Sau khi gửi, bạn không thể chỉnh sửa nội dung thông báo.
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('student.chairman.notifications.inbox') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">Gửi thông báo</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('send_option').addEventListener('change', function() {
        const scheduledField = document.getElementById('scheduled_at_field');
        if (this.value === 'scheduled') {
            scheduledField.style.display = 'block';
        } else {
            scheduledField.style.display = 'none';
        }
    });

    // Trigger on page load
    document.getElementById('send_option').dispatchEvent(new Event('change'));
</script>
@endpush
