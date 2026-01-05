<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lịch sử điểm hoạt động - Chủ nhiệm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0B3D91;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF7B0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--soft-yellow);
        }
        .content {
            margin-left: 260px;
            padding: 24px;
        }
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">
                <i class="bi bi-clock-history"></i> Lịch sử điểm hoạt động
            </h3>
            <div class="badge bg-primary" style="font-size: 14px; padding: 10px 16px;">
                <i class="bi bi-building"></i> CLB: {{ $club->name }} ({{ $club->code }})
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('student.chairman.activity-points-history') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Tên, MSSV hoặc hoạt động..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-person"></i> Sinh viên
                    </label>
                    <select name="user_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->student_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-calendar-event"></i> Hoạt động
                    </label>
                    <select name="event_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-calendar"></i> Từ ngày
                    </label>
                    <input type="date" name="start_date" class="form-control" 
                           value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-calendar-check"></i> Đến ngày
                    </label>
                    <input type="date" name="end_date" class="form-control" 
                           value="{{ request('end_date') }}">
                </div>
                <div class="col-md-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                    <a href="{{ route('student.chairman.activity-points-history') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-role">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Họ tên sinh viên</th>
                        <th>MSSV</th>
                        <th>Hoạt động</th>
                        <th>Điểm đạt được</th>
                        <th>Thời gian ghi nhận</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pointsHistory as $item)
                        <tr>
                            <td>{{ ($pointsHistory->currentPage() - 1) * $pointsHistory->perPage() + $loop->iteration }}</td>
                            <td><strong>{{ $item->user_name }}</strong></td>
                            <td>{{ $item->student_code }}</td>
                            <td>
                                <strong>{{ $item->event_title }}</strong><br>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($item->event_date)->format('d/m/Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark" style="font-size: 14px; padding: 6px 12px;">
                                    <i class="bi bi-star-fill"></i> {{ $item->activity_points }} điểm
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->point_date)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h4>Chưa có dữ liệu</h4>
                                <p>Hiện tại không có lịch sử điểm hoạt động phù hợp với bộ lọc</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pointsHistory->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $pointsHistory->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

