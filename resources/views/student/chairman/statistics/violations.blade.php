@extends('layouts.chairman')

@section('title', 'Thống kê vi phạm CLB - Chủ nhiệm CLB')

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
            <i class="bi bi-exclamation-triangle"></i>
            Thống kê vi phạm CLB
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
        <div class="col-md-4">
            <div class="stat-card">
                <i class="bi bi-exclamation-circle fs-1 text-danger"></i>
                <div class="stat-number">{{ $totalViolations ?? 0 }}</div>
                <div class="stat-label">Tổng vi phạm</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <i class="bi bi-clock-history fs-1 text-warning"></i>
                <div class="stat-number">{{ $pendingViolations ?? 0 }}</div>
                <div class="stat-label">Đang xử lý</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <i class="bi bi-check-circle fs-1 text-success"></i>
                <div class="stat-number">{{ $processedViolations ?? 0 }}</div>
                <div class="stat-label">Đã xử lý</div>
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
                <label class="form-label fw-bold">Mức độ</label>
                <select name="severity" class="form-control" onchange="this.form.submit()">
                    <option value="all">-- Tất cả --</option>
                    <option value="light" {{ request('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                    <option value="serious" {{ request('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Trạng thái</label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="all">-- Tất cả --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Đã xử lý</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Từ ngày</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="col-md-2">
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
                    <th>Loại vi phạm</th>
                    <th>Mức độ</th>
                    <th>Trạng thái</th>
                    <th>Ngày phát hiện</th>
                    <th>Người ghi nhận</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $index => $violation)
                    <tr>
                        <td>{{ ($violations->currentPage() - 1) * $violations->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $violation->title }}</strong></td>
                        <td>{{ $violation->violation_type ?? 'Chưa xác định' }}</td>
                        <td>
                            @if($violation->violation_severity == 'light')
                                <span class="badge bg-warning text-dark">Nhẹ</span>
                            @elseif($violation->violation_severity == 'medium')
                                <span class="badge bg-info">Trung bình</span>
                            @elseif($violation->violation_severity == 'serious')
                                <span class="badge bg-danger">Nghiêm trọng</span>
                            @else
                                <span class="badge bg-secondary">Chưa xác định</span>
                            @endif
                        </td>
                        <td>
                            @if($violation->violation_status == 'pending')
                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                            @elseif($violation->violation_status == 'processing')
                                <span class="badge bg-info">Đang xử lý</span>
                            @elseif($violation->violation_status == 'processed')
                                <span class="badge bg-success">Đã xử lý</span>
                            @else
                                <span class="badge bg-secondary">Chưa xác định</span>
                            @endif
                        </td>
                        <td>
                            @if($violation->violation_detected_at)
                                {{ \Carbon\Carbon::parse($violation->violation_detected_at)->format('d/m/Y H:i') }}
                            @else
                                Chưa có
                            @endif
                        </td>
                        <td>
                            @if($violation->violation_recorded_by)
                                @php
                                    $recorder = \App\Models\User::find($violation->violation_recorded_by);
                                @endphp
                                {{ $recorder->name ?? 'Không xác định' }}
                            @else
                                Chưa có
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Chưa có dữ liệu vi phạm</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($violations->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $violations->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
