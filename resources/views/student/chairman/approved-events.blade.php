@extends('layouts.chairman')

@section('title', 'Danh sách hoạt động đã duyệt - Chủ nhiệm')

@push('styles')
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
@endpush

@section('content')
<div class="fade-in">
        <div class="page-header">
            <h3 class="fw-bold mb-0">
                <i class="bi bi-list-check"></i>
                Danh sách hoạt động (Đã duyệt)
            </h3>
            <div class="badge bg-primary" style="font-size: 14px; padding: 10px 16px;">
                <i class="bi bi-building"></i> CLB: {{ $club->name }} ({{ $club->code }})
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('student.chairman.create-event') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tạo hoạt động
            </a>
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
                @forelse($events as $index => $event)
                    <tr>
                        <td>{{ ($events->currentPage() - 1) * $events->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $event->title }}</strong></td>
                        <td>{{ Str::limit($event->description ?? 'Chưa có mô tả', 50) }}</td>
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
                            @php
                                $participantCount = DB::table('event_registrations')
                                    ->where('event_id', $event->id)
                                    ->whereIn('status', ['approved', 'attended'])
                                    ->count();
                            @endphp
                            <a href="{{ route('student.chairman.approved-participants', ['event_id' => $event->id]) }}" class="btn btn-sm btn-info">
                                {{ $participantCount }} người
                            </a>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" data-bs-target="#modalEditEvent"
                                    onclick="loadEventToEdit('{{ $event->id }}', '{{ addslashes($event->title) }}', '{{ addslashes($event->description ?? '') }}', '{{ $event->start_at }}', '{{ $event->end_at }}', '{{ addslashes($event->location ?? '') }}', '{{ $event->status }}')">
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
                            <p>Hiện tại không có hoạt động nào đã được duyệt</p>
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

    {{-- MODAL SỬA HOẠT ĐỘNG --}}
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
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadEventToEdit(eventId, title, description, startAt, endAt, location, status) {
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description || '';
            
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
@endpush

