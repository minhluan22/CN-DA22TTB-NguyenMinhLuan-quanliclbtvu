@extends('layouts.chairman')

@section('title', 'Thống kê hoạt động CLB - Chủ nhiệm CLB')

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
    
    .stat-card {
        text-align: center;
        padding: 20px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .stat-number {
        font-size: 32px;
        font-weight: bold;
        color: #0033A0;
        margin: 10px 0;
    }
    
    .stat-label {
        font-size: 14px;
        color: #6b7280;
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
    
    .table-role tbody tr {
        transition: all 0.3s ease;
    }
    
    .table-role tbody tr:hover {
        background: #f8fafc;
        transform: translateY(-1px);
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
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
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
            <i class="bi bi-calendar-event"></i>
            Thống kê hoạt động CLB
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

    {{-- SUMMARY CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <i class="bi bi-calendar-check fs-1 text-primary"></i>
                <div class="stat-number">{{ $totalEvents ?? 0 }}</div>
                <div class="stat-label">Tổng hoạt động</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <i class="bi bi-calendar-plus fs-1 text-info"></i>
                <div class="stat-number">{{ $upcomingEvents ?? 0 }}</div>
                <div class="stat-label">Sắp diễn ra</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <i class="bi bi-calendar-play fs-1 text-warning"></i>
                <div class="stat-number">{{ $ongoingEvents ?? 0 }}</div>
                <div class="stat-label">Đang diễn ra</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <i class="bi bi-calendar-check2 fs-1 text-success"></i>
                <div class="stat-number">{{ $finishedEvents ?? 0 }}</div>
                <div class="stat-label">Đã kết thúc</div>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="filter-card">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoạt động..." class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Trạng thái</label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="all">-- Tất cả --</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="finished" {{ request('status') == 'finished' ? 'selected' : '' }}>Đã kết thúc</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Từ ngày</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Đến ngày</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="table-card">
        <table class="table table-role">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên hoạt động</th>
                    <th>Mô tả</th>
                    <th>Thời gian</th>
                    <th>Địa điểm</th>
                    <th>Trạng thái</th>
                    <th>Người tham gia</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $index => $event)
                    <tr>
                        <td>{{ ($events->currentPage() - 1) * $events->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $event->title }}</strong></td>
                        <td>{{ Str::limit($event->description ?? 'Chưa có mô tả', 35) }}</td>
                        <td>
                            @if($event->start_at)
                                {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                                @if($event->end_at)
                                    <br><small>→ {{ \Carbon\Carbon::parse($event->end_at)->format('d/m/Y H:i') }}</small>
                                @endif
                            @else
                                Chưa cập nhật
                            @endif
                        </td>
                        <td>{{ $event->location ?? 'Chưa cập nhật' }}</td>
                        <td>
                            @if($event->status == 'upcoming')
                                <span class="badge bg-primary">Sắp diễn ra</span>
                            @elseif($event->status == 'ongoing')
                                <span class="badge bg-info">Đang diễn ra</span>
                            @elseif($event->status == 'finished')
                                <span class="badge bg-success">Đã kết thúc</span>
                            @elseif($event->status == 'cancelled')
                                <span class="badge bg-danger">Đã hủy</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $event->participant_count ?? 0 }} người</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Chưa có dữ liệu</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($events->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $events->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
