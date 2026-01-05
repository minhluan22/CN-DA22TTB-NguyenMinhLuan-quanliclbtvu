@extends('layouts.chairman')

@section('title', 'Tổng quan CLB - Chủ nhiệm CLB')

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
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        text-align: center;
        border-left: 4px solid transparent;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .stat-card.primary { border-left-color: #0033A0; }
    .stat-card.success { border-left-color: #5FB84A; }
    .stat-card.info { border-left-color: #0B3D91; }
    .stat-card.warning { border-left-color: #FFE600; }
    
    .stat-icon {
        font-size: 40px;
        margin-bottom: 12px;
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
    
    .charts-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f1f1f;
        margin-bottom: 20px;
    }
    
    .chart-container {
        height: 300px;
        position: relative;
    }
    
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .summary-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .summary-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .summary-value {
        font-size: 28px;
        font-weight: 700;
        color: #1f1f1f;
        margin-bottom: 4px;
    }
    
    .summary-label {
        font-size: 14px;
        color: #6b7280;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-bar-chart"></i>
            Tổng quan CLB
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

    {{-- FILTER --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('student.chairman.statistics') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Năm học:</label>
                    <select name="academic_year" class="form-control" onchange="this.form.submit()">
                        @for($year = now()->year; $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                                {{ $year }} - {{ $year + 1 }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI CARDS --}}
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon">👥</div>
            <div class="stat-value">{{ number_format($totalMembers) }}</div>
            <div class="stat-label">Tổng số thành viên CLB</div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon">📅</div>
            <div class="stat-value">{{ number_format($totalEvents) }}</div>
            <div class="stat-label">Tổng số hoạt động đã tổ chức</div>
        </div>
        <div class="stat-card info">
            <div class="stat-icon">📊</div>
            <div class="stat-value">{{ number_format($semester1Events) }}</div>
            <div class="stat-label">Học kỳ 1 ({{ $academicYear }})</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon">📈</div>
            <div class="stat-value">{{ number_format($semester2Events) }}</div>
            <div class="stat-label">Học kỳ 2 ({{ $academicYear + 1 }})</div>
        </div>
    </div>

    {{-- CHARTS ROW --}}
    <div class="charts-row">
        {{-- Biểu đồ cột: Số hoạt động theo thời gian --}}
        <div class="chart-card">
            <div class="chart-title">📈 Số hoạt động theo thời gian (6 tháng gần nhất)</div>
            <div class="chart-container">
                <canvas id="monthlyEventsChart"></canvas>
            </div>
        </div>

        {{-- Biểu đồ tròn: Tỷ lệ hoạt động đã tổ chức / bị hủy --}}
        <div class="chart-card">
            <div class="chart-title">🎯 Tỷ lệ hoạt động</div>
            <div class="chart-container">
                <canvas id="eventStatusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- THỐNG KÊ CHI TIẾT --}}
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">✅</div>
            <div class="summary-content">
                <div class="summary-value">{{ number_format($finishedEvents) }}</div>
                <div class="summary-label">Hoạt động đã tổ chức ({{ $finishedRatio }}%)</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">❌</div>
            <div class="summary-content">
                <div class="summary-value">{{ number_format($cancelledEvents) }}</div>
                <div class="summary-label">Hoạt động bị hủy ({{ $cancelledRatio }}%)</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon" style="background: rgba(11, 61, 145, 0.1); color: #0B3D91;">📚</div>
            <div class="summary-content">
                <div class="summary-value">{{ number_format($yearEvents) }}</div>
                <div class="summary-label">Hoạt động trong năm học</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Biểu đồ số hoạt động theo thời gian
const monthlyCtx = document.getElementById('monthlyEventsChart');
if (monthlyCtx) {
    const monthlyData = {!! json_encode($monthlyEvents) !!};
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Số hoạt động',
                data: monthlyData.map(item => item.count),
                backgroundColor: '#0B3D91',
                borderColor: '#0033A0',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Biểu đồ tròn: Tỷ lệ hoạt động
const statusCtx = document.getElementById('eventStatusChart');
if (statusCtx) {
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Đã tổ chức', 'Bị hủy'],
            datasets: [{
                data: [{{ $finishedEvents }}, {{ $cancelledEvents }}],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
                }
            }
        }
    });
}
</script>
@endpush
