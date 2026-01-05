@extends('layouts.chairman')

@section('title', 'Danh sách tham gia (Đã duyệt) - Chủ nhiệm')

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
                <i class="bi bi-people-check"></i>
                Danh sách tham gia (Đã duyệt)
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

        {{-- LỌC THEO HOẠT ĐỘNG --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Lọc theo hoạt động</label>
                        <select name="event_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Tất cả hoạt động --</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }} ({{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-role">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên sinh viên</th>
                    <th>MSSV</th>
                    <th>Email</th>
                    <th>Hoạt động</th>
                    <th>Trạng thái tham gia</th>
                    <th>Điểm hoạt động</th>
                    <th>Ngày đăng ký</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participants as $index => $participant)
                    <tr>
                        <td>{{ ($participants->currentPage() - 1) * $participants->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $participant->name }}</strong></td>
                        <td>{{ $participant->student_code }}</td>
                        <td>{{ $participant->email }}</td>
                        <td>
                            <strong>{{ $participant->event_title }}</strong><br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($participant->event_start)->format('d/m/Y H:i') }}
                            </small>
                        </td>
                        <td>
                            @if($participant->status === 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @elseif($participant->status === 'attended')
                                <span class="badge bg-info">Đã tham gia</span>
                            @endif
                        </td>
                        <td>
                            @if($participant->activity_points > 0)
                                <span class="badge bg-warning text-dark">{{ $participant->activity_points }} điểm</span>
                            @else
                                <span class="text-muted">Chưa có</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($participant->registration_date)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="bi bi-people"></i>
                            <h4>Chưa có người tham gia</h4>
                            <p>Hiện tại không có người tham gia nào đã được duyệt</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($participants->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $participants->links('vendor.pagination.custom') }}
            </div>
        @endif
</div>
@endsection

