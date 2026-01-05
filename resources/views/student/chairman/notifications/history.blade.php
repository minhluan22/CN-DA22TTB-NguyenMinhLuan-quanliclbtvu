@extends('layouts.chairman')

@section('title', 'Lịch sử thông báo - Chủ nhiệm CLB')

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
    
    .filter-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .table-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .pagination {
        margin: 20px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0;
        list-style: none;
        padding: 0;
    }

    .pagination .page-item {
        margin: 0 2px;
        list-style: none;
    }

    .pagination .page-link {
        color: #0B3D91;
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 6px 12px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.15s ease;
        min-width: 38px;
        text-align: center;
        display: inline-block;
        text-decoration: none;
        line-height: 1.42857143;
        cursor: pointer;
    }

    .pagination .page-link:hover:not(.disabled):not([aria-disabled="true"]) {
        color: white;
        background-color: #0B3D91;
        border-color: #0B3D91;
        text-decoration: none;
    }

    .pagination .page-item.active .page-link {
        color: white;
        background-color: #0B3D91;
        border-color: #0B3D91;
        font-weight: 600;
        cursor: default;
        z-index: 1;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        opacity: 0.6;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-clock-history"></i>
            Lịch sử thông báo
        </h1>
        <p class="page-subtitle">Xem lịch sử các thông báo nội bộ CLB đã gửi</p>
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

    <div class="filter-card">
        <form method="GET">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Loại thông báo</label>
                    <select name="type" class="form-control">
                        <option value="">-- Tất cả --</option>
                        <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Thông báo hệ thống</option>
                        <option value="regulation" {{ request('type') == 'regulation' ? 'selected' : '' }}>Thông báo nội quy</option>
                        <option value="administrative" {{ request('type') == 'administrative' ? 'selected' : '' }}>Thông báo hành chính</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Từ ngày</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Đến ngày</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Tiêu đề hoặc nội dung..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">🔍 Lọc</button>
                </div>
            </div>
            @if(request()->hasAny(['type', 'start_date', 'end_date', 'search']))
                <div class="row mt-2">
                    <div class="col-md-12">
                        <a href="{{ route('student.chairman.notifications.history') }}" class="btn btn-secondary btn-sm">Xóa bộ lọc</a>
                    </div>
                </div>
            @endif
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th>Số người nhận</th>
                        <th>Thời gian gửi</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr>
                            <td>{{ ($notifications->currentPage() - 1) * $notifications->perPage() + $loop->iteration }}</td>
                            <td><strong>{{ $notification->title }}</strong></td>
                            <td>
                                @if($notification->type == 'system')
                                    <span class="badge bg-secondary">Hệ thống</span>
                                @elseif($notification->type == 'regulation')
                                    <span class="badge bg-danger">Nội quy</span>
                                @elseif($notification->type == 'administrative')
                                    <span class="badge bg-primary">Hành chính</span>
                                @endif
                            </td>
                            <td>{{ $notification->recipient_count ?? 0 }}</td>
                            <td>{{ $notification->sent_at ? $notification->sent_at->format('d/m/Y H:i') : '—' }}</td>
                            <td>
                                <a href="{{ route('student.chairman.notifications.show', $notification->id) }}" 
                                   class="btn btn-sm btn-primary" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Không có thông báo nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $notifications->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
