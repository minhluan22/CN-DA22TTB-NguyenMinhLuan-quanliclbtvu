@extends('layouts.admin')

@section('title', 'Xử lý kỷ luật')

@section('content')

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

<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-exclamation-triangle"></i> Xử lý kỷ luật
    </h3>

    {{-- FILTER FORM --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-1">Từ khóa</label>
                        <input type="text" name="search" class="form-control form-control-sm" 
                               value="{{ request('search') }}" 
                               placeholder="Tên sinh viên, MSSV, CLB...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold mb-1">CLB</label>
                        <select name="club_id" class="form-select form-select-sm">
                        <option value="">-- Tất cả CLB --</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                {{ $club->code }} - {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold mb-1">Mức độ</label>
                    <select name="severity" class="form-select form-select-sm">
                        <option value="">-- Tất cả --</option>
                        <option value="light" {{ request('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                        <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="serious" {{ request('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-hover">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Sinh viên</th>
                    <th>CLB</th>
                    <th>Nội quy vi phạm</th>
                    <th>Mô tả</th>
                    <th>Mức độ</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $index => $violation)
                    <tr>
                        <td>{{ $violations->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $violation->user->name ?? 'N/A' }}</strong>
                            @if($violation->user->student_code)
                                <br><small class="text-muted">({{ $violation->user->student_code }})</small>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $violation->club->code ?? 'N/A' }}</small><br>
                            {{ $violation->club->name ?? 'N/A' }}
                        </td>
                        <td>
                            <strong>{{ $violation->regulation->code ?? 'N/A' }}</strong><br>
                            <small>{{ Str::limit($violation->regulation->title ?? 'N/A', 50) }}</small>
                        </td>
                        <td>{{ Str::limit($violation->description, 80) }}</td>
                        <td>
                            @if($violation->severity == 'light')
                                <span class="badge bg-success">Nhẹ</span>
                            @elseif($violation->severity == 'medium')
                                <span class="badge bg-warning text-dark">Trung bình</span>
                            @else
                                <span class="badge bg-danger">Nghiêm trọng</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y H:i') }}</small>
                        </td>
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
                            <div class="btn-group">
                                <a href="{{ route('admin.violations.show', $violation->id) }}" 
                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.violations.handle', $violation->id) }}" 
                                   class="btn btn-sm btn-warning" title="Xử lý kỷ luật">
                                    <i class="bi bi-hammer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Không có vi phạm nào cần xử lý
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $violations->links('vendor.pagination.custom') }}
    </div>
</div>

@endsection

