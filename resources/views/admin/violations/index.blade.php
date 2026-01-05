@extends('layouts.admin')

@section('title', 'Danh sách vi phạm')

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

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: #1f1f1f;">
            <i class="bi bi-exclamation-triangle-fill" style="color: #ef4444;"></i> Danh sách vi phạm
        </h2>
    </div>

    {{-- THỐNG KÊ --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="bi bi-list-ul"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
                    <div class="stat-label">Tổng vi phạm</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['pending'] ?? 0 }}</div>
                    <div class="stat-label">Chưa xử lý</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['processed'] ?? 0 }}</div>
                    <div class="stat-label">Đã xử lý</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['monitoring'] ?? 0 }}</div>
                    <div class="stat-label">Đang theo dõi</div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card mb-4">
        <div class="filter-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel-fill"></i> Bộ lọc tìm kiếm
            </h5>
        </div>
        <div class="filter-body">
            <form method="GET" id="filterForm">
                <div class="row g-3">
            <div class="col-md-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="bi bi-search"></i> Từ khóa
                        </label>
                        <input type="text" name="search" class="form-control form-control-lg" 
                       value="{{ request('search') }}" 
                       placeholder="Tên sinh viên, MSSV, CLB...">
            </div>
            <div class="col-md-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="bi bi-building"></i> CLB
                        </label>
                        <select name="club_id" class="form-select form-select-lg">
                    <option value="">-- Tất cả CLB --</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                            {{ $club->code }} - {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                        <label class="form-label fw-semibold mb-2">
                            <i class="bi bi-bar-chart"></i> Mức độ
                        </label>
                        <select name="severity" class="form-select form-select-lg">
                    <option value="">-- Tất cả --</option>
                    <option value="light" {{ request('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                    <option value="serious" {{ request('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                </select>
            </div>
            <div class="col-md-2">
                        <label class="form-label fw-semibold mb-2">
                            <i class="bi bi-info-circle"></i> Trạng thái
                        </label>
                        <select name="status" class="form-select form-select-lg">
                    <option value="">-- Tất cả --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chưa xử lý</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Đã xử lý</option>
                    <option value="monitoring" {{ request('status') == 'monitoring' ? 'selected' : '' }}>Đang theo dõi</option>
                </select>
            </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold mb-2">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-funnel-fill"></i> Tìm kiếm
                </button>
            </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="d-flex gap-2">
                    <a href="{{ route('admin.violations.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                               class="btn btn-success">
                                <i class="bi bi-file-earmark-excel-fill"></i> Xuất Excel
                    </a>
                    <a href="{{ route('admin.violations.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                               class="btn btn-danger">
                                <i class="bi bi-file-earmark-pdf-fill"></i> Xuất PDF
                    </a>
                            @if(request()->anyFilled(['search', 'club_id', 'severity', 'status']))
                                <a href="{{ route('admin.violations.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Xóa bộ lọc
                                </a>
                            @endif
                </div>
            </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="table-card">
        <div class="table-header">
            <h5 class="mb-0">
                <i class="bi bi-table"></i> Danh sách vi phạm
                <span class="badge bg-primary ms-2">{{ $violations->total() }}</span>
            </h5>
        </div>
        <div class="table-body">
            <div class="table-responsive">
                <table class="violations-table">
                    <thead>
                <tr>
                            <th style="width: 60px;">STT</th>
                            <th style="width: 180px;">Sinh viên</th>
                            <th style="width: 150px;">CLB</th>
                            <th style="width: 180px;">Nội quy vi phạm</th>
                    <th>Mô tả</th>
                            <th style="width: 120px;">Mức độ</th>
                            <th style="width: 140px;">Thời gian</th>
                            <th style="width: 150px;">Người ghi nhận</th>
                            <th style="width: 140px;">Trạng thái</th>
                            <th style="width: 120px;" class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $index => $violation)
                            <tr class="{{ $violation->status == 'pending' ? 'row-pending' : '' }}">
                                <td class="text-center">
                                    <span class="stt-badge">{{ $violations->firstItem() + $index }}</span>
                                </td>
                        <td>
                                    <div class="student-info">
                                        <strong class="student-name">{{ $violation->user->name ?? 'N/A' }}</strong>
                            @if($violation->user->student_code ?? null)
                                            <div class="student-code">{{ $violation->user->student_code }}</div>
                            @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="club-info">
                                        <span class="club-code">{{ $violation->club->code ?? 'N/A' }}</span>
                                        <div class="club-name">{{ $violation->club->name ?? 'N/A' }}</div>
                                    </div>
                        </td>
                        <td>
                                    <div class="regulation-info">
                                        <strong class="regulation-code">{{ $violation->regulation->code ?? 'N/A' }}</strong>
                                        <div class="regulation-title">{{ Str::limit($violation->regulation->title ?? 'N/A', 40) }}</div>
                                    </div>
                        </td>
                        <td>
                                    <div class="description-text" title="{{ $violation->description }}">
                                        {{ Str::limit($violation->description, 80) }}
                                    </div>
                        </td>
                        <td>
                            @if($violation->severity == 'light')
                                        <span class="severity-badge severity-light">
                                            <i class="bi bi-circle-fill"></i> Nhẹ
                                        </span>
                            @elseif($violation->severity == 'medium')
                                        <span class="severity-badge severity-medium">
                                            <i class="bi bi-circle-fill"></i> Trung bình
                                        </span>
                            @else
                                        <span class="severity-badge severity-serious">
                                            <i class="bi bi-circle-fill"></i> Nghiêm trọng
                                        </span>
                            @endif
                        </td>
                        <td>
                                    <div class="date-info">
                                        <i class="bi bi-calendar3"></i>
                                        <span>{{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="time-info">
                                        <i class="bi bi-clock"></i>
                                        <span>{{ \Carbon\Carbon::parse($violation->violation_date)->format('H:i') }}</span>
                                    </div>
                        </td>
                        <td>
                                    <div class="recorder-info">
                                        <strong>{{ $violation->recorder->name ?? 'N/A' }}</strong>
                            @if($violation->recorder)
                                            <div class="recorder-role">Chủ nhiệm</div>
                            @endif
                                    </div>
                        </td>
                        <td>
                            @if($violation->status == 'pending')
                                        <span class="status-badge status-pending">
                                            <i class="bi bi-clock-history"></i> Chưa xử lý
                                        </span>
                            @elseif($violation->status == 'processed')
                                        <span class="status-badge status-processed">
                                            <i class="bi bi-check-circle"></i> Đã xử lý
                                        </span>
                                @if($violation->discipline_type)
                                            <div class="discipline-type">
                                                @if($violation->discipline_type == 'warning') ⚠️ Cảnh cáo
                                                @elseif($violation->discipline_type == 'reprimand') 📝 Khiển trách
                                                @elseif($violation->discipline_type == 'suspension') 🚫 Đình chỉ
                                                @elseif($violation->discipline_type == 'expulsion') ❌ Buộc rời
                                                @elseif($violation->discipline_type == 'ban') 🚷 Cấm tham gia
                                        @endif
                                            </div>
                                @endif
                            @else
                                        <span class="status-badge status-monitoring">
                                            <i class="bi bi-eye"></i> Đang theo dõi
                                        </span>
                            @endif
                        </td>
                        <td>
                                    <div class="action-buttons">
                                <a href="{{ route('admin.violations.show', $violation->id) }}" 
                                           class="btn-action btn-view" title="Xem chi tiết">
                                            <i class="bi bi-eye-fill"></i>
                                </a>
                                @if($violation->status != 'processed')
                                    <a href="{{ route('admin.violations.handle', $violation->id) }}" 
                                               class="btn-action btn-handle" title="Xử lý kỷ luật">
                                        <i class="bi bi-hammer"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox" style="font-size: 48px; color: #cbd5e1;"></i>
                                        <p class="mt-3 mb-0 text-muted">Không có vi phạm nào</p>
                                    </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

            {{-- PAGINATION --}}
            @if($violations->hasPages())
                <div class="table-footer">
                    <div class="d-flex justify-content-center">
                {{ $violations->links('vendor.pagination.custom') }}
            </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border-left: 4px solid;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
    .stat-card-primary {
        border-left-color: #0B3D91;
    }
    .stat-card-warning {
        border-left-color: #FFE600;
    }
    .stat-card-success {
        border-left-color: #5FB84A;
    }
    .stat-card-info {
        border-left-color: #0dcaf0;
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    .stat-card-primary .stat-icon {
        background: linear-gradient(135deg, #0B3D91 0%, #0C4CB8 100%);
    }
    .stat-card-warning .stat-icon {
        background: linear-gradient(135deg, #FFE600 0%, #FFD700 100%);
    }
    .stat-card-success .stat-icon {
        background: linear-gradient(135deg, #5FB84A 0%, #6EDC6E 100%);
    }
    .stat-card-info .stat-icon {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
    }
    .stat-content {
        flex: 1;
    }
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #1f1f1f;
        line-height: 1.2;
    }
    .stat-label {
        font-size: 14px;
        color: #6b7280;
        margin-top: 4px;
    }

    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .filter-header {
        background: linear-gradient(135deg, #0B3D91 0%, #0C4CB8 100%);
        color: white;
        padding: 16px 20px;
    }
    .filter-header h5 {
        color: white;
        margin: 0;
    }
    .filter-body {
        padding: 20px;
    }
    .form-label {
        font-size: 13px;
        color: #374151;
    }
    .form-control-lg, .form-select-lg {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: all 0.2s ease;
    }
    .form-control-lg:focus, .form-select-lg:focus {
        border-color: #0B3D91;
        box-shadow: 0 0 0 3px rgba(11, 61, 145, 0.1);
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .table-header {
        background: #f8f9fa;
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
    }
    .table-header h5 {
        color: #1f1f1f;
        margin: 0;
    }
    .table-body {
        padding: 0;
    }
    .table-footer {
        padding: 20px;
        border-top: 1px solid #e5e7eb;
        background: #f8f9fa;
    }

    /* Violations Table */
    .violations-table {
        width: 100%;
        border-collapse: collapse;
    }
    .violations-table thead {
        background: #f8f9fa;
    }
    .violations-table thead th {
        padding: 16px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }
    .violations-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    .violations-table tbody tr:hover {
        background: #f8f9fa;
    }
    .violations-table tbody tr.row-pending {
        background: #fffbf0;
    }
    .violations-table tbody tr.row-pending:hover {
        background: #fff8e1;
    }
    .violations-table tbody td {
        padding: 16px 12px;
        vertical-align: middle;
    }

    /* Table Content Styles */
    .stt-badge {
        display: inline-block;
        width: 32px;
        height: 32px;
        line-height: 32px;
        text-align: center;
        background: #f3f4f6;
        border-radius: 8px;
        font-weight: 600;
        color: #6b7280;
    }
    .student-info {
        display: flex;
        flex-direction: column;
    }
    .student-name {
        color: #1f1f1f;
        font-size: 14px;
    }
    .student-code {
        color: #6b7280;
        font-size: 12px;
        margin-top: 2px;
    }
    .club-info {
        display: flex;
        flex-direction: column;
    }
    .club-code {
        color: #0B3D91;
        font-weight: 600;
        font-size: 12px;
    }
    .club-name {
        color: #374151;
        font-size: 13px;
        margin-top: 2px;
    }
    .regulation-info {
        display: flex;
        flex-direction: column;
    }
    .regulation-code {
        color: #ef4444;
        font-size: 13px;
    }
    .regulation-title {
        color: #6b7280;
        font-size: 12px;
        margin-top: 2px;
    }
    .description-text {
        color: #374151;
        font-size: 13px;
        line-height: 1.5;
    }
    .date-info, .time-info {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #6b7280;
    }
    .time-info {
        margin-top: 4px;
    }
    .recorder-info {
        display: flex;
        flex-direction: column;
    }
    .recorder-role {
        color: #6b7280;
        font-size: 12px;
        margin-top: 2px;
    }

    /* Badges */
    .severity-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .severity-badge i {
        font-size: 8px;
    }
    .severity-light {
        background: #d1fae5;
        color: #065f46;
    }
    .severity-light i {
        color: #10b981;
    }
    .severity-medium {
        background: #fef3c7;
        color: #92400e;
    }
    .severity-medium i {
        color: #f59e0b;
    }
    .severity-serious {
        background: #fee2e2;
        color: #991b1b;
    }
    .severity-serious i {
        color: #ef4444;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .status-processed {
        background: #d1fae5;
        color: #065f46;
    }
    .status-monitoring {
        background: #dbeafe;
        color: #1e40af;
    }
    .discipline-type {
        font-size: 11px;
        color: #6b7280;
        margin-top: 4px;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }
    .btn-action {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
    }
    .btn-view {
        background: #0B3D91;
        color: white;
    }
    .btn-view:hover {
        background: #0C4CB8;
        transform: scale(1.05);
        color: white;
    }
    .btn-handle {
        background: #FFE600;
        color: #1f1f1f;
    }
    .btn-handle:hover {
        background: #FFD700;
        transform: scale(1.05);
        color: #1f1f1f;
    }

    /* Empty State */
    .empty-state {
        padding: 40px 20px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .violations-table {
            font-size: 12px;
        }
        .violations-table thead th,
        .violations-table tbody td {
            padding: 12px 8px;
        }
    }
</style>

@endsection
