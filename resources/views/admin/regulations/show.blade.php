@extends('layouts.admin')

@section('title', 'Chi tiết nội quy')

@section('content')

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-file-text"></i> Chi tiết nội quy
        </h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.regulations.edit', $regulation->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.regulations.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $regulation->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Mã nội quy:</strong> {{ $regulation->code }}
                    </div>
                    <div class="mb-3">
                        <strong>Nội dung:</strong>
                        <div class="mt-2 p-4 bg-light rounded" style="line-height: 1.8;">
                            {!! \Illuminate\Support\Str::markdown($regulation->content) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Thông tin nội quy</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Phạm vi áp dụng:</strong><br>
                        @if($regulation->scope == 'all_clubs')
                            <span class="badge bg-info">Toàn hệ thống</span>
                        @else
                            <span class="badge bg-secondary">{{ $regulation->club->name ?? 'N/A' }}</span>
                        @endif
                    </div>
                    <div class="mb-2">
                        <strong>Mức độ:</strong><br>
                        @if($regulation->severity == 'light')
                            <span class="badge bg-success">Nhẹ</span>
                        @elseif($regulation->severity == 'medium')
                            <span class="badge bg-warning text-dark">Trung bình</span>
                        @else
                            <span class="badge bg-danger">Nghiêm trọng</span>
                        @endif
                    </div>
                    <div class="mb-2">
                        <strong>Trạng thái:</strong><br>
                        @if($regulation->status == 'active')
                            <span class="badge bg-success">Đang áp dụng</span>
                        @else
                            <span class="badge bg-secondary">Ngừng áp dụng</span>
                        @endif
                    </div>
                    <div class="mb-2">
                        <strong>Ngày ban hành:</strong><br>
                        {{ $regulation->issued_date->format('d/m/Y') }}
                    </div>
                    <div class="mb-2">
                        <strong>Người tạo:</strong><br>
                        {{ $regulation->creator->name ?? 'N/A' }}
                    </div>
                    @if($regulation->updater)
                        <div class="mb-2">
                            <strong>Người cập nhật:</strong><br>
                            {{ $regulation->updater->name }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">Lịch sử vi phạm</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <strong>{{ $regulation->violations->count() }}</strong> vi phạm đã ghi nhận
                    </p>
                    @if($regulation->violations->count() > 0)
                        <a href="{{ route('admin.violations.index', ['regulation_id' => $regulation->id]) }}" 
                           class="btn btn-sm btn-primary mt-2">
                            Xem danh sách vi phạm
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Style cho nội dung markdown */
    .bg-light h1, .bg-light h2, .bg-light h3 {
        color: #0B3D91;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .bg-light h1 {
        font-size: 1.5rem;
        font-weight: bold;
        border-bottom: 2px solid #0B3D91;
        padding-bottom: 0.5rem;
    }
    .bg-light h2 {
        font-size: 1.3rem;
        font-weight: 600;
    }
    .bg-light h3 {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .bg-light strong {
        color: #0033A0;
        font-weight: 600;
    }
    .bg-light ul, .bg-light ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .bg-light li {
        margin-bottom: 0.5rem;
    }
    .bg-light p {
        margin-bottom: 1rem;
    }
</style>
@endpush

