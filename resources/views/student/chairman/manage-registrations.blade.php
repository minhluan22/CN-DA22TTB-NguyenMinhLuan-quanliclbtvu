@extends('layouts.chairman')

@section('title', 'Quản lý đơn đăng ký CLB - Chủ nhiệm')

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
            --secondary: #2b2f3a;
            --card: #ffffff;
            --muted: #6b7280;
            --border: #e5e7eb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .table-role {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        .submenu {
            margin-left: 20px;
            margin-top: 4px;
        }
        .submenu a {
            font-size: 13px;
            padding: 8px 12px;
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
        <h3 class="fw-bold mb-4">Quản lý đơn đăng ký vào CLB</h3>

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
            <strong>CLB:</strong> {{ $club->name }} ({{ $club->code }}) | 
            <strong>Trạng thái:</strong> 
            @if ($club->status == 'active')
                <span class="badge bg-success">Hoạt động</span>
            @else
                <span class="badge bg-warning">Chờ duyệt</span>
            @endif
        </div>

        {{-- TÌM KIẾM & LỌC --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Tất cả --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ phê duyệt</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã phê duyệt</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        {{-- BẢNG DANH SÁCH ĐƠN ĐĂNG KÝ --}}
        <div class="table-responsive">
            <table class="table table-role">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sinh viên</th>
                        <th>MSSV</th>
                        <th>Email</th>
                        <th>Lý do tham gia</th>
                        <th>Ngày đăng ký</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registrations as $reg)
                        <tr>
                            <td>{{ ($registrations->currentPage() - 1) * $registrations->perPage() + $loop->iteration }}</td>
                            <td>
                                <strong>{{ $reg->name }}</strong>
                                @if($reg->has_left)
                                    <br><small class="text-warning" style="color: #f59e0b;">⚠️ Đã từng tham gia và rời CLB</small>
                                @endif
                                @if($reg->join_count > 0)
                                    <br><small class="text-info" style="color: #0d6efd;">📊 Đã tham gia {{ $reg->join_count }} lần</small>
                                @endif
                                @if($reg->is_current_member)
                                    <br><small class="text-success" style="color: #166534;">✓ Đang là thành viên</small>
                                @endif
                            </td>
                            <td>{{ $reg->student_code ?? '-' }}</td>
                            <td>{{ $reg->email }}</td>
                            <td>
                                @if($reg->reason)
                                    @if(strlen($reg->reason) > 10)
                                        <span id="reason-preview-{{ $reg->id }}">{{ Str::limit($reg->reason, 10) }}...</span>
                                        <a href="#" onclick="showReasonDetail({{ $reg->id }}, '{{ addslashes($reg->reason) }}'); return false;" style="color: var(--primary); text-decoration: none; margin-left: 4px; font-size: 11px;">Xem chi tiết</a>
                                    @else
                                        {{ $reg->reason }}
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($reg->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($reg->status == 'pending')
                                    <span class="badge bg-warning">⏳ Chờ phê duyệt</span>
                                @elseif ($reg->status == 'approved')
                                    <span class="badge bg-success">✅ Đã phê duyệt</span>
                                @elseif ($reg->status == 'rejected')
                                    <span class="badge bg-danger">❌ Bị từ chối</span>
                                @endif
                            </td>
                            <td>
                                @if ($reg->status == 'pending')
                                    <form action="{{ route('student.chairman.approve-registration', $reg->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Phê duyệt đơn đăng ký này?')">
                                            Phê duyệt
                                        </button>
                                    </form>
                                    <form action="{{ route('student.chairman.reject-registration', $reg->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Từ chối đơn đăng ký này?')">
                                            Từ chối
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Không có đơn đăng ký nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PHÂN TRANG --}}
        @if($registrations->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                {{ $registrations->links('vendor.pagination.custom') }}
            </div>
        @endif

    <!-- Modal xem chi tiết lý do -->
    <div id="reasonModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 24px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: var(--primary);">Lý do tham gia CLB</h3>
                <button onclick="closeReasonModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--muted);">&times;</button>
            </div>
            <div id="reasonDetail" style="padding: 16px; background: #f9fafb; border-radius: 8px; line-height: 1.6; white-space: pre-wrap; word-wrap: break-word;"></div>
            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <button onclick="closeReasonModal()" class="btn btn-secondary">Đóng</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
        function showReasonDetail(regId, reason) {
            document.getElementById('reasonDetail').textContent = reason;
            document.getElementById('reasonModal').style.display = 'flex';
        }

        function closeReasonModal() {
            document.getElementById('reasonModal').style.display = 'none';
        }

        // Đóng modal khi click bên ngoài
        document.getElementById('reasonModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReasonModal();
            }
        });
    </script>
@endpush

