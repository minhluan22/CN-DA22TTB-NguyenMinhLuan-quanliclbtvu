@extends('layouts.chairman')

@section('title', 'Duyệt hoạt động - Chủ nhiệm')

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
    
    .table-role {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .table-role thead {
        background: #eaf2ff;
        color: #0B3D91;
    }
    
    .table-role thead th {
        background: #eaf2ff !important;
        color: #0B3D91 !important;
        font-weight: 700;
    }
    
    .event-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .event-title {
        font-size: 20px;
        font-weight: 700;
        color: #0033A0;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .event-meta {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .event-meta span {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }
    
    .empty-state h4 {
        color: #374151;
        margin-bottom: 8px;
    }
    
    .empty-state p {
        color: #6b7280;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-check-circle"></i>
            Duyệt hoạt động
        </h1>
    </div>

    <!-- Club Info Card -->
    <div class="club-info-card">
        <div class="club-info-grid">
            <div class="club-info-item">
                <span class="club-info-label">Tên Câu lạc bộ</span>
                <span class="club-info-value">{{ $club->name }}</span>
            </div>
            <div class="club-info-item">
                <span class="club-info-label">Mã CLB</span>
                <span class="club-info-value">{{ $club->code }}</span>
            </div>
            <div class="club-info-item">
                <span class="club-info-label">Trạng thái</span>
                <span class="club-info-value">
                    @if($club->status === 'active')
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

    @if(count($pendingApprovals) > 0)
        @foreach($pendingApprovals as $item)
            <div class="event-card">
                <div class="event-title">
                    <i class="bi bi-calendar-event"></i>
                    {{ $item['event']->title }}
                </div>
                <div class="event-meta">
                    <span>
                        <i class="bi bi-calendar3"></i>
                        {{ \Carbon\Carbon::parse($item['event']->start_at)->format('d/m/Y H:i') }}
                    </span>
                    <span>
                        <i class="bi bi-geo-alt"></i>
                        {{ $item['event']->location ?? 'Chưa cập nhật' }}
                    </span>
                </div>

                <div class="table-role">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Họ tên</th>
                                <th>MSSV</th>
                                <th>Trạng thái</th>
                                <th>Điểm hoạt động</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item['registrations'] as $index => $reg)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $reg->name }}</strong></td>
                                    <td>{{ $reg->student_code }}</td>
                                    <td>
                                        @if($reg->status === 'approved')
                                            <span class="badge bg-success">Đã đăng ký</span>
                                        @elseif($reg->status === 'attended')
                                            <span class="badge bg-info">Đã tham gia</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('student.chairman.approve-activity-points', $reg->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <div class="input-group" style="width: 150px;">
                                                <input type="number" name="activity_points" class="form-control form-control-sm" min="0" max="100" value="0" required>
                                                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Xác nhận duyệt điểm hoạt động?')">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="text-muted">Chờ duyệt</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h4>Không có hoạt động nào cần duyệt</h4>
            <p>Hiện tại không có hoạt động nào cần duyệt điểm hoạt động</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
