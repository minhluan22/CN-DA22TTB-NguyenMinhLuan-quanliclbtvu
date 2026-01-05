@extends('layouts.chairman')

@section('title', 'Chi tiết thông báo - Chủ nhiệm CLB')

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
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="page-title">
                <i class="bi bi-bell"></i>
                Chi tiết thông báo
            </h1>
            <a href="{{ route('student.chairman.notifications.inbox') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
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

    <div class="detail-card">
        <div class="mb-4">
            <h4 class="fw-bold">{{ $notification->title }}</h4>
            <div class="text-muted small">
                <i class="bi bi-person"></i> Người gửi: <strong>{{ $notification->sender->name ?? 'Hệ thống' }}</strong> | 
                <i class="bi bi-clock"></i> Thời gian: <strong>{{ $notification->sent_at ? $notification->sent_at->format('d/m/Y H:i') : 'Chưa gửi' }}</strong>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Nguồn:</label>
            <div>
                @if($notification->notification_source === 'admin')
                    <span class="badge bg-primary">Từ Admin</span>
                @else
                    <span class="badge bg-success">Nội bộ CLB</span>
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Loại thông báo:</label>
            <div>
                @if($notification->type == 'system')
                    <span class="badge bg-secondary">Thông báo hệ thống</span>
                @elseif($notification->type == 'regulation')
                    <span class="badge bg-danger">Thông báo nội quy</span>
                @elseif($notification->type == 'administrative')
                    <span class="badge bg-primary">Thông báo hành chính</span>
                @endif
            </div>
        </div>

        @if($notification->notification_source === 'admin' && $notification->target_type === 'clubs')
            <div class="mb-3">
                <label class="form-label fw-bold">CLB nhận:</label>
                <div>
                    @php
                        $selectedClubs = \App\Models\Club::whereIn('id', $notification->target_ids ?? [])->get();
                    @endphp
                    @foreach($selectedClubs as $club)
                        <span class="badge bg-light text-dark me-1">{{ $club->code }} - {{ $club->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label fw-bold">Nội dung:</label>
            <div class="border rounded p-3 bg-light">
                {!! nl2br(e($notification->body)) !!}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-primary">{{ $notification->total_recipients ?? 0 }}</h5>
                        <p class="text-muted mb-0">Tổng người nhận</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-success">{{ $notification->read_count ?? 0 }}</h5>
                        <p class="text-muted mb-0">Đã đọc</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-warning">{{ $notification->unread_count ?? 0 }}</h5>
                        <p class="text-muted mb-0">Chưa đọc</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
