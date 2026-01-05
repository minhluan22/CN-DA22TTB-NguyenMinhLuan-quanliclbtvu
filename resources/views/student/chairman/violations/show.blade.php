@extends('layouts.chairman')

@section('title', 'Chi tiết vi phạm - Chủ nhiệm CLB')

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
        max-width: 1200px;
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
    
    .info-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .info-row {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #6b7280;
    }
    
    .info-value {
        color: #1f1f1f;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-exclamation-triangle"></i>
            Chi tiết vi phạm
        </h1>
        <a href="{{ route('student.chairman.violations.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
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

    {{-- THÔNG TIN VI PHẠM --}}
    <div class="info-card">
        <h5 class="mb-3 fw-bold">Thông tin vi phạm</h5>
        <div class="info-row">
            <div class="info-label">Sinh viên vi phạm:</div>
            <div class="info-value">
                <strong>{{ $violation->user->name ?? 'N/A' }}</strong>
                <span class="text-muted">({{ $violation->user->student_code ?? 'N/A' }})</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Nội quy vi phạm:</div>
            <div class="info-value">
                <strong>{{ $violation->regulation->title ?? 'N/A' }}</strong>
                <br><small class="text-muted">({{ $violation->regulation->code ?? 'N/A' }})</small>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Mức độ vi phạm:</div>
            <div class="info-value">
                @if($violation->severity == 'light')
                    <span class="badge bg-success">Nhẹ</span>
                @elseif($violation->severity == 'medium')
                    <span class="badge bg-warning text-dark">Trung bình</span>
                @else
                    <span class="badge bg-danger">Nghiêm trọng</span>
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Thời gian xảy ra:</div>
            <div class="info-value">
                {{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y H:i') }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Mô tả vi phạm:</div>
            <div class="info-value">
                <div class="border p-3 rounded" style="white-space: pre-wrap; background: #f9fafb;">
                    {{ $violation->description }}
                </div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Người ghi nhận:</div>
            <div class="info-value">
                {{ $violation->recorder->name ?? 'N/A' }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Trạng thái:</div>
            <div class="info-value">
                @if($violation->status == 'pending')
                    <span class="badge bg-warning text-dark">Chưa xử lý</span>
                @elseif($violation->status == 'processed')
                    <span class="badge bg-success">Đã xử lý</span>
                @else
                    <span class="badge bg-info">Đang theo dõi</span>
                @endif
            </div>
        </div>
    </div>

    {{-- THÔNG TIN XỬ LÝ KỶ LUẬT (nếu có) --}}
    @if($violation->status == 'processed' && $violation->discipline_type)
        <div class="info-card">
            <h5 class="mb-3 fw-bold">Thông tin xử lý kỷ luật</h5>
            <div class="info-row">
                <div class="info-label">Hình thức kỷ luật:</div>
                <div class="info-value">
                    @if($violation->discipline_type == 'warning')
                        <span class="badge bg-warning text-dark">Cảnh cáo</span>
                    @elseif($violation->discipline_type == 'reprimand')
                        <span class="badge bg-orange">Khiển trách</span>
                    @elseif($violation->discipline_type == 'suspension')
                        <span class="badge bg-warning text-dark">Đình chỉ</span>
                    @elseif($violation->discipline_type == 'expulsion')
                        <span class="badge bg-danger">Buộc rời CLB</span>
                    @elseif($violation->discipline_type == 'ban')
                        <span class="badge bg-secondary">Cấm tham gia</span>
                    @endif
                </div>
            </div>
            @if($violation->discipline_reason)
                <div class="info-row">
                    <div class="info-label">Lý do xử lý:</div>
                    <div class="info-value">
                        <div class="border p-3 rounded" style="white-space: pre-wrap; background: #f9fafb;">
                            {{ $violation->discipline_reason }}
                        </div>
                    </div>
                </div>
            @endif
            @if($violation->discipline_period_start || $violation->discipline_period_end)
                <div class="info-row">
                    <div class="info-label">Thời hạn kỷ luật:</div>
                    <div class="info-value">
                        @if($violation->discipline_period_start && $violation->discipline_period_end)
                            Từ {{ \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') }} 
                            đến {{ \Carbon\Carbon::parse($violation->discipline_period_end)->format('d/m/Y') }}
                        @elseif($violation->discipline_period_start)
                            Từ {{ \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') }}
                        @else
                            Không giới hạn
                        @endif
                    </div>
                </div>
            @endif
            @if($violation->processor)
                <div class="info-row">
                    <div class="info-label">Người xử lý:</div>
                    <div class="info-value">
                        {{ $violation->processor->name ?? 'N/A' }} (Admin)
                    </div>
                </div>
            @endif
            @if($violation->processed_at)
                <div class="info-row">
                    <div class="info-label">Thời gian xử lý:</div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($violation->processed_at)->format('d/m/Y H:i') }}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
