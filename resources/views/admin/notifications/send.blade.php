@extends('layouts.admin')

@section('title', 'Gửi thông báo')

@section('content')

<div class="container-fluid mt-3">
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

    <h3 class="fw-bold mb-4">
        <i class="bi bi-send"></i> Gửi thông báo
    </h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.notifications.store') }}" id="notificationForm">
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
                        <select name="type" class="form-select" required>
                            <option value="">-- Chọn loại --</option>
                            <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>Thông báo hệ thống</option>
                            <option value="regulation" {{ old('type') == 'regulation' ? 'selected' : '' }}>Thông báo nội quy – quy định</option>
                            <option value="administrative" {{ old('type') == 'administrative' ? 'selected' : '' }}>Thông báo hành chính</option>
                        </select>
                        @error('type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Đối tượng nhận <span class="text-danger">*</span></label>
                        <select name="target_type" id="target_type" class="form-select" required>
                            <option value="">-- Chọn đối tượng --</option>
                            <option value="all" {{ old('target_type') == 'all' ? 'selected' : '' }}>Toàn bộ người dùng</option>
                            <option value="students" {{ old('target_type') == 'students' ? 'selected' : '' }}>Tất cả sinh viên</option>
                            <option value="chairmen" {{ old('target_type') == 'chairmen' ? 'selected' : '' }}>Tất cả Chủ nhiệm CLB</option>
                            <option value="clubs" {{ old('target_type') == 'clubs' ? 'selected' : '' }}>CLB cụ thể</option>
                        </select>
                        @error('target_type')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3" id="clubs_selection" style="display: none;">
                    <label class="form-label fw-bold">Chọn CLB <span class="text-danger">*</span></label>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; padding: 10px;">
                        @foreach($clubs as $club)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="target_ids[]" 
                                       value="{{ $club->id }}" 
                                       id="club_{{ $club->id }}"
                                       {{ in_array($club->id, old('target_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="club_{{ $club->id }}">
                                    {{ $club->code }} - {{ $club->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('target_ids')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Thời gian gửi</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="send_option" id="send_now" value="now" checked>
                        <label class="form-check-label" for="send_now">
                            Gửi ngay
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="send_option" id="send_scheduled" value="scheduled">
                        <label class="form-check-label" for="send_scheduled">
                            Lên lịch gửi sau
                        </label>
                    </div>
                </div>

                <div class="mb-3" id="scheduled_time" style="display: none;">
                    <label class="form-label fw-bold">Thời gian gửi</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" 
                           value="{{ old('scheduled_at') }}"
                           min="{{ now()->format('Y-m-d\TH:i') }}">
                    @error('scheduled_at')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <strong>📌 Lưu ý:</strong> Sau khi thông báo được gửi, bạn không thể chỉnh sửa nội dung để đảm bảo tính minh bạch.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Gửi thông báo
                    </button>
                    <a href="{{ route('admin.notifications.inbox') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetType = document.getElementById('target_type');
        const clubsSelection = document.getElementById('clubs_selection');
        const sendNow = document.getElementById('send_now');
        const sendScheduled = document.getElementById('send_scheduled');
        const scheduledTime = document.getElementById('scheduled_time');

        // Hiển thị/ẩn chọn CLB
        targetType.addEventListener('change', function() {
            if (this.value === 'clubs') {
                clubsSelection.style.display = 'block';
            } else {
                clubsSelection.style.display = 'none';
                // Bỏ chọn tất cả checkbox
                document.querySelectorAll('input[name="target_ids[]"]').forEach(cb => cb.checked = false);
            }
        });

        // Trigger on load
        if (targetType.value === 'clubs') {
            clubsSelection.style.display = 'block';
        }

        // Hiển thị/ẩn thời gian lên lịch
        sendNow.addEventListener('change', function() {
            if (this.checked) {
                scheduledTime.style.display = 'none';
            }
        });

        sendScheduled.addEventListener('change', function() {
            if (this.checked) {
                scheduledTime.style.display = 'block';
            }
        });

        // Validation form
        document.getElementById('notificationForm').addEventListener('submit', function(e) {
            if (targetType.value === 'clubs') {
                const checkedClubs = document.querySelectorAll('input[name="target_ids[]"]:checked');
                if (checkedClubs.length === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một CLB!');
                    return false;
                }
            }
        });
    });
</script>

@endsection

