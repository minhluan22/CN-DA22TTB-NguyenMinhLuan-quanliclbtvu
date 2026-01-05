@extends('layouts.admin')

@section('title', 'Thống kê hoạt động theo thời gian')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        height: 100%;
        border-left: 4px solid transparent;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .stat-card.primary { border-left-color: #0033A0; }
    .stat-card.success { border-left-color: #5FB84A; }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }
    .stat-value {
        font-size: 36px;
        font-weight: 700;
        color: #0033A0;
        margin: 8px 0;
    }
    .stat-label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-calendar-range"></i> Thống kê hoạt động theo thời gian
    </h3>

    {{-- FILTER FORM --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold mb-1">Từ ngày</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" 
                               value="{{ $startDate }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold mb-1">Đến ngày</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" 
                               value="{{ $endDate }}" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Tìm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TỔNG QUAN --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card stat-card primary">
                <div class="card-body">
                    <div class="stat-icon" style="background: #0033A020; color: #0033A0;">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="stat-value">{{ number_format($totalEvents) }}</div>
                    <div class="stat-label">Tổng số hoạt động</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card success">
                <div class="card-body">
                    <div class="stat-icon" style="background: #5FB84A20; color: #5FB84A;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-value">{{ number_format($totalParticipations) }}</div>
                    <div class="stat-label">Tổng lượt tham gia</div>
                </div>
            </div>
        </div>
    </div>

    {{-- THỐNG KÊ THEO THÁNG --}}
    <div class="card mb-4" id="monthly-stats">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar3"></i> Thống kê theo tháng</span>
            <form method="GET" class="d-inline-flex gap-2 align-items-center" style="margin: 0;" onsubmit="setTimeout(() => document.getElementById('monthly-stats').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100); return true;">
                @if(request('start_date'))
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                @endif
                @if(request('end_date'))
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                @endif
                <select name="filter_month" class="form-select form-select-sm" style="width: auto;">
                    <option value="">-- Tất cả tháng --</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('filter_month') == $i ? 'selected' : '' }}>
                            Tháng {{ $i }}
                        </option>
                    @endfor
                </select>
                <select name="filter_year" class="form-select form-select-sm" style="width: auto;">
                    <option value="">-- Tất cả năm --</option>
                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                        <option value="{{ $year }}" {{ request('filter_year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-sm btn-light">
                    <i class="bi bi-funnel"></i> Tìm
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tháng</th>
                            <th>Tổng hoạt động</th>
                            <th>Đang diễn ra</th>
                            <th>Đã kết thúc</th>
                            <th>Đã hủy</th>
                            <th>Bị vô hiệu hóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyStats as $stat)
                            <tr>
                                <td><strong>{{ $stat->month }}</strong></td>
                                <td><span class="badge" style="background-color: #0033A0; color: white;">{{ $stat->event_count }}</span></td>
                                <td><span class="badge" style="background-color: #0B3D91; color: white;">{{ $stat->ongoing_count }}</span></td>
                                <td><span class="badge" style="background-color: #5FB84A; color: white;">{{ $stat->finished_count }}</span></td>
                                <td><span class="badge" style="background-color: #FFE600; color: #000;">{{ $stat->cancelled_count }}</span></td>
                                <td><span class="badge" style="background-color: #B84A5F; color: white;">{{ $stat->disabled_count ?? 0 }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- THỐNG KÊ THEO HỌC KỲ --}}
    @if(count($semesterStats) > 0)
    <div class="card mb-4" id="semester-stats">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar2-range"></i> Thống kê theo học kỳ</span>
            <form method="GET" class="d-inline-flex gap-2 align-items-center" style="margin: 0;" onsubmit="setTimeout(() => document.getElementById('semester-stats').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100); return true;">
                @if(request('start_date'))
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                @endif
                @if(request('end_date'))
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                @endif
                <select name="filter_semester" class="form-select form-select-sm" style="width: auto;">
                    <option value="">-- Tất cả học kỳ --</option>
                    <option value="HK1" {{ request('filter_semester') == 'HK1' ? 'selected' : '' }}>Học kỳ 1</option>
                    <option value="HK2" {{ request('filter_semester') == 'HK2' ? 'selected' : '' }}>Học kỳ 2</option>
                    <option value="He" {{ request('filter_semester') == 'He' ? 'selected' : '' }}>Hè</option>
                </select>
                <select name="filter_semester_year" class="form-select form-select-sm" style="width: auto;">
                    <option value="">-- Tất cả năm --</option>
                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                        <option value="{{ $year }}" {{ request('filter_semester_year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-sm btn-light">
                    <i class="bi bi-funnel"></i> Tìm
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Học kỳ</th>
                            <th>Tổng hoạt động</th>
                            <th>Đang diễn ra</th>
                            <th>Đã kết thúc</th>
                            <th>Đã hủy</th>
                            <th>Bị vô hiệu hóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semesterStats as $stat)
                            <tr>
                                <td><strong>{{ $stat['semester'] }}</strong></td>
                                <td><span class="badge" style="background-color: #0033A0; color: white;">{{ $stat['event_count'] }}</span></td>
                                <td><span class="badge" style="background-color: #0B3D91; color: white;">{{ $stat['ongoing_count'] }}</span></td>
                                <td><span class="badge" style="background-color: #5FB84A; color: white;">{{ $stat['finished_count'] }}</span></td>
                                <td><span class="badge" style="background-color: #FFE600; color: #000;">{{ $stat['cancelled_count'] }}</span></td>
                                <td><span class="badge" style="background-color: #B84A5F; color: white;">{{ $stat['disabled_count'] ?? 0 }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- THỐNG KÊ THEO NĂM HỌC --}}
    @if($yearlyStats->count() > 0)
    <div class="card mb-4" id="yearly-stats">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar4-range"></i> Thống kê theo năm học</span>
            <form method="GET" class="d-inline-flex gap-2 align-items-center" style="margin: 0;" onsubmit="setTimeout(() => document.getElementById('yearly-stats').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100); return true;">
                @if(request('start_date'))
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                @endif
                @if(request('end_date'))
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                @endif
                <select name="filter_year_only" class="form-select form-select-sm" style="width: auto;">
                    <option value="">-- Tất cả năm --</option>
                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                        <option value="{{ $year }}" {{ request('filter_year_only') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-sm btn-light">
                    <i class="bi bi-funnel"></i> Tìm
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Năm</th>
                            <th>Tổng hoạt động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($yearlyStats as $stat)
                            <tr>
                                <td><strong>{{ $stat->year }}</strong></td>
                                <td><span class="badge" style="background-color: #0033A0; color: white;">{{ $stat->event_count }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra xem có filter parameter nào không, nếu có thì scroll đến section tương ứng
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('filter_month') || urlParams.has('filter_year')) {
        setTimeout(() => {
            document.getElementById('monthly-stats')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
    } else if (urlParams.has('filter_semester') || urlParams.has('filter_semester_year')) {
        setTimeout(() => {
            document.getElementById('semester-stats')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
    } else if (urlParams.has('filter_year_only')) {
        setTimeout(() => {
            document.getElementById('yearly-stats')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
    }
});
</script>
@endpush

@endsection
