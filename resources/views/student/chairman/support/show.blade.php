@extends('layouts.chairman')

@section('title', 'Chi tiết yêu cầu hỗ trợ - Chủ nhiệm CLB')

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
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    
    .detail-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-open { background: #FFE600; color: #000; }
    .badge-in_progress { background: #0B3D91; color: white; }
    .badge-resolved { background: #5FB84A; color: white; }
    .badge-closed { background: #6b7280; color: white; }
    .badge-high { background: #B84A5F; color: white; }
    .badge-medium { background: #FFE600; color: #000; }
    .badge-low { background: #8EDC6E; color: #000; }
    
    .response-box {
        background: #E6F0FF;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #0B3D91;
        margin-top: 16px;
    }

    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }

    .modal-header {
        background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
        border-bottom: none;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.9;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
    }

    .modal-title {
        font-weight: 700;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-body {
        padding: 24px;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0B3D91;
        box-shadow: 0 0 0 3px rgba(11, 61, 145, 0.1);
    }

    .modal-footer {
        border-top: 2px solid #e5e7eb;
        padding: 16px 24px;
    }

    .btn-modal-cancel {
        padding: 10px 20px;
        border: 2px solid #e5e7eb;
        background: white;
        color: #1f1f1f;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-modal-cancel:hover {
        background: #f9fafb;
        border-color: #6b7280;
    }

    .btn-modal-submit {
        padding: 10px 20px;
        background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-modal-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(11, 61, 145, 0.3);
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-headset"></i>
            Chi tiết yêu cầu hỗ trợ
        </h1>
        <div style="display: flex; gap: 12px; align-items: center;">
            @if(isset($isAdmin) && $isAdmin || isset($isChairman) && $isChairman)
                @if(!$request->admin_response)
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#respondModal">
                        <i class="bi bi-reply"></i> Trả lời hỗ trợ
                    </button>
                @endif
            @endif
            <a href="{{ route('student.chairman.support.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Club Info Card -->
    @if($chairmanClub)
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
    @endif

    <div class="detail-card">
        @if($request->club)
            <div class="mb-3">
                <label class="form-label fw-bold">CLB:</label>
                <p>
                    <span class="badge bg-info">{{ $request->club->code }}</span>
                    {{ $request->club->name }}
                </p>
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label fw-bold">Tiêu đề:</label>
            <p style="font-size: 18px; margin-top: 8px;">{{ $request->subject }}</p>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Mức độ ưu tiên:</label>
            <span class="badge badge-{{ $request->priority }}" style="margin-left: 12px;">
                {{ $request->priority_label }}
            </span>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Trạng thái:</label>
            <span class="badge badge-{{ $request->status }}" style="margin-left: 12px;">
                {{ $request->status_label }}
            </span>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Ngày gửi:</label>
            <p style="margin-top: 8px;">{{ $request->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Nội dung:</label>
            <div style="background: #f9fafb; padding: 16px; border-radius: 8px; margin-top: 8px; white-space: pre-wrap;">
                {{ $request->content }}
            </div>
        </div>

        @if($request->admin_response)
            <div class="response-box">
                <strong style="color: #0B3D91;">
                    <i class="bi bi-reply"></i> Phản hồi từ {{ $request->responder ? $request->responder->name : 'Admin' }}:
                </strong>
                <p style="margin-top: 12px; white-space: pre-wrap;">{{ $request->admin_response }}</p>
                @if($request->responded_at)
                    <small style="color: #6b7280;">
                        Phản hồi lúc: {{ $request->responded_at->format('d/m/Y H:i') }}
                    </small>
                @endif
            </div>
        @else
            <div class="alert alert-warning">
                <i class="bi bi-clock"></i> Yêu cầu đang được xử lý. Vui lòng chờ phản hồi.
            </div>
        @endif
    </div>
</div>

<!-- Modal Trả lời hỗ trợ -->
@if(isset($isAdmin) && $isAdmin || isset($isChairman) && $isChairman)
    <div class="modal fade" id="respondModal" tabindex="-1" aria-labelledby="respondModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="respondModalLabel">
                        <i class="bi bi-reply"></i> Trả lời yêu cầu hỗ trợ
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('student.chairman.support.respond', $request->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="admin_response" class="form-label">
                                <i class="bi bi-chat-text"></i> Nội dung phản hồi <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control" 
                                id="admin_response" 
                                name="admin_response" 
                                rows="6" 
                                required
                                minlength="10"
                                placeholder="Nhập nội dung phản hồi (tối thiểu 10 ký tự)..."
                            >{{ old('admin_response') }}</textarea>
                            @error('admin_response')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">
                                <i class="bi bi-info-circle"></i> Trạng thái <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="open" {{ old('status', $request->status) == 'open' ? 'selected' : '' }}>Mở</option>
                                <option value="in_progress" {{ old('status', $request->status) == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="resolved" {{ old('status', $request->status) == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                                <option value="closed" {{ old('status', $request->status) == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                            </select>
                            @error('status')
                                <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Hủy
                        </button>
                        <button type="submit" class="btn-modal-submit">
                            <i class="bi bi-send"></i> Gửi phản hồi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
