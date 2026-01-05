@extends('layouts.chairman')

@section('title', 'Danh sách đăng ký - Chủ nhiệm')

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
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
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
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .badge-status {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
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
                Danh sách đăng ký
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

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('student.chairman.pending-registrations') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-search"></i> Tìm kiếm sinh viên
                    </label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Tên, MSSV hoặc Email..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-funnel"></i> Trạng thái
                    </label>
                    <select name="status" class="form-select">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>-- Tất cả --</option>
                        <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ $statusFilter == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ $statusFilter == 'rejected' ? 'selected' : '' }}>Đã hủy</option>
                        <option value="attended" {{ $statusFilter == 'attended' ? 'selected' : '' }}>Đã tham gia</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-role">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Họ tên</th>
                        <th>MSSV</th>
                        <th>Email</th>
                        <th>Hoạt động đăng ký</th>
                        <th>Thời gian đăng ký</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $index => $reg)
                        <tr>
                            <td>{{ ($registrations->currentPage() - 1) * $registrations->perPage() + $index + 1 }}</td>
                            <td><strong>{{ $reg->name }}</strong></td>
                            <td>{{ $reg->student_code }}</td>
                            <td>{{ $reg->email }}</td>
                            <td>
                                <strong>{{ $reg->event_title }}</strong><br>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($reg->event_start)->format('d/m/Y H:i') }}
                                </small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($reg->registration_date)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($reg->status == 'pending')
                                    <span class="badge badge-status bg-warning text-dark">
                                        <i class="bi bi-clock-history"></i> Chờ duyệt
                                    </span>
                                @elseif($reg->status == 'approved')
                                    <span class="badge badge-status bg-success">
                                        <i class="bi bi-check-circle"></i> Đã duyệt
                                    </span>
                                @elseif($reg->status == 'rejected')
                                    <span class="badge badge-status bg-danger">
                                        <i class="bi bi-x-circle"></i> Đã hủy
                                    </span>
                                @elseif($reg->status == 'attended')
                                    <span class="badge badge-status bg-info">
                                        <i class="bi bi-person-check"></i> Đã tham gia
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @if($reg->status == 'pending')
                                        <form action="{{ route('student.chairman.approve-event-registration', $reg->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Xác nhận duyệt đăng ký tham gia hoạt động?')">
                                                <i class="bi bi-check-circle"></i> Duyệt
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="openRejectModal({{ $reg->id }}, {{ json_encode($reg->name) }}, {{ json_encode($reg->event_title) }})">
                                            <i class="bi bi-x-circle"></i> Từ chối
                                        </button>
                                    @elseif($reg->status == 'approved')
                                        <button type="button" class="btn btn-warning btn-sm" onclick="openRejectModal({{ $reg->id }}, {{ json_encode($reg->name) }}, {{ json_encode($reg->event_title) }})">
                                            <i class="bi bi-x-octagon"></i> Hủy
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h4>Chưa có đăng ký nào</h4>
                                <p>Hiện tại không có đăng ký nào phù hợp với bộ lọc</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $registrations->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    <!-- Modal Từ chối đăng ký -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i class="bi bi-x-circle"></i> Lý do từ chối đăng ký
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Thông tin đăng ký:</label>
                            <div class="alert alert-info mb-3">
                                <strong id="rejectStudentName"></strong><br>
                                <small id="rejectEventTitle"></small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="rejectReason" class="form-label fw-semibold">
                                Lý do từ chối <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="rejectReason" name="reason" rows="4" 
                                      placeholder="Nhập lý do từ chối đăng ký..." required></textarea>
                            <div class="invalid-feedback">
                                Vui lòng nhập lý do từ chối.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do nhanh:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                        data-reason="Đủ số lượng tham gia">
                                    Đủ số lượng tham gia
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                        data-reason="Không phù hợp thời gian">
                                    Không phù hợp thời gian
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                        data-reason="Đăng ký trễ hạn">
                                    Đăng ký trễ hạn
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                        data-reason="Vi phạm quy định CLB">
                                    Vi phạm quy định CLB
                                </button>
                            </div>
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentRegistrationId = null;

        function openRejectModal(registrationId, studentName, eventTitle) {
            currentRegistrationId = registrationId;
            
            // Cập nhật thông tin trong modal
            document.getElementById('rejectStudentName').textContent = studentName;
            document.getElementById('rejectEventTitle').textContent = eventTitle;
            
            // Reset form
            document.getElementById('rejectForm').reset();
            document.getElementById('rejectReason').classList.remove('is-invalid');
            
            // Cập nhật action của form
            document.getElementById('rejectForm').action = '{{ route("student.chairman.reject-event-registration", ":id") }}'.replace(':id', registrationId);
            
            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        // Xử lý lý do nhanh
        document.querySelectorAll('.quick-reason').forEach(button => {
            button.addEventListener('click', function() {
                const reason = this.getAttribute('data-reason');
                document.getElementById('rejectReason').value = reason;
                document.getElementById('rejectReason').classList.remove('is-invalid');
            });
        });

        // Validation form
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            const reason = document.getElementById('rejectReason').value.trim();
            
            if (!reason) {
                e.preventDefault();
                document.getElementById('rejectReason').classList.add('is-invalid');
                document.getElementById('rejectReason').focus();
                return false;
            }
            
            return true;
        });

        // Xóa invalid khi người dùng nhập
        document.getElementById('rejectReason').addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    </script>
@endpush
