@extends('layouts.chairman')

@section('title', 'Danh sách vi phạm - Chủ nhiệm CLB')

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
        display: flex;
        justify-content: space-between;
        align-items: center;
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
    
    .filter-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .table-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
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

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        opacity: 0.6;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-exclamation-triangle"></i>
            Danh sách vi phạm
        </h1>
        <a href="{{ route('student.chairman.violations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ghi nhận vi phạm
        </a>
    </div>

    <!-- Club Info Card -->
    @if($chairmanClub)
        @php
            $clubModel = \App\Models\Club::find($chairmanClub->id);
        @endphp
        <div class="club-info-card">
            <div class="club-info-grid">
                <div class="club-info-item">
                    <span class="club-info-label">Tên Câu lạc bộ</span>
                    <span class="club-info-value">{{ $chairmanClub->name }}</span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Mã CLB</span>
                    <span class="club-info-value">{{ $chairmanClub->code }}</span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Trạng thái</span>
                    <span class="club-info-value">
                        @if($clubModel && $clubModel->status === 'active')
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
    @endif

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

    {{-- FILTER FORM --}}
    <div class="filter-card">
        <form method="GET">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Từ khóa</label>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" 
                           placeholder="Tên sinh viên, MSSV...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Mức độ</label>
                    <select name="severity" class="form-control">
                        <option value="">-- Tất cả --</option>
                        <option value="light" {{ request('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                        <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="serious" {{ request('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="">-- Tất cả --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chưa xử lý</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Đã xử lý</option>
                        <option value="monitoring" {{ request('status') == 'monitoring' ? 'selected' : '' }}>Đang theo dõi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Sinh viên</th>
                        <th>MSSV</th>
                        <th>Nội quy vi phạm</th>
                        <th>Mô tả</th>
                        <th>Mức độ</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($violations as $violation)
                        <tr>
                            <td>{{ ($violations->currentPage() - 1) * $violations->perPage() + $loop->iteration }}</td>
                            <td><strong>{{ $violation->user->name ?? 'N/A' }}</strong></td>
                            <td>{{ $violation->user->student_code ?? 'N/A' }}</td>
                            <td>
                                {{ $violation->regulation->title ?? 'N/A' }}
                                <br><small class="text-muted">({{ $violation->regulation->code ?? 'N/A' }})</small>
                            </td>
                            <td>
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $violation->description }}
                                </div>
                            </td>
                            <td>
                                @if($violation->severity == 'light')
                                    <span class="badge bg-success">Nhẹ</span>
                                @elseif($violation->severity == 'medium')
                                    <span class="badge bg-warning text-dark">Trung bình</span>
                                @else
                                    <span class="badge bg-danger">Nghiêm trọng</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($violation->status == 'pending')
                                    <span class="badge bg-warning text-dark">Chưa xử lý</span>
                                @elseif($violation->status == 'processed')
                                    <span class="badge bg-success">Đã xử lý</span>
                                @else
                                    <span class="badge bg-info">Đang theo dõi</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('student.chairman.violations.show', $violation->id) }}" 
                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Không có vi phạm nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($violations->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $violations->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
