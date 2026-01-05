@extends('layouts.admin')

@section('title', 'Hộp thư thông báo')

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
        <i class="bi bi-inbox"></i> Hộp thư thông báo
    </h3>

    {{-- FILTER FORM --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label fw-bold mb-1">Loại thông báo</label>
                        <select name="type" class="form-select form-select-sm">
                        <option value="">-- Tất cả --</option>
                        <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Thông báo hệ thống</option>
                        <option value="regulation" {{ request('type') == 'regulation' ? 'selected' : '' }}>Thông báo nội quy</option>
                        <option value="administrative" {{ request('type') == 'administrative' ? 'selected' : '' }}>Thông báo hành chính</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold mb-1">Đối tượng nhận</label>
                    <select name="target_type" class="form-select form-select-sm">
                        <option value="">-- Tất cả --</option>
                        <option value="all" {{ request('target_type') == 'all' ? 'selected' : '' }}>Toàn bộ người dùng</option>
                        <option value="students" {{ request('target_type') == 'students' ? 'selected' : '' }}>Sinh viên</option>
                        <option value="chairmen" {{ request('target_type') == 'chairmen' ? 'selected' : '' }}>Chủ nhiệm CLB</option>
                        <option value="clubs" {{ request('target_type') == 'clubs' ? 'selected' : '' }}>CLB cụ thể</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold mb-1">Nguồn thông báo</label>
                    <select name="source" class="form-select form-select-sm">
                        <option value="">-- Tất cả --</option>
                        <option value="admin" {{ request('source') == 'admin' ? 'selected' : '' }}>Từ Admin</option>
                        <option value="support" {{ request('source') == 'support' ? 'selected' : '' }}>Yêu cầu hỗ trợ</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold mb-1">Từ ngày</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold mb-1">Đến ngày</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold mb-1">Sắp xếp</label>
                    <select name="sort_by" class="form-select form-select-sm">
                        <option value="sent_at" {{ request('sort_by') == 'sent_at' ? 'selected' : '' }}>Thời gian gửi</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Thời gian tạo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold mb-1">Thứ tự</label>
                    <select name="sort_order" class="form-select form-select-sm">
                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Cũ nhất</option>
                    </select>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i> Lọc
                    </button>
                    @if(request()->hasAny(['type', 'target_type', 'start_date', 'end_date', 'source']))
                        <a href="{{ route('admin.notifications.inbox') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-clockwise"></i> Đặt lại
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề</th>
                            <th>Người gửi</th>
                            <th>Đối tượng nhận</th>
                            <th>Loại</th>
                            <th>Thời gian gửi</th>
                            <th>Trạng thái đọc</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr>
                                <td>{{ ($notifications->currentPage() - 1) * $notifications->perPage() + $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $notification->title }}</strong>
                                </td>
                                <td>{{ $notification->sender->name ?? 'Hệ thống' }}</td>
                                <td>
                                    @if($notification->target_type == 'all')
                                        <span class="badge bg-primary">Toàn bộ người dùng</span>
                                    @elseif($notification->target_type == 'students')
                                        <span class="badge bg-info">Sinh viên</span>
                                    @elseif($notification->target_type == 'chairmen')
                                        <span class="badge bg-warning text-dark">Chủ nhiệm CLB</span>
                                    @elseif($notification->target_type == 'clubs')
                                        <span class="badge bg-success">{{ count($notification->target_ids ?? []) }} CLB</span>
                                    @endif
                                    @if($notification->notification_source == 'support')
                                        <span class="badge bg-danger ms-1">Gửi đến Admin</span>
                                    @endif
                                </td>
                                <td>
                                    @if($notification->notification_source == 'support')
                                        <span class="badge bg-warning text-dark">Hỗ trợ</span>
                                    @elseif($notification->type == 'system')
                                        <span class="badge bg-secondary">Hệ thống</span>
                                    @elseif($notification->type == 'regulation')
                                        <span class="badge bg-danger">Nội quy</span>
                                    @elseif($notification->type == 'administrative')
                                        <span class="badge bg-primary">Hành chính</span>
                                    @endif
                                </td>
                                <td>{{ $notification->sent_at ? $notification->sent_at->format('d/m/Y H:i') : '—' }}</td>
                                <td>
                                    <span class="badge bg-success">{{ $notification->read_count ?? 0 }} đã đọc</span>
                                    <span class="badge bg-warning text-dark">{{ $notification->unread_count ?? 0 }} chưa đọc</span>
                                    <small class="text-muted d-block mt-1">Tổng: {{ $notification->total_recipients ?? 0 }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.notifications.show', $notification->id) }}" 
                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mb-0 mt-2">Không có thông báo nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $notifications->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>

<style>
    .badge {
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 500;
    }

    /* =========================================================
       CUSTOM PAGINATION STYLE
       → Style cho phân trang tùy chỉnh (giống y hệt trang Danh sách tài khoản Admin)
    ========================================================= */
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

    .pagination .page-item.active .page-link:hover {
        color: white;
        background-color: #0B3D91;
        border-color: #0B3D91;
    }

    .pagination .page-item.disabled .page-link,
    .pagination .page-item.disabled .page-link:hover,
    .pagination .page-item.disabled .page-link:focus {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        opacity: 0.6;
        pointer-events: none;
    }

    nav[aria-label="Page navigation"] {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    nav[aria-label="Page navigation"] .pagination {
        margin: 0;
    }
</style>

@endsection

