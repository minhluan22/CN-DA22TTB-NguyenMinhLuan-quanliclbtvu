@extends('layouts.admin')

@section('title', 'Yêu cầu hỗ trợ từ Chủ nhiệm CLB')

@section('content')
<div class="container-fluid mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h3 class="fw-bold mb-4">
        <i class="bi bi-person-badge"></i> Yêu cầu hỗ trợ từ Chủ nhiệm CLB
    </h3>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-1">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="Tên chủ nhiệm, CLB, tiêu đề..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold mb-1">Trạng thái</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Mở</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold mb-1">Mức độ</label>
                        <select name="priority" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.support.chairman-requests') }}" class="btn btn-warning btn-sm w-100">
                            <i class="bi bi-arrow-clockwise"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Chủ nhiệm</th>
                                <th>CLB</th>
                                <th>Tiêu đề</th>
                                <th>Mức độ</th>
                                <th>Ngày gửi</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td><strong>{{ $request->user->name ?? 'N/A' }}</strong></td>
                                    <td>
                                        @if($request->club)
                                            <span class="badge bg-info">{{ $request->club->code }}</span><br>
                                            <small class="text-muted">{{ Str::limit($request->club->name, 30) }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($request->subject, 50) }}</td>
                                    <td>
                                        @php
                                            $priorityClass = match($request->priority) {
                                                'high' => 'danger',
                                                'medium' => 'warning text-dark',
                                                default => 'info'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $priorityClass }}">
                                            {{ $request->priority_label }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($request->status) {
                                                'open' => 'warning text-dark',
                                                'resolved' => 'success',
                                                'in_progress' => 'info',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ $request->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.support.show', $request->id) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6b7280;"></i>
                    <p class="text-muted mt-3 mb-0">Chưa có yêu cầu nào từ chủ nhiệm CLB.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- PAGINATION --}}
    @if($requests->count() > 0)
        <div class="d-flex justify-content-center mt-3">
            {{ $requests->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
@endsection

