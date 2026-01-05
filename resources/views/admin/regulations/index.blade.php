@extends('layouts.admin')

@section('title', 'Danh sách nội quy')

@section('content')
<style>
    .table tbody tr.table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
</style>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>✅ Thành công!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-file-text"></i> Danh sách nội quy
        </h3>
        <a href="{{ route('admin.regulations.create') }}" class="btn" style="background: #0B3D91; color: white; border: none;">
            <i class="bi bi-plus-circle"></i> Tạo nội quy mới
        </a>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" class="mb-4">
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
                <label class="form-label small text-muted mb-1">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="">-- Tất cả --</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang áp dụng</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng áp dụng</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">&nbsp;</label>
                <button type="submit" class="btn w-100" style="background: #0B3D91; color: white; border: none;">
                    <i class="bi bi-funnel"></i> Tìm
                </button>
            </div>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Mã</th>
                    <th>Tiêu đề</th>
                    <th>Phạm vi</th>
                    <th>Mức độ</th>
                    <th>Trạng thái</th>
                    <th>Ngày ban hành</th>
                    <th>Người tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($regulations as $regulation)
                    <tr>
                        <td><strong>{{ $regulation->code }}</strong></td>
                        <td>{{ $regulation->title }}</td>
                        <td>
                            @if($regulation->scope == 'all_clubs')
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
                        <td>
                            @if($regulation->status == 'active')
                                <span class="badge bg-success">Đang áp dụng</span>
                            @else
                                <span class="badge bg-secondary">Ngừng áp dụng</span>
                            @endif
                        </td>
                        <td>{{ $regulation->issued_date->format('d/m/Y') }}</td>
                        <td>{{ $regulation->creator->name ?? 'N/A' }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.regulations.show', $regulation->id) }}" 
                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.regulations.edit', $regulation->id) }}" 
                                   class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.regulations.toggle-status', $regulation->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $regulation->status == 'active' ? 'secondary' : 'success' }}" 
                                            title="{{ $regulation->status == 'active' ? 'Ngừng áp dụng' : 'Kích hoạt' }}">
                                        <i class="bi bi-{{ $regulation->status == 'active' ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
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

@endsection

