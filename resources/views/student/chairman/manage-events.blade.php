<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý hoạt động CLB - Chủ nhiệm</title>
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
        .sidebar {
            width: 240px;
            background: var(--primary-blue);
            color: var(--text-light);
            padding: 24px 16px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .logo {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 24px;
        }
        .nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .nav a {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.9);
            padding: 10px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, color 0.2s;
        }
        .nav a:hover {
            background: var(--primary-blue-hover);
            color: var(--text-light);
        }
        .nav a.active {
            background: var(--accent-yellow);
            color: var(--text-dark);
        }
        .logout-btn {
            margin-top: auto;
            background: #ef4444;
            color: #fff;
            border: none;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
        }
        .content {
            margin-left: 260px;
            padding: 24px;
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
        .btn-add-role {
            background-color: #0B3D91;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
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

    <div class="content fade-in">
        <div class="page-header">
            <h3 class="fw-bold mb-0">
                <i class="bi bi-calendar-event"></i>
                Quản lý hoạt động CLB
            </h3>
        </div>

        {{-- THÔNG BÁO --}}
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

        {{-- THÔNG TIN CLB --}}
        <div class="alert alert-info mb-4">
            <strong>CLB:</strong> {{ $club->name }} ({{ $club->code }})
        </div>

        {{-- NÚT THÊM SỰ KIỆN --}}
        <div class="text-end mb-3">
            <button type="button" class="btn-add-role" data-bs-toggle="modal" data-bs-target="#modalAddEvent">
                <i class="bi bi-plus-circle"></i> Thêm hoạt động
            </button>
        </div>

        {{-- LỌC --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Tất cả --</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                            <option value="finished" {{ request('status') == 'finished' ? 'selected' : '' }}>Đã kết thúc</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        {{-- BẢNG DANH SÁCH SỰ KIỆN --}}
        <div class="table-responsive">
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
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>{{ ($events->currentPage() - 1) * $events->perPage() + $loop->iteration }}</td>
                            <td><strong>{{ $event->title }}</strong></td>
                            <td>{{ Str::limit($event->description, 50) ?? '-' }}</td>
                            <td>
                                @if ($event->start_at)
                                    {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                                    @if ($event->end_at)
                                        <br><small>→ {{ \Carbon\Carbon::parse($event->end_at)->format('d/m/Y H:i') }}</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $event->location ?? '-' }}</td>
                            <td>
                                @if ($event->status == 'upcoming')
                                    <span class="badge bg-primary">Sắp diễn ra</span>
                                @elseif ($event->status == 'ongoing')
                                    <span class="badge bg-info">Đang diễn ra</span>
                                @elseif ($event->status == 'finished')
                                    <span class="badge bg-success">Đã kết thúc</span>
                                @elseif ($event->status == 'cancelled')
                                    <span class="badge bg-danger">Đã hủy</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $participantCount = DB::table('event_registrations')
                                        ->where('event_id', $event->id)
                                        ->where('status', 'approved')
                                        ->count();
                                @endphp
                                <a href="{{ route('student.chairman.event-participants', $event->id) }}" class="btn btn-sm btn-info">
                                    {{ $participantCount }} người
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-sm" style="background-color: #0B3D91; color: white;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditEvent"
                                        onclick="loadEventToEdit('{{ $event->id }}', '{{ addslashes($event->title) }}', '{{ addslashes($event->description) }}', '{{ $event->start_at }}', '{{ $event->end_at }}', '{{ addslashes($event->location) }}', '{{ $event->status }}')">
                                    Sửa
                                </button>
                                <form action="{{ route('student.chairman.delete-event', $event->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn chắc chắn muốn xóa hoạt động này?')">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="bi bi-calendar-x"></i>
                                <h4>Chưa có hoạt động nào</h4>
                                <p>Hãy tạo hoạt động mới để bắt đầu</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PHÂN TRANG --}}
        <div class="mt-3 d-flex justify-content-center">
            {{ $events->links('vendor.pagination.custom') }}
        </div>
    </div>

    {{-- MODAL THÊM SỰ KIỆN --}}
    <div class="modal fade" id="modalAddEvent" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm hoạt động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('student.chairman.store-event') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên hoạt động</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thời gian bắt đầu</label>
                                <input type="datetime-local" name="start_at" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thời gian kết thúc</label>
                                <input type="datetime-local" name="end_at" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa điểm</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn" style="background-color: #0B3D91; color: white;">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL SỬA SỰ KIỆN --}}
    <div class="modal fade" id="modalEditEvent" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa hoạt động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editEventForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên hoạt động</label>
                            <input type="text" id="edit_title" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả</label>
                            <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thời gian bắt đầu</label>
                                <input type="datetime-local" id="edit_start_at" name="start_at" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thời gian kết thúc</label>
                                <input type="datetime-local" id="edit_end_at" name="end_at" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa điểm</label>
                            <input type="text" id="edit_location" name="location" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select id="edit_status" name="status" class="form-control" required>
                                <option value="upcoming">Sắp diễn ra</option>
                                <option value="ongoing">Đang diễn ra</option>
                                <option value="finished">Đã kết thúc</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn" style="background-color: #0B3D91; color: white;">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadEventToEdit(eventId, title, description, startAt, endAt, location, status) {
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description || '';
            
            // Convert datetime to local format
            if (startAt) {
                const start = new Date(startAt);
                document.getElementById('edit_start_at').value = start.toISOString().slice(0, 16);
            }
            if (endAt) {
                const end = new Date(endAt);
                document.getElementById('edit_end_at').value = end.toISOString().slice(0, 16);
            }
            
            document.getElementById('edit_location').value = location || '';
            document.getElementById('edit_status').value = status;
            document.getElementById('editEventForm').action = '/student/chairman/update-event/' + eventId;
        }
    </script>
</body>
</html>

