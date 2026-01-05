@extends('layouts.chairman')

@section('title', 'Danh sách nội quy - Chủ nhiệm CLB')

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
            <i class="bi bi-file-text"></i>
            Danh sách nội quy
        </h1>
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

    {{-- FILTER FORM --}}
    <div class="filter-card">
        <form method="GET">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Từ khóa</label>
                    <input type="text" name="search" class="form-control" 
                           value="{{ request('search') }}" 
                           placeholder="Mã, tiêu đề, nội dung...">
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
                        <th>Mã</th>
                        <th>Tiêu đề</th>
                        <th>Phạm vi</th>
                        <th>Mức độ</th>
                        <th>Ngày ban hành</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regulations as $regulation)
                        <tr>
                            <td><strong>{{ $regulation->code }}</strong></td>
                            <td>{{ $regulation->title }}</td>
                            <td>
                                @if($regulation->scope == 'system')
                                    <span class="badge bg-info">Toàn hệ thống</span>
                                @else
                                    <span class="badge bg-secondary">{{ $regulation->club->name ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($regulation->severity == 'light')
                                    <span class="badge bg-success">Nhẹ</span>
                                @elseif($regulation->severity == 'medium')
                                    <span class="badge bg-warning text-dark">Trung bình</span>
                                @else
                                    <span class="badge bg-danger">Nghiêm trọng</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($regulation->issued_date)->format('d/m/Y') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#regulationModal{{ $regulation->id }}">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </button>
                            </td>
                        </tr>

                        {{-- Modal Chi tiết --}}
                        <div class="modal fade" id="regulationModal{{ $regulation->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ $regulation->title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>Mã nội quy:</strong> {{ $regulation->code }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>Phạm vi:</strong>
                                            @if($regulation->scope == 'system')
                                                <span class="badge bg-info">Toàn hệ thống</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $regulation->club->name ?? 'N/A' }}</span>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <strong>Mức độ:</strong>
                                            @if($regulation->severity == 'light')
                                                <span class="badge bg-success">Nhẹ</span>
                                            @elseif($regulation->severity == 'medium')
                                                <span class="badge bg-warning text-dark">Trung bình</span>
                                            @else
                                                <span class="badge bg-danger">Nghiêm trọng</span>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <strong>Nội dung:</strong>
                                            <div class="border p-3 mt-2" style="white-space: pre-wrap;">{{ $regulation->content }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Ngày ban hành:</strong> {{ \Carbon\Carbon::parse($regulation->issued_date)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Không có nội quy nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $regulations->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
