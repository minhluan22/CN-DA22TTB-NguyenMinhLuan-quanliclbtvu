@extends('layouts.admin')

@section('title', 'Xuất báo cáo')

@push('styles')
<style>
    .statistics-container {
        /* Removed - using container-fluid mt-3 instead */
    }
    .export-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }
    .export-card h5 {
        color: #0033A0;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .info-card {
        background: linear-gradient(135deg, #0B3D9120 0%, #0033A010 100%);
        border-left: 4px solid #0B3D91;
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
    }
    .report-type-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.3s;
        cursor: pointer;
    }
    .report-type-card:hover {
        border-color: #0B3D91;
        background: #0B3D9105;
        transform: translateX(4px);
    }
    .report-type-card.active {
        border-color: #0B3D91;
        background: #0B3D9110;
    }
    .report-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 16px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-file-earmark-arrow-down"></i> Xuất báo cáo
        </h2>
        <div class="badge" style="background-color: #0B3D91; color: white; padding: 8px 16px; font-size: 14px;">
            <i class="bi bi-calendar3"></i> Năm học {{ date('Y') }} - {{ date('Y') + 1 }}
        </div>
    </div>

    {{-- Export Form --}}
    <div class="export-card">
        <h5 class="mb-3">
            <i class="bi bi-file-earmark-text"></i> Chọn loại báo cáo
        </h5>
        <p class="text-muted mb-4">Chọn loại báo cáo và định dạng để xuất file.</p>

        <form method="POST" action="{{ route('admin.statistics.export.generate') }}" id="exportForm">
            @csrf
            
            {{-- Report Type Selection --}}
            <div class="mb-4">
                <label class="form-label fw-bold mb-3">Loại báo cáo <span class="text-danger">*</span></label>
                <div>
                    <div class="report-type-card" onclick="selectReportType('club_overview')">
                        <div class="d-flex align-items-center">
                            <div class="report-icon" style="background: #0033A020; color: #0033A0;">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Báo cáo tổng quan CLB</div>
                                <small class="text-muted">Thống kê tổng quan về CLB: số thành viên, hoạt động, tài chính</small>
                            </div>
                            <input type="radio" name="report_type" value="club_overview" class="ms-2" required>
                        </div>
                    </div>

                    <div class="report-type-card" onclick="selectReportType('members')">
                        <div class="d-flex align-items-center">
                            <div class="report-icon" style="background: #5FB84A20; color: #5FB84A;">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Báo cáo danh sách thành viên</div>
                                <small class="text-muted">Danh sách đầy đủ thành viên CLB với thông tin chi tiết</small>
                            </div>
                            <input type="radio" name="report_type" value="members" class="ms-2" required>
                        </div>
                    </div>

                    <div class="report-type-card" onclick="selectReportType('activities')">
                        <div class="d-flex align-items-center">
                            <div class="report-icon" style="background: #0B3D9120; color: #0B3D91;">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Báo cáo hoạt động CLB</div>
                                <small class="text-muted">Danh sách và thống kê các hoạt động trong khoảng thời gian được chọn</small>
                            </div>
                            <input type="radio" name="report_type" value="activities" class="ms-2" required>
                        </div>
                    </div>

                    <div class="report-type-card" onclick="selectReportType('participations')">
                        <div class="d-flex align-items-center">
                            <div class="report-icon" style="background: #8EDC6E20; color: #8EDC6E;">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Báo cáo tham gia hoạt động</div>
                                <small class="text-muted">Thống kê số lượng đăng ký và tham gia hoạt động của thành viên</small>
                            </div>
                            <input type="radio" name="report_type" value="participations" class="ms-2" required>
                        </div>
                    </div>

                    <div class="report-type-card" onclick="selectReportType('violations')">
                        <div class="d-flex align-items-center">
                            <div class="report-icon" style="background: #FFE60020; color: #FFE600;">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Báo cáo vi phạm – kỷ luật</div>
                                <small class="text-muted">Danh sách và thống kê các vi phạm và kỷ luật đã xử lý</small>
                            </div>
                            <input type="radio" name="report_type" value="violations" class="ms-2" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Date Range --}}
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Từ ngày <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" required 
                           value="{{ date('Y-m-01') }}" style="border-color: #0B3D91;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Đến ngày <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control" required 
                           value="{{ date('Y-m-t') }}" style="border-color: #0B3D91;">
                </div>
            </div>

            {{-- Club Filter (for Chairman) --}}
            @if(Auth::user()->role_id == 3) {{-- Chairman role --}}
                @php
                    $chairmanClub = \Illuminate\Support\Facades\DB::table('club_members')
                        ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
                        ->where('club_members.user_id', Auth::id())
                        ->where('club_members.position', 'chairman')
                        ->where('club_members.status', 'approved')
                        ->select('clubs.id', 'clubs.name', 'clubs.code')
                        ->first();
                @endphp
                @if($chairmanClub)
                    <input type="hidden" name="club_id" value="{{ $chairmanClub->id }}">
                    <div class="alert alert-info mb-3" style="background-color: #0B3D9120; border-color: #0B3D91; color: #0033A0;">
                        <i class="bi bi-info-circle"></i> Báo cáo sẽ chỉ xuất dữ liệu của CLB: <strong>{{ $chairmanClub->name }} ({{ $chairmanClub->code }})</strong>
                    </div>
                @endif
            @else
                {{-- Admin can select club --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">CLB (Tùy chọn)</label>
                    <select name="club_id" class="form-control" style="border-color: #0B3D91;">
                        <option value="">-- Tất cả CLB --</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}">{{ $club->code }} - {{ $club->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Format Selection --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Định dạng <span class="text-danger">*</span></label>
                    <select name="format" class="form-control" required style="border-color: #0B3D91;">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel (XLSX)</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="background-color: #0B3D91; color: white; padding: 12px 32px;">
                <i class="bi bi-download"></i> Xuất báo cáo
            </button>
        </form>
    </div>

    {{-- Instructions --}}
    <div class="export-card">
        <h5 class="mb-3">
            <i class="bi bi-info-circle"></i> Hướng dẫn
        </h5>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="info-card">
                    <div class="fw-bold mb-2"><i class="bi bi-building"></i> Báo cáo tổng quan CLB</div>
                    <small>Thống kê tổng quan về CLB bao gồm: số lượng thành viên, số hoạt động đã tổ chức, tài chính, và các chỉ số khác.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="fw-bold mb-2"><i class="bi bi-people"></i> Báo cáo danh sách thành viên</div>
                    <small>Danh sách đầy đủ thành viên CLB với thông tin: MSSV, họ tên, khoa, lớp, chức vụ, ngày tham gia.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="fw-bold mb-2"><i class="bi bi-calendar-event"></i> Báo cáo hoạt động CLB</div>
                    <small>Danh sách và thống kê các hoạt động trong khoảng thời gian được chọn, bao gồm thông tin chi tiết về từng hoạt động.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <div class="fw-bold mb-2"><i class="bi bi-person-check"></i> Báo cáo tham gia hoạt động</div>
                    <small>Thống kê số lượng đăng ký và tham gia hoạt động của thành viên, bao gồm tỷ lệ tham gia và các chỉ số liên quan.</small>
                </div>
            </div>
            <div class="col-md-12">
                <div class="info-card">
                    <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle"></i> Báo cáo vi phạm – kỷ luật</div>
                    <small>Danh sách và thống kê các vi phạm và kỷ luật đã xử lý, bao gồm thông tin về loại vi phạm, mức độ, và biện pháp xử lý.</small>
                </div>
            </div>
        </div>
        <div class="alert alert-warning mt-3">
            <i class="bi bi-info-circle"></i> <strong>Lưu ý:</strong> Các báo cáo được sử dụng cho mục đích báo cáo nhà trường, lưu hồ sơ quản lý và đánh giá cuối năm học. Chủ nhiệm chỉ có thể xuất báo cáo của CLB mình quản lý.
        </div>
    </div>
</div>

<script>
function selectReportType(value) {
    // Remove active class from all cards
    document.querySelectorAll('.report-type-card').forEach(card => {
        card.classList.remove('active');
    });
    
    // Add active class to selected card
    event.currentTarget.classList.add('active');
    
    // Set radio button
    document.querySelector(`input[value="${value}"]`).checked = true;
}

// Add click handler to all report type cards
document.querySelectorAll('.report-type-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.tagName !== 'INPUT') {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                selectReportType(radio.value);
            }
        }
    });
});
</script>
@endsection
