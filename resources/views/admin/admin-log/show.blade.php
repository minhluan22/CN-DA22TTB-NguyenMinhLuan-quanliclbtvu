@extends('layouts.admin')

@section('title', 'Chi tiết nhật ký')

@section('content')
<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-journal-text"></i> Chi tiết nhật ký
        </h3>
        <a href="{{ route('admin.admin-log.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Thời gian:</strong><br>
                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                </div>
                <div class="col-md-6">
                    <strong>Admin:</strong><br>
                    {{ $log->admin->name ?? 'N/A' }}
                    @if($log->admin->email ?? null)
                        <br><small class="text-muted">{{ $log->admin->email }}</small>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Hành động:</strong><br>
                    <span class="badge bg-info">{{ $log->action_name }}</span>
                </div>
                <div class="col-md-6">
                    <strong>Đối tượng:</strong><br>
                    {{ $log->model_name }}
                    @if($log->model_id)
                        <br><small class="text-muted">ID: {{ $log->model_id }}</small>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Mô tả:</strong><br>
                    {{ $log->description ?? '—' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>IP Address:</strong><br>
                    {{ $log->ip_address ?? '—' }}
                </div>
                <div class="col-md-6">
                    <strong>User Agent:</strong><br>
                    <small>{{ $log->user_agent ?? '—' }}</small>
                </div>
            </div>

            @if($log->old_data || $log->new_data)
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Dữ liệu cũ:</strong>
                        <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                    <div class="col-md-6">
                        <strong>Dữ liệu mới:</strong>
                        <pre class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;"><code>{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

