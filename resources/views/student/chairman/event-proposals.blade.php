<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Danh sách đề xuất hoạt động - Chủ nhiệm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #072C6A;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF7B0;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .content {
            margin-left: 260px;
            padding: 16px;
            font-size: 14px;
        }
        .filter-section {
            background: white;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .table-role {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .table-role table {
            font-size: 13px;
        }
        .table-role th {
            font-size: 12px;
            padding: 10px 8px;
            font-weight: 600;
        }
        .table-role td {
            padding: 10px 8px;
            vertical-align: middle;
        }
        .badge-position {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-chairman {
            background: #0d6efd;
            color: white;
        }
        .badge-vice {
            background: #0dcaf0;
            color: white;
        }
        .badge-member {
            background: #6c757d;
            color: white;
        }
        .activity-type {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
        }
        .type-academic {
            background: #e3f2fd;
            color: #1976d2;
        }
        .type-arts {
            background: #fce4ec;
            color: #c2185b;
        }
        .type-volunteer {
            background: #e8f5e9;
            color: #388e3c;
        }
        .type-other {
            background: #fff3e0;
            color: #f57c00;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .btn-xs {
            padding: 4px 8px;
            font-size: 11px;
            line-height: 1.2;
            border-radius: 4px;
        }
        .form-control-sm {
            font-size: 13px;
            padding: 6px 10px;
        }
        .form-label {
            font-size: 12px;
            margin-bottom: 4px;
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

        /* Đảm bảo phân trang hiển thị đúng trong container */
        nav[aria-label="Page navigation"] {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        nav[aria-label="Page navigation"] .pagination {
            margin: 0;
        }
    </style>
</head>
<body>
    @include('student.sidebar')

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0" style="font-size: 18px;">
                <i class="bi bi-lightbulb"></i> Danh sách đề xuất hoạt động
            </h4>
            <div class="badge bg-primary" style="font-size: 12px; padding: 6px 12px;">
                <i class="bi bi-building"></i> CLB: {{ $club->name }} ({{ $club->code }})
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FILTER SECTION --}}
        <div class="filter-section">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size: 12px; margin-bottom: 4px;">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           value="{{ request('search') }}" 
                           placeholder="Tên hoạt động, người đề xuất, MSSV..."
                           style="font-size: 13px;">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size: 12px; margin-bottom: 4px;">
                        <i class="bi bi-funnel"></i> Trạng thái duyệt
                    </label>
                    <select name="approval_status" class="form-control form-control-sm" style="font-size: 13px;">
                        <option value="">-- Tất cả --</option>
                        <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size: 12px; margin-bottom: 4px;">
                        <i class="bi bi-tag"></i> Loại hoạt động
                    </label>
                    <select name="activity_type" class="form-control form-control-sm" style="font-size: 13px;">
                        <option value="">-- Tất cả --</option>
                        <option value="academic" {{ request('activity_type') == 'academic' ? 'selected' : '' }}>Học thuật</option>
                        <option value="arts" {{ request('activity_type') == 'arts' ? 'selected' : '' }}>Văn nghệ</option>
                        <option value="volunteer" {{ request('activity_type') == 'volunteer' ? 'selected' : '' }}>Tình nguyện</option>
                        <option value="other" {{ request('activity_type') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100" style="font-size: 12px; padding: 6px 12px;">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="table-role">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Tên hoạt động</th>
                        <th>Người đề xuất</th>
                        <th>Chức vụ</th>
                        <th>Loại hoạt động</th>
                        <th>Trạng thái</th>
                        <th>Thời gian gửi</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $proposal)
                        <tr>
                            <td>{{ ($proposals->currentPage() - 1) * $proposals->perPage() + $loop->iteration }}</td>
                            <td>
                                <strong style="font-size: 13px;">{{ $proposal->title }}</strong>
                                @if($proposal->activity_type)
                                    <br>
                                    <span class="activity-type type-{{ $proposal->activity_type }}">
                                        @if($proposal->activity_type == 'academic')
                                            Học thuật
                                        @elseif($proposal->activity_type == 'arts')
                                            Văn nghệ
                                        @elseif($proposal->activity_type == 'volunteer')
                                            Tình nguyện
                                        @else
                                            Khác
                                        @endif
                                    </span>
                                @endif
                            </td>
                            <td style="font-size: 12px;">
                                {{ $proposal->proposer_name }}
                                @if($proposal->proposer_student_code)
                                    <br><small class="text-muted" style="font-size: 11px;">({{ $proposal->proposer_student_code }})</small>
                                @endif
                            </td>
                            <td>
                                @if($proposal->proposer_position == 'chairman')
                                    <span class="badge-position badge-chairman">Chủ nhiệm</span>
                                @elseif($proposal->proposer_position == 'vice_chairman')
                                    <span class="badge-position badge-vice">Phó Chủ nhiệm</span>
                                @else
                                    <span class="badge-position badge-member">Thành viên</span>
                                @endif
                            </td>
                            <td>
                                @if($proposal->activity_type == 'academic')
                                    <span class="activity-type type-academic">Học thuật</span>
                                @elseif($proposal->activity_type == 'arts')
                                    <span class="activity-type type-arts">Văn nghệ</span>
                                @elseif($proposal->activity_type == 'volunteer')
                                    <span class="activity-type type-volunteer">Tình nguyện</span>
                                @else
                                    <span class="activity-type type-other">Khác</span>
                                @endif
                            </td>
                            <td>
                                @if($proposal->approval_status == 'pending')
                                    <span class="status-badge status-pending">
                                        <i class="bi bi-clock"></i> Chờ duyệt
                                    </span>
                                @elseif($proposal->approval_status == 'approved')
                                    <span class="status-badge status-approved">
                                        <i class="bi bi-check-circle"></i> Đã duyệt
                                    </span>
                                @elseif($proposal->approval_status == 'rejected')
                                    <span class="status-badge status-rejected">
                                        <i class="bi bi-x-circle"></i> Đã từ chối
                                    </span>
                                @endif
                            </td>
                            <td style="font-size: 12px;">
                                <small>{{ \Carbon\Carbon::parse($proposal->created_at)->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <button type="button" class="btn btn-xs btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $proposal->id }}"
                                            style="font-size: 11px; padding: 4px 8px;">
                                        <i class="bi bi-eye"></i> Chi tiết
                                    </button>
                                    @if($proposal->approval_status == 'pending')
                                        <button type="button" class="btn btn-xs btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $proposal->id }}"
                                                {{ $proposal->created_by == Auth::id() ? 'disabled title="Bạn không thể duyệt đề xuất của chính mình"' : '' }}
                                                style="font-size: 11px; padding: 4px 8px;">
                                            <i class="bi bi-check-circle"></i> Duyệt
                                        </button>
                                        <button type="button" class="btn btn-xs btn-danger" 
                                                onclick="openRejectModal({{ $proposal->id }}, {{ json_encode($proposal->title) }}, {{ json_encode($proposal->proposer_name) }})"
                                                {{ $proposal->created_by == Auth::id() ? 'disabled title="Bạn không thể từ chối đề xuất của chính mình"' : '' }}
                                                style="font-size: 11px; padding: 4px 8px;">
                                            <i class="bi bi-x-circle"></i> Từ chối
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL CHI TIẾT --}}
                        <div class="modal fade" id="detailModal{{ $proposal->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Chi tiết đề xuất hoạt động</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Tên hoạt động:</strong>
                                                <p>{{ $proposal->title }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Loại hoạt động:</strong>
                                                <p>
                                                    @if($proposal->activity_type == 'academic')
                                                        <span class="activity-type type-academic">Học thuật</span>
                                                    @elseif($proposal->activity_type == 'arts')
                                                        <span class="activity-type type-arts">Văn nghệ</span>
                                                    @elseif($proposal->activity_type == 'volunteer')
                                                        <span class="activity-type type-volunteer">Tình nguyện</span>
                                                    @else
                                                        <span class="activity-type type-other">Khác</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Người đề xuất:</strong>
                                            <p>
                                                {{ $proposal->proposer_name }}
                                                @if($proposal->proposer_student_code)
                                                    ({{ $proposal->proposer_student_code }})
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $proposal->proposer_email }}</small>
                                                <br>
                                                @if($proposal->proposer_position == 'chairman')
                                                    <span class="badge-position badge-chairman">Chủ nhiệm</span>
                                                @elseif($proposal->proposer_position == 'vice_chairman')
                                                    <span class="badge-position badge-vice">Phó Chủ nhiệm</span>
                                                @else
                                                    <span class="badge-position badge-member">Thành viên</span>
                                                @endif
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Mục tiêu:</strong>
                                            <p>{{ $proposal->goal ?? 'N/A' }}</p>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Nội dung chi tiết:</strong>
                                            <p>{{ $proposal->description ?? 'N/A' }}</p>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Thời gian bắt đầu:</strong>
                                                <p>{{ $proposal->start_at ? \Carbon\Carbon::parse($proposal->start_at)->format('d/m/Y H:i') : 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Thời gian kết thúc:</strong>
                                                <p>{{ $proposal->end_at ? \Carbon\Carbon::parse($proposal->end_at)->format('d/m/Y H:i') : 'N/A' }}</p>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <strong>Địa điểm:</strong>
                                            <p>{{ $proposal->location ?? 'N/A' }}</p>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>Số lượng dự kiến:</strong>
                                                <p>{{ $proposal->expected_participants ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Kinh phí dự kiến:</strong>
                                                <p>{{ $proposal->expected_budget ? number_format($proposal->expected_budget, 0, ',', '.') . ' VNĐ' : 'N/A' }}</p>
                                            </div>
                                        </div>

                                        @if($proposal->attachment)
                                            <div class="mb-3">
                                                <strong>File đính kèm:</strong>
                                                <p>
                                                    <a href="{{ asset('storage/' . $proposal->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download"></i> Tải xuống
                                                    </a>
                                                </p>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <strong>Trạng thái:</strong>
                                            <p>
                                                @if($proposal->approval_status == 'pending')
                                                    <span class="status-badge status-pending">
                                                        <i class="bi bi-clock"></i> Chờ duyệt
                                                    </span>
                                                @elseif($proposal->approval_status == 'approved')
                                                    <span class="status-badge status-approved">
                                                        <i class="bi bi-check-circle"></i> Đã duyệt
                                                    </span>
                                                @elseif($proposal->approval_status == 'rejected')
                                                    <span class="status-badge status-rejected">
                                                        <i class="bi bi-x-circle"></i> Đã từ chối
                                                    </span>
                                                @endif
                                            </p>
                                        </div>

                                        @if($proposal->approval_status == 'rejected' && $proposal->violation_notes)
                                            <div class="mb-3">
                                                <strong>Lý do từ chối:</strong>
                                                <div class="alert alert-danger">
                                                    {{ $proposal->violation_notes }}
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <strong>Thời gian gửi:</strong>
                                            <p>{{ \Carbon\Carbon::parse($proposal->created_at)->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL DUYỆT --}}
                        @if($proposal->approval_status == 'pending')
                            <div class="modal fade" id="approveModal{{ $proposal->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="{{ route('student.chairman.approve-proposal', $proposal->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Duyệt đề xuất hoạt động</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <p><strong>Người đề xuất:</strong> {{ $proposal->proposer_name }} 
                                                        @if($proposal->proposer_student_code)
                                                            ({{ $proposal->proposer_student_code }})
                                                        @endif
                                                    </p>
                                                    <p><strong>Mục tiêu:</strong> {{ $proposal->goal ?? 'N/A' }}</p>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Tên hoạt động <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" 
                                                           value="{{ $proposal->title }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Nội dung chi tiết <span class="text-danger">*</span></label>
                                                    <textarea name="description" class="form-control" rows="4" required>{{ $proposal->description }}</textarea>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                                        <input type="datetime-local" name="start_at" class="form-control" 
                                                               value="{{ $proposal->start_at ? \Carbon\Carbon::parse($proposal->start_at)->format('Y-m-d\TH:i') : '' }}" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">Thời gian kết thúc</label>
                                                        <input type="datetime-local" name="end_at" class="form-control" 
                                                               value="{{ $proposal->end_at ? \Carbon\Carbon::parse($proposal->end_at)->format('Y-m-d\TH:i') : '' }}">
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Địa điểm <span class="text-danger">*</span></label>
                                                    <input type="text" name="location" class="form-control" 
                                                           value="{{ $proposal->location }}" required>
                                                </div>

                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle"></i> Bạn có thể chỉnh sửa thông tin trước khi duyệt. Sau khi duyệt, hoạt động sẽ được tạo chính thức.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-success">Duyệt đề xuất</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 48px; color: #ccc; display: block; margin-bottom: 16px;"></i>
                                <p class="text-muted mb-0">Chưa có đề xuất nào</p>
                                <small class="text-muted">Hiện tại không có đề xuất hoạt động nào</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($proposals->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $proposals->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    <!-- MODAL TỪ CHỐI -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-x-circle"></i> Lý do từ chối đề xuất
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Thông tin đề xuất:</label>
                            <div class="alert alert-info mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong id="rejectProposalTitle"></strong>
                                    </div>
                                    <div class="text-end">
                                        <small id="rejectProposerName"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="rejectReason" class="form-label fw-semibold">
                                Lý do từ chối <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="rejectReason" 
                                      name="rejection_reason" rows="4" 
                                      placeholder="Nhập lý do từ chối đề xuất..." required></textarea>
                            <div class="invalid-feedback">
                                Vui lòng nhập lý do từ chối.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do nhanh:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                        data-reason="Không phù hợp với mục tiêu CLB">
                                    Không phù hợp với mục tiêu CLB
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                        data-reason="Thiếu thông tin chi tiết">
                                    Thiếu thông tin chi tiết
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                        data-reason="Trùng lặp với hoạt động khác">
                                    Trùng lặp với hoạt động khác
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                        data-reason="Vi phạm quy định CLB">
                                    Vi phạm quy định CLB
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Lý do từ chối sẽ được gửi đến người đề xuất.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i> Hủy
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-check-circle"></i> Xác nhận từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentProposalId = null;

        function openRejectModal(proposalId, proposalTitle, proposerName) {
            currentProposalId = proposalId;
            
            // Cập nhật thông tin trong modal
            document.getElementById('rejectProposalTitle').textContent = proposalTitle;
            document.getElementById('rejectProposerName').textContent = 'Người đề xuất: ' + proposerName;
            
            // Reset form
            document.getElementById('rejectForm').reset();
            document.getElementById('rejectReason').classList.remove('is-invalid');
            
            // Cập nhật action của form
            document.getElementById('rejectForm').action = '{{ route("student.chairman.reject-proposal", ":id") }}'.replace(':id', proposalId);
            
            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        // Xử lý lý do nhanh
        document.querySelectorAll('.quick-reason').forEach(button => {
            button.addEventListener('click', function() {
                const reason = this.getAttribute('data-reason');
                const textarea = document.getElementById('rejectReason');
                if (textarea) {
                    textarea.value = reason;
                    textarea.classList.remove('is-invalid');
                }
            });
        });

        // Validation form từ chối
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            const textarea = document.getElementById('rejectReason');
            
            if (!textarea || !textarea.value.trim()) {
                e.preventDefault();
                if (textarea) {
                    textarea.classList.add('is-invalid');
                    textarea.focus();
                }
                return false;
            }
        });
    </script>
</body>
</html>

