@extends('layouts.chairman')

@section('title', 'Hoạt động chờ phê duyệt - Chủ nhiệm')

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
                <i class="bi bi-clock-history"></i>
                Hoạt động chờ phê duyệt
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('student.chairman.create-event') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tạo hoạt động
            </a>
        </div>

        <table class="table table-role">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên hoạt động</th>
                    <th>Mô tả</th>
                    <th>Thời gian</th>
                    <th>Địa điểm</th>
                    <th>Ngày đề xuất</th>
                    <th>Trạng thái</th>
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
                        <td>{{ \Carbon\Carbon::parse($event->created_at)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock-history"></i> Chờ phê duyệt
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-calendar-check"></i>
                            <h4>Không có hoạt động chờ duyệt</h4>
                            <p>Tất cả hoạt động đã được phê duyệt hoặc chưa có hoạt động nào được đề xuất</p>
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
@endsection

