@extends('layouts.admin')

@section('title', 'Nhật ký Admin')

@section('content')
<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-journal-text"></i> Nhật ký Admin
    </h3>

    {{-- FILTER FORM --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label fw-bold mb-1">Từ khóa</label>
                        <input type="text" name="keyword" class="form-control form-control-sm" 
                               value="{{ request('keyword') }}" 
                               placeholder="Tìm trong mô tả...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold mb-1">Hành động</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($actions as $key => $label)
                                <option value="{{ $key }}" {{ request('action') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold mb-1">Đối tượng</label>
                        <select name="model_type" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($modelTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('model_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold mb-1">Admin</label>
                        <select name="admin_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold mb-1">Từ ngày</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold mb-1">Đến ngày</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search"></i> Lọc
                        </button>
                        <a href="{{ route('admin.admin-log.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-clockwise"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Thời gian</th>
                    <th>Admin</th>
                    <th>Hành động</th>
                    <th>Đối tượng</th>
                    <th>Mô tả</th>
                    <th>IP Address</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $index }}</td>
                        <td>
                            <small>{{ $log->created_at->format('d/m/Y H:i:s') }}</small>
                        </td>
                        <td>
                            <strong>{{ $log->admin->name ?? 'N/A' }}</strong>
                            @if($log->admin->email ?? null)
                                <br><small class="text-muted">{{ $log->admin->email }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $log->action_name }}</span>
                        </td>
                        <td>
                            {{ $log->model_name }}
                            @if($log->model_id)
                                <br><small class="text-muted">ID: {{ $log->model_id }}</small>
                            @endif
                        </td>
                        <td>
                            <span title="{{ $log->description }}">
                                {{ Str::limit($log->description ?? '—', 50) }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $log->ip_address ?? '—' }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.admin-log.show', $log->id) }}" 
                               class="btn btn-sm btn-primary" title="Xem chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                            Không có nhật ký nào
                        </td>
                    </tr>
                @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $logs->links('vendor.pagination.custom') }}
    </div>

    {{-- EXPORT BUTTONS --}}
    <div class="d-flex justify-content-end gap-2 mt-3">
        <form action="{{ route('admin.admin-log.export') }}" method="GET" class="d-inline">
            @foreach(request()->except(['format', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <input type="hidden" name="format" value="excel">
            <button type="submit" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Xuất Excel
            </button>
        </form>
        <form action="{{ route('admin.admin-log.export') }}" method="GET" class="d-inline">
            @foreach(request()->except(['format', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <input type="hidden" name="format" value="pdf">
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
            </button>
        </form>
    </div>
</div>

@endsection
