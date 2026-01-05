@extends('layouts.chairman')

@section('title', 'Lịch sử kỷ luật - Theo thành viên - Chủ nhiệm CLB')

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
    
    .info-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .stat-card {
        text-align: center;
        padding: 20px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .stat-number {
        font-size: 32px;
        font-weight: bold;
        color: #0033A0;
    }
    
    .stat-label {
        font-size: 14px;
        color: #6b7280;
        margin-top: 8px;
    }
    
    .table-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-clock-history"></i>
            Lịch sử kỷ luật - Theo thành viên
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

    {{-- FORM CHỌN THÀNH VIÊN --}}
    <div class="filter-card">
        <form method="GET" class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-bold">Chọn thành viên <span class="text-danger">*</span></label>
                <select name="member_id" class="form-control" required>
                    <option value="">-- Chọn thành viên --</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }} ({{ $member->student_code }}) - 
                            @if($member->position == 'chairman') Chủ nhiệm
                            @elseif($member->position == 'vice_chairman') Phó chủ nhiệm
                            @else Thành viên
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Mức độ</label>
                <select name="severity" class="form-control">
                    <option value="">-- Tất cả --</option>
                    <option value="light" {{ request('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                    <option value="serious" {{ request('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="">-- Tất cả --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chưa xử lý</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Đã xử lý</option>
                    <option value="monitoring" {{ request('status') == 'monitoring' ? 'selected' : '' }}>Đang theo dõi</option>
                </select>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Xem lịch sử
                </button>
            </div>
        </form>
    </div>

    @if($selectedMember)
        {{-- THÔNG TIN THÀNH VIÊN --}}
        <div class="info-card">
            <h5 class="mb-3 fw-bold">Thông tin thành viên</h5>
            <div class="row">
                <div class="col-md-3">
                    <strong>Họ tên:</strong> {{ $selectedMember->name }}
                </div>
                <div class="col-md-3">
                    <strong>MSSV:</strong> {{ $selectedMember->student_code }}
                </div>
                <div class="col-md-3">
                    <strong>Chức vụ:</strong>
                    @if($selectedMember->position == 'chairman')
                        <span class="badge bg-danger">Chủ nhiệm</span>
                    @elseif($selectedMember->position == 'vice_chairman')
                        <span class="badge bg-warning text-dark">Phó chủ nhiệm</span>
                    @else
                        <span class="badge bg-secondary">Thành viên</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>CLB:</strong> {{ $club->name }}
                </div>
            </div>
        </div>

        {{-- THỐNG KÊ --}}
        @if($memberStats)
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">{{ $memberStats['total'] }}</div>
                        <div class="stat-label">Tổng số lần vi phạm</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-success">{{ $memberStats['by_severity']['light'] }}</div>
                        <div class="stat-label">Nhẹ</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-warning">{{ $memberStats['by_severity']['medium'] }}</div>
                        <div class="stat-label">Trung bình</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-danger">{{ $memberStats['by_severity']['serious'] }}</div>
                        <div class="stat-label">Nghiêm trọng</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- DANH SÁCH VI PHẠM --}}
        <div class="table-card">
            <h5 class="mb-3 fw-bold">Danh sách vi phạm chi tiết</h5>
            @if($memberViolations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mã</th>
                                <th>Nội quy vi phạm</th>
                                <th>Mô tả vi phạm</th>
                                <th>Mức độ</th>
                                <th>Hình thức kỷ luật</th>
                                <th>Trạng thái</th>
                                <th>Ngày vi phạm</th>
                                <th>Ngày xử lý</th>
                                <th>Người xử lý</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memberViolations as $violation)
                                <tr>
                                    <td>#{{ $violation->id }}</td>
                                    <td>
                                        {{ $violation->regulation->code ?? 'N/A' }}
                                        <br><small class="text-muted">{{ $violation->regulation->title ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
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
                                    <td>
                                        @if($violation->discipline_type)
                                            @if($violation->discipline_type == 'warning')
                                                <span class="badge bg-warning text-dark">Cảnh cáo</span>
                                            @elseif($violation->discipline_type == 'reprimand')
                                                <span class="badge" style="background-color: #fd7e14; color: white;">Khiển trách</span>
                                            @elseif($violation->discipline_type == 'suspension')
                                                <span class="badge bg-warning text-dark">Đình chỉ</span>
                                            @elseif($violation->discipline_type == 'expulsion')
                                                <span class="badge bg-danger">Buộc rời</span>
                                            @elseif($violation->discipline_type == 'ban')
                                                <span class="badge bg-secondary">Cấm tham gia</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
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
                                    <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($violation->processed_at)
                                            {{ \Carbon\Carbon::parse($violation->processed_at)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($violation->processor)
                                            {{ $violation->processor->name }} (Admin)
                                        @elseif($violation->recorder)
                                            {{ $violation->recorder->name }} (Chủ nhiệm)
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('student.chairman.violations.show', $violation->id) }}" 
                                           class="btn btn-sm btn-info" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    Không có vi phạm nào
                </div>
            @endif
        </div>
    @else
        <div class="table-card">
            <div class="text-center text-muted py-5">
                <i class="bi bi-info-circle" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                <p>Vui lòng chọn thành viên để xem lịch sử kỷ luật</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
