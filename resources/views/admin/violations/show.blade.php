@extends('layouts.admin')

@section('title', 'Chi tiết vi phạm')

@section('content')

<div class="container-fluid mt-3">
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-exclamation-triangle"></i> Chi tiết vi phạm
        </h3>
        <div>
            <a href="{{ route('admin.violations.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            @if($violation->status != 'processed')
                <a href="{{ route('admin.violations.handle', $violation->id) }}" class="btn btn-success">
                    <i class="bi bi-gear"></i> Xử lý kỷ luật
                </a>
            @endif
        </div>
    </div>

    {{-- THÔNG TIN VI PHẠM --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Thông tin vi phạm</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Sinh viên vi phạm:</strong><br>
                        <strong>{{ $violation->user->name ?? 'N/A' }}</strong><br>
                        <span class="text-muted">MSSV: {{ $violation->user->student_code ?? 'N/A' }}</span>
                    </p>
                    <p><strong>CLB:</strong><br>
                        <strong>{{ $violation->club->name ?? 'N/A' }}</strong><br>
                        <span class="text-muted">Mã: {{ $violation->club->code ?? 'N/A' }}</span>
                    </p>
                    <p><strong>Nội quy vi phạm:</strong><br>
                        <strong>{{ $violation->regulation->code ?? 'N/A' }}</strong> - {{ $violation->regulation->title ?? 'N/A' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Mức độ vi phạm:</strong><br>
                        @if($violation->severity == 'light')
                            <span class="badge" style="background-color: #8EDC6E; color: #000;">Nhẹ</span>
                        @elseif($violation->severity == 'medium')
                            <span class="badge" style="background-color: #FFE600; color: #000;">Trung bình</span>
                        @else
                            <span class="badge bg-danger">Nghiêm trọng</span>
                        @endif
                    </p>
                    <p><strong>Thời gian xảy ra:</strong><br>
                        {{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y H:i') }}
                    </p>
                    <p><strong>Người ghi nhận:</strong><br>
                        {{ $violation->recorder->name ?? 'N/A' }} <span class="text-muted">(Chủ nhiệm)</span>
                    </p>
                    <p><strong>Trạng thái:</strong><br>
                        @if($violation->status == 'pending')
                            <span class="badge" style="background-color: #FFE600; color: #000;">Chưa xử lý</span>
                        @elseif($violation->status == 'processed')
                            <span class="badge" style="background-color: #5FB84A; color: white;">Đã xử lý</span>
                        @else
                            <span class="badge" style="background-color: #0B3D91; color: white;">Đang theo dõi</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="mt-3">
                <strong>Mô tả vi phạm:</strong>
                <div class="border p-3 rounded mt-2" style="background-color: #f9fafb; white-space: pre-wrap;">
                    {{ $violation->description }}
                </div>
            </div>
        </div>
    </div>

    {{-- THÔNG TIN XỬ LÝ KỶ LUẬT --}}
    @if($violation->status == 'processed' || $violation->discipline_type)
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Thông tin xử lý kỷ luật</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Hình thức kỷ luật:</strong><br>
                            @if($violation->discipline_type == 'warning')
                                <span class="badge" style="background-color: #FFE600; color: #000;">Cảnh cáo</span>
                            @elseif($violation->discipline_type == 'reprimand')
                                <span class="badge bg-info">Khiển trách</span>
                            @elseif($violation->discipline_type == 'suspension')
                                <span class="badge" style="background-color: #0B3D91; color: white;">Đình chỉ</span>
                            @elseif($violation->discipline_type == 'expulsion')
                                <span class="badge bg-danger">Buộc rời CLB</span>
                            @elseif($violation->discipline_type == 'ban')
                                <span class="badge bg-danger">Cấm tham gia hoạt động</span>
                            @else
                                <span class="text-muted">Chưa xác định</span>
                            @endif
                        </p>
                        @if($violation->discipline_period_start || $violation->discipline_period_end)
                            <p><strong>Thời hạn kỷ luật:</strong><br>
                                @if($violation->discipline_period_start && $violation->discipline_period_end)
                                    Từ {{ \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') }} 
                                    đến {{ \Carbon\Carbon::parse($violation->discipline_period_end)->format('d/m/Y') }}
                                @elseif($violation->discipline_period_start)
                                    Từ {{ \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') }}
                                @else
                                    Không giới hạn
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($violation->processor)
                            <p><strong>Người xử lý:</strong><br>
                                {{ $violation->processor->name ?? 'N/A' }} <span class="text-muted">(Admin)</span>
                            </p>
                        @endif
                        @if($violation->processed_at)
                            <p><strong>Thời gian xử lý:</strong><br>
                                {{ \Carbon\Carbon::parse($violation->processed_at)->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
                @if($violation->discipline_reason)
                    <div class="mt-3">
                        <strong>Lý do xử lý:</strong>
                        <div class="border p-3 rounded mt-2" style="background-color: #f9fafb; white-space: pre-wrap;">
                            {{ $violation->discipline_reason }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection

