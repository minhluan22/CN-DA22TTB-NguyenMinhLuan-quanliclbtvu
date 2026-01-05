@extends('layouts.chairman')

@section('title', 'Duyệt hoạt động - Chủ nhiệm')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #FFF3A0;
    }
    
    .dashboard-container {
        padding: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }
    
    /* Page Header */
    .page-header {
        background: white;
        padding: 24px 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #0033A0;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    /* Club Info Card */
    .club-info-card {
        background: linear-gradient(135deg, #0033A0 0%, #0B3D91 100%);
        padding: 24px 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(0,51,160,0.2);
        color: white;
    }
    
    .club-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
    }
    
    .club-info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .club-info-label {
        font-size: 13px;
        opacity: 0.85;
        font-weight: 500;
    }
    
    .club-info-value {
        font-size: 18px;
        font-weight: 700;
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
    
    .badge-position {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
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
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
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
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-check-circle"></i>
            Duyệt hoạt động
        </h1>
    </div>

    <!-- Club Info Card -->
    <div class="club-info-card">
        <div class="club-info-grid">
            <div class="club-info-item">
                <span class="club-info-label">Tên Câu lạc bộ</span>
                <span class="club-info-value">{{ $club->name }}</span>
            </div>
            <div class="club-info-item">
                <span class="club-info-label">Mã CLB</span>
                <span class="club-info-value">{{ $club->code }}</span>
            </div>
            <div class="club-info-item">
                <span class="club-info-label">Trạng thái</span>
                <span class="club-info-value">
                    @if($club->status === 'active')
                        ✅ Hoạt động
                    @else
                        🔒 Ngừng hoạt động
                    @endif
                </span>
            </div>
            <div class="club-info-item">
                <span class="club-info-label">Vai trò của bạn</span>
                <span class="club-info-value">Chủ nhiệm CLB</span>
            </div>
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
                <label class="form-label fw-semibold">
                    <i class="bi bi-search"></i> Tìm kiếm
                </label>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Tên hoạt động, người đề xuất, MSSV...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-funnel"></i> Trạng thái
                </label>
                <select name="status" class="form-control">
                    <option value="">-- Tất cả --</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
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
                    <th>Thời gian gửi</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proposals as $index => $proposal)
                    <tr>
                        <td>{{ $proposals->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $proposal->title }}</strong>
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
                        <td>
                            {{ $proposal->proposer_name }}
                            @if($proposal->proposer_student_code)
                                <br><small class="text-muted">({{ $proposal->proposer_student_code }})</small>
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
                            <small>{{ \Carbon\Carbon::parse($proposal->created_at)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#approveModal{{ $proposal->id }}"
                                    {{ $proposal->created_by == Auth::id() ? 'disabled title="Bạn không thể duyệt đề xuất của chính mình"' : '' }}>
                                <i class="bi bi-check-circle"></i> Duyệt
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="openRejectModal({{ $proposal->id }}, {{ json_encode($proposal->title) }}, {{ json_encode($proposal->proposer_name) }})"
                                    {{ $proposal->created_by == Auth::id() ? 'disabled title="Bạn không thể từ chối đề xuất của chính mình"' : '' }}>
                                <i class="bi bi-x-circle"></i> Từ chối
                            </button>
                        </td>
                    </tr>

                    {{-- MODAL DUYỆT --}}
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
                                                       value="{{ \Carbon\Carbon::parse($proposal->start_at)->format('Y-m-d\TH:i') }}" required>
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

                    {{-- MODAL TỪ CHỐI --}}
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
                                                <strong id="rejectProposalTitle"></strong><br>
                                                <small id="rejectProposerName"></small>
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
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 48px; color: #ccc; display: block; margin-bottom: 16px;"></i>
                            <p class="text-muted mb-0">Chưa có đề xuất nào</p>
                            <small class="text-muted">Hiện tại không có đề xuất hoạt động nào chờ duyệt</small>
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
@endsection

@push('scripts')
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
@endpush
