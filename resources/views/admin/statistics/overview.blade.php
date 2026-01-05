@extends('layouts.admin')

@section('title', 'Tổng quan hệ thống')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        cursor: pointer;
        border-left: 4px solid transparent;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }
    .stat-card.primary { border-left-color: #0033A0; }
    .stat-card.success { border-left-color: #5FB84A; }
    .stat-card.warning { border-left-color: #FFE600; }
    .stat-card.info { border-left-color: #0B3D91; }
    .stat-card.danger { border-left-color: #dc3545; }
    
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
        line-height: 1.2;
    }
    .stat-label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .stat-change {
        font-size: 12px;
        font-weight: 600;
        margin-top: 4px;
    }
    .stat-change.positive { color: #5FB84A; }
    .stat-change.negative { color: #dc3545; }
    
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-top: 24px;
        height: 100%;
    }
    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f1f1f;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .filter-section {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }
    .ranking-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .ranking-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(4px);
    }
    .ranking-number {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 14px;
        margin-right: 12px;
    }
    .ranking-number.gold { background: linear-gradient(135deg, #FFE600, #FFD700); }
    .ranking-number.silver { background: linear-gradient(135deg, #C0C0C0, #A8A8A8); }
    .ranking-number.bronze { background: linear-gradient(135deg, #CD7F32, #B8860B); }
    .ranking-number.default { background: #0B3D91; }
    
    .metric-card {
        background: linear-gradient(135deg, #0033A0 0%, #0B3D91 100%);
        color: white;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
    }
    .metric-value {
        font-size: 48px;
        font-weight: 700;
        margin: 12px 0;
    }
    .metric-label {
        font-size: 14px;
        opacity: 0.9;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-speedometer2"></i> Tổng quan hệ thống
        </h2>
        <div class="badge" style="background-color: #0B3D91; color: white; padding: 8px 16px; font-size: 14px;">
            <i class="bi bi-calendar3"></i> Năm học {{ $academicYear }} - {{ $academicYear + 1 }}
        </div>
    </div>

    {{-- Filter --}}
    <div class="filter-section">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 fw-bold">Năm học</label>
                <select name="academic_year" class="form-control" onchange="this.form.submit()" style="border-color: #0B3D91;">
                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                        <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                            {{ $year }} - {{ $year + 1 }}
                        </option>
                    @endfor
                </select>
            </div>
        </form>
    </div>

    {{-- Summary Cards Row 1 --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary" onclick="window.location.href='{{ route('admin.statistics.clubs') }}'">
                <div class="stat-icon" style="background: #0033A020; color: #0033A0;">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-value">{{ number_format($totalClubs) }}</div>
                <div class="stat-label">Tổng số CLB</div>
                <div class="stat-change">
                    <i class="bi bi-check-circle-fill"></i> {{ $activeClubs }} đang hoạt động
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card success" onclick="window.location.href='{{ route('admin.statistics.members') }}'">
                <div class="stat-icon" style="background: #5FB84A20; color: #5FB84A;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-value">{{ number_format($totalMembers) }}</div>
                <div class="stat-label">Sinh viên tham gia CLB</div>
                <div class="stat-change {{ $membersGrowth >= 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ $membersGrowth >= 0 ? 'up' : 'down' }}-circle"></i> 
                    {{ abs($membersGrowth) }}% so với tháng trước
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card info" onclick="window.location.href='{{ route('admin.statistics.activities') }}'">
                <div class="stat-icon" style="background: #0B3D9120; color: #0B3D91;">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div class="stat-value">{{ number_format($totalEvents) }}</div>
                <div class="stat-label">Hoạt động/Sự kiện</div>
                <div class="stat-change {{ $eventsGrowth >= 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ $eventsGrowth >= 0 ? 'up' : 'down' }}-circle"></i> 
                    {{ abs($eventsGrowth) }}% so với tháng trước
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card danger" onclick="window.location.href='{{ route('admin.violations.index') }}'">
                <div class="stat-icon" style="background: #dc354520; color: #dc3545;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="stat-value" style="color: #dc3545;">{{ number_format($totalViolations + $activityViolations) }}</div>
                <div class="stat-label">Tổng vi phạm</div>
                <div class="stat-change" style="color: #ff6b6b;">
                    <i class="bi bi-clock-history"></i> {{ $pendingViolations }} chưa xử lý
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards Row 2 --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #8EDC6E20; color: #8EDC6E;">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-value">{{ number_format($upcomingEvents) }}</div>
                <div class="stat-label">Sắp diễn ra</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #0B3D9120; color: #0B3D91;">
                    <i class="bi bi-play-circle"></i>
                </div>
                <div class="stat-value">{{ number_format($ongoingEvents) }}</div>
                <div class="stat-label">Đang diễn ra</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #5FB84A20; color: #5FB84A;">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-value">{{ number_format($totalParticipants) }}</div>
                <div class="stat-label">Tổng lượt tham gia</div>
                <div class="stat-change positive">
                    Tỷ lệ: {{ $participationRate }}%
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric-card">
                <div class="metric-label">Trung bình người tham gia/hoạt động</div>
                <div class="metric-value">{{ $avgParticipantsPerEvent }}</div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row g-4">
        {{-- Biểu đồ hoạt động & đăng ký theo tháng --}}
        <div class="col-md-8">
            <div class="card">
                <div class="chart-title">
                    <span><i class="bi bi-graph-up"></i> Hoạt động & Tham gia theo tháng</span>
                </div>
                <div style="height: 350px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Biểu đồ tỷ lệ CLB --}}
        <div class="col-md-4">
            <div class="card">
                <div class="chart-title">
                    <span><i class="bi bi-pie-chart"></i> Tỷ lệ CLB</span>
                </div>
                <div style="height: 350px;">
                    <canvas id="clubRatioChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mt-2">
        {{-- Biểu đồ tăng trưởng thành viên --}}
        <div class="col-md-6">
            <div class="card">
                <div class="chart-title">
                    <span><i class="bi bi-people"></i> Tăng trưởng thành viên</span>
                </div>
                <div style="height: 300px;">
                    <canvas id="memberGrowthChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Biểu đồ trạng thái hoạt động --}}
        <div class="col-md-6">
            <div class="card">
                <div class="chart-title">
                    <span><i class="bi bi-bar-chart"></i> Trạng thái hoạt động</span>
                </div>
                <div style="height: 300px;">
                    <canvas id="eventStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Rankings Row --}}
    <div class="row g-4 mt-2">
        {{-- Top CLB theo thành viên --}}
        <div class="col-md-4">
            <div class="card">
                <div class="chart-title">
                    <span><i class="bi bi-trophy"></i> Top 5 CLB - Thành viên</span>
                </div>
                <div>
                    @foreach($topClubsByMembers as $index => $club)
                        <div class="ranking-card">
                            <div class="d-flex align-items-center">
                                <div class="ranking-number {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $club->name }}</div>
                                    <small class="text-muted">{{ $club->code }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: #0033A0;">{{ $club->member_count }}</div>
                                    <small class="text-muted">thành viên</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Top CLB theo hoạt động --}}
        <div class="col-md-4">
            <div class="card">
                <div class="chart-title">
                    <span><i class="bi bi-trophy"></i> Top 5 CLB - Hoạt động</span>
                </div>
                <div>
                    @foreach($topClubsByEvents as $index => $club)
                        <div class="ranking-card">
                            <div class="d-flex align-items-center">
                                <div class="ranking-number {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $club->name }}</div>
                                    <small class="text-muted">{{ $club->code }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: #0033A0;">{{ $club->event_count }}</div>
                                    <small class="text-muted">hoạt động</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Top hoạt động theo tham gia --}}
        <div class="col-md-4">
            <div class="card">
                <div class="chart-title">
                    <span><i class="bi bi-trophy"></i> Top 5 Hoạt động - Tham gia</span>
                </div>
                <div>
                    @foreach($topEventsByParticipants as $index => $event)
                        <div class="ranking-card">
                            <div class="d-flex align-items-center">
                                <div class="ranking-number {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold" style="font-size: 13px;">{{ Str::limit($event->title, 30) }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: #0033A0;">{{ $event->participant_count }}</div>
                                    <small class="text-muted">người</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Phân bố CLB theo lĩnh vực --}}
    <div class="row g-4 mt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="chart-title">
                    <span><i class="bi bi-diagram-3"></i> Phân bố CLB theo lĩnh vực</span>
                </div>
                <div style="height: 300px;">
                    <canvas id="clubsByFieldChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Biểu đồ hoạt động & đăng ký theo tháng
const monthlyCtx = document.getElementById('monthlyChart');
if (monthlyCtx) {
    const monthlyEvents = {!! json_encode($monthlyEvents) !!};
    const monthlyRegistrations = {!! json_encode($monthlyRegistrations) !!};
    const monthlyParticipants = {!! json_encode($monthlyParticipants) !!};
    
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyEvents.map(item => item.month),
            datasets: [{
                label: 'Hoạt động',
                data: monthlyEvents.map(item => item.count),
                borderColor: '#0033A0',
                backgroundColor: 'rgba(0, 51, 160, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y'
            }, {
                label: 'Đăng ký',
                data: monthlyRegistrations.map(item => item.count),
                borderColor: '#FFE600',
                backgroundColor: 'rgba(255, 230, 0, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }, {
                label: 'Tham gia',
                data: monthlyParticipants.map(item => item.count),
                borderColor: '#5FB84A',
                backgroundColor: 'rgba(95, 184, 74, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: { drawOnChartArea: false }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

// Biểu đồ tỷ lệ CLB
const clubRatioCtx = document.getElementById('clubRatioChart');
if (clubRatioCtx) {
    const clubStatusRatio = {!! json_encode($clubStatusRatio) !!};
    new Chart(clubRatioCtx, {
        type: 'doughnut',
        data: {
            labels: ['Đang hoạt động', 'Tạm dừng', 'Chờ duyệt'],
            datasets: [{
                data: [
                    clubStatusRatio.active || 0,
                    clubStatusRatio.archived || 0,
                    clubStatusRatio.pending || 0
                ],
                backgroundColor: ['#5FB84A', '#6b7280', '#FFE600'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 12, font: { size: 12 } }
                }
            }
        }
    });
}

// Biểu đồ tăng trưởng thành viên
const memberGrowthCtx = document.getElementById('memberGrowthChart');
if (memberGrowthCtx) {
    const monthlyMembers = {!! json_encode($monthlyMembers) !!};
    new Chart(memberGrowthCtx, {
        type: 'line',
        data: {
            labels: monthlyMembers.map(item => item.month),
            datasets: [{
                label: 'Tổng thành viên',
                data: monthlyMembers.map(item => item.count),
                borderColor: '#8EDC6E',
                backgroundColor: 'rgba(142, 220, 110, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#5FB84A',
                pointBorderColor: '#5FB84A',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// Biểu đồ trạng thái hoạt động
const eventStatusCtx = document.getElementById('eventStatusChart');
if (eventStatusCtx) {
    const eventStatusRatio = {!! json_encode($eventStatusRatio) !!};
    new Chart(eventStatusCtx, {
        type: 'bar',
        data: {
            labels: ['Sắp diễn ra', 'Đang diễn ra', 'Đã kết thúc', 'Đã hủy'],
            datasets: [{
                label: 'Số lượng',
                data: [
                    eventStatusRatio.upcoming || 0,
                    eventStatusRatio.ongoing || 0,
                    eventStatusRatio.finished || 0,
                    eventStatusRatio.cancelled || 0
                ],
                backgroundColor: ['#8EDC6E', '#0B3D91', '#5FB84A', '#dc3545'],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// Biểu đồ phân bố CLB theo lĩnh vực
const clubsByFieldCtx = document.getElementById('clubsByFieldChart');
if (clubsByFieldCtx) {
    const clubsByField = {!! json_encode($clubsByField) !!};
    new Chart(clubsByFieldCtx, {
        type: 'pie',
        data: {
            labels: clubsByField.map(item => item.field || 'Khác'),
            datasets: [{
                data: clubsByField.map(item => item.count),
                backgroundColor: [
                    '#0033A0', '#0B3D91', '#5FB84A', '#8EDC6E', 
                    '#FFE600', '#FFF3A0', '#dc3545', '#9333ea'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { padding: 12, font: { size: 12 } }
                }
            }
        }
    });
}
</script>
@endpush
