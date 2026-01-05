@extends('layouts.chairman')

@section('title', 'Dashboard - Chủ nhiệm CLB')

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
    
    /* Stats Overview Cards */
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }
    
    .stat-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    
    .stat-card.blue::before { background: #0033A0; }
    .stat-card.yellow::before { background: #FFE600; }
    .stat-card.green::before { background: #5FB84A; }
    .stat-card.red::before { background: #dc3545; }
    
    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }
    
    .stat-icon.blue { background: #E6F0FF; color: #0033A0; }
    .stat-icon.yellow { background: #FFF9E6; color: #FFE600; }
    .stat-icon.green { background: #DCFCE7; color: #5FB84A; }
    .stat-icon.red { background: #FEE2E2; color: #dc3545; }
    
    .stat-content {
        margin-left: 8px;
    }
    
    .stat-value {
        font-size: 36px;
        font-weight: 800;
        color: #1f2937;
        line-height: 1;
        margin-bottom: 8px;
    }
    
    .stat-label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 600;
    }
    
    /* Charts Section */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .chart-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .chart-card.full-width {
        grid-column: 1 / -1;
    }
    
    .chart-header {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f3f4f6;
    }
    
    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .chart-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .chart-container.tall {
        height: 400px;
    }
    
    /* Tables Section */
    .tables-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .table-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .table-card.full-width {
        grid-column: 1 / -1;
    }
    
    .table-header {
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table-title {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .table-action {
        font-size: 13px;
        color: #0033A0;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }
    
    .table-action:hover {
        color: #0B3D91;
        text-decoration: underline;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table thead {
        background: #f9fafb;
    }
    
    .data-table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
        color: #374151;
    }
    
    .data-table tbody tr:hover {
        background: #f9fafb;
    }
    
    .data-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Badges */
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .badge.success {
        background: #DCFCE7;
        color: #166534;
    }
    
    .badge.warning {
        background: #FFF9E6;
        color: #B45309;
    }
    
    .badge.danger {
        background: #FEE2E2;
        color: #991B1B;
    }
    
    .badge.info {
        background: #E6F0FF;
        color: #1E40AF;
    }
    
    /* Button Styles */
    .btn-sm {
        padding: 6px 14px;
        font-size: 13px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background: #0033A0;
        color: white;
    }
    
    .btn-primary:hover {
        background: #0B3D91;
        transform: translateY(-1px);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #9ca3af;
    }
    
    .empty-state i {
        font-size: 56px;
        color: #d1d5db;
        margin-bottom: 16px;
        display: block;
    }
    
    .empty-state p {
        font-size: 15px;
        margin: 0;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .charts-grid,
        .tables-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 16px;
        }
        
        .stats-overview {
            grid-template-columns: 1fr;
        }
        
        .page-title {
            font-size: 22px;
        }
        
        .stat-value {
            font-size: 28px;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-speedometer2"></i>
            Dashboard Chủ nhiệm CLB
        </h1>
    </div>

    <!-- Club Info Card -->
    <div class="club-info-card">
        <div class="club-info-grid">
            <div class="club-info-item">
                <span class="club-info-label">Tên Câu lạc bộ</span>
                <span class="club-info-value">{{ $club->name }}</span>
            </div>
            <div class="club-info-item">
                <span class="club-info-label">Mã CLB</span>
                <span class="club-info-value">{{ $club->code }}</span>
            </div>
            <div class="club-info-item">
                <span class="club-info-label">Trạng thái</span>
                <span class="club-info-value">
                    @if($club->status === 'active')
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

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card blue">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['total_members'] }}</div>
                <div class="stat-label">Tổng thành viên</div>
            </div>
        </div>

        <div class="stat-card yellow">
            <div class="stat-header">
                <div class="stat-icon yellow">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['pending_registrations'] }}</div>
                <div class="stat-label">Đơn đăng ký chờ duyệt</div>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <div class="stat-icon green">
                    <i class="bi bi-calendar-event"></i>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['upcoming_events'] }}</div>
                <div class="stat-label">Hoạt động sắp diễn ra</div>
            </div>
        </div>

        <div class="stat-card red">
            <div class="stat-header">
                <div class="stat-icon red">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $stats['pending_violations'] }}</div>
                <div class="stat-label">Vi phạm chưa xử lý</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Thành viên theo chức vụ -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="bi bi-pie-chart-fill" style="color: #0033A0;"></i>
                    Thành viên theo chức vụ
                </h3>
                <p class="chart-subtitle">Phân bố chức vụ trong CLB</p>
            </div>
            <div class="chart-container">
                <canvas id="positionChart"></canvas>
            </div>
        </div>

        <!-- Thành viên theo giới tính -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="bi bi-gender-ambiguous" style="color: #5FB84A;"></i>
                    Thành viên theo giới tính
                </h3>
                <p class="chart-subtitle">Tỷ lệ nam/nữ trong CLB</p>
            </div>
            <div class="chart-container">
                <canvas id="genderChart"></canvas>
            </div>
        </div>

        <!-- Hoạt động theo trạng thái -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="bi bi-bar-chart-fill" style="color: #FFE600;"></i>
                    Hoạt động theo trạng thái
                </h3>
                <p class="chart-subtitle">Tổng quan trạng thái hoạt động</p>
            </div>
            <div class="chart-container">
                <canvas id="eventStatusChart"></canvas>
            </div>
        </div>

        <!-- Hoạt động theo thời gian -->
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="bi bi-graph-up" style="color: #dc3545;"></i>
                    Hoạt động 6 tháng gần nhất
                </h3>
                <p class="chart-subtitle">Xu hướng tổ chức hoạt động</p>
            </div>
            <div class="chart-container">
                <canvas id="eventTimelineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="tables-grid">
        <!-- Đơn đăng ký mới -->
        <div class="table-card">
            <div class="table-header">
                <h3 class="table-title">
                    <i class="bi bi-file-earmark-check"></i>
                    Đơn đăng ký mới
                </h3>
                <a href="{{ route('student.chairman.manage-registrations') }}" class="table-action">
                    Xem tất cả <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if($newRegistrations->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sinh viên</th>
                            <th>MSSV</th>
                            <th>Ngày đăng ký</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($newRegistrations as $reg)
                            <tr>
                                <td>
                                    <strong>{{ $reg->name }}</strong>
                                </td>
                                <td>{{ $reg->student_code }}</td>
                                <td>{{ \Carbon\Carbon::parse($reg->created_at)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('student.chairman.manage-registrations') }}" class="btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Không có đơn đăng ký mới</p>
                </div>
            @endif
        </div>

        <!-- Hoạt động sắp diễn ra -->
        <div class="table-card">
            <div class="table-header">
                <h3 class="table-title">
                    <i class="bi bi-calendar-event"></i>
                    Hoạt động sắp diễn ra
                </h3>
                <a href="{{ route('student.chairman.approved-events') }}" class="table-action">
                    Xem tất cả <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            @if($upcomingEvents->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tên hoạt động</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingEvents as $event)
                            <tr>
                                <td>
                                    <strong>{{ $event->title }}</strong>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($event->status === 'ongoing')
                                        <span class="badge info">Đang diễn ra</span>
                                    @else
                                        <span class="badge success">Sắp diễn ra</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p>Không có hoạt động sắp diễn ra</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart colors
    const colors = {
        blue: '#0033A0',
        yellow: '#FFE600',
        green: '#5FB84A',
        red: '#dc3545',
        purple: '#7c3aed',
        orange: '#f97316',
        teal: '#0d9488',
        pink: '#ec4899'
    };
    
    // Chart.js default config
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.font.size = 13;
    Chart.defaults.color = '#6b7280';
    
    // 1. Thành viên theo chức vụ (Pie Chart)
    const positionData = @json($membersByPosition);
    const positionLabels = {
        'chairman': 'Chủ nhiệm',
        'vice_chairman': 'Phó chủ nhiệm',
        'secretary': 'Thư ký',
        'treasurer': 'Thủ quỹ',
        'head_expertise': 'Trưởng ban chuyên môn',
        'head_media': 'Trưởng ban truyền thông',
        'head_events': 'Trưởng ban sự kiện',
        'member': 'Thành viên'
    };
    
    const positionChartData = {
        labels: positionData.map(item => positionLabels[item.position] || item.position),
        datasets: [{
            data: positionData.map(item => item.count),
            backgroundColor: [
                colors.blue,
                colors.yellow,
                colors.green,
                colors.red,
                colors.purple,
                colors.orange,
                colors.teal,
                colors.pink
            ],
            borderWidth: 0
        }]
    };
    
    new Chart(document.getElementById('positionChart'), {
        type: 'doughnut',
        data: positionChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true
                    }
                }
            }
        }
    });
    
    // 2. Thành viên theo giới tính (Pie Chart)
    const genderData = @json($membersByGender);
    const genderLabels = {
        'male': 'Nam',
        'female': 'Nữ',
        'other': 'Khác'
    };
    
    const genderChartData = {
        labels: genderData.map(item => genderLabels[item.gender] || 'Khác'),
        datasets: [{
            data: genderData.map(item => item.count),
            backgroundColor: [colors.blue, colors.pink, colors.teal],
            borderWidth: 0
        }]
    };
    
    new Chart(document.getElementById('genderChart'), {
        type: 'pie',
        data: genderChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true
                    }
                }
            }
        }
    });
    
    // 3. Hoạt động theo trạng thái (Bar Chart)
    const eventStatusData = @json($eventsByStatus);
    const statusLabels = {
        'upcoming': 'Sắp diễn ra',
        'ongoing': 'Đang diễn ra',
        'finished': 'Đã kết thúc',
        'cancelled': 'Đã hủy'
    };
    
    const eventStatusChartData = {
        labels: eventStatusData.map(item => statusLabels[item.status] || item.status),
        datasets: [{
            label: 'Số lượng',
            data: eventStatusData.map(item => item.count),
            backgroundColor: [colors.blue, colors.green, colors.yellow, colors.red],
            borderRadius: 8,
            borderWidth: 0
        }]
    };
    
    new Chart(document.getElementById('eventStatusChart'), {
        type: 'bar',
        data: eventStatusChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: '#f3f4f6'
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
    
    // 4. Hoạt động theo thời gian (Line Chart)
    const eventTimelineData = @json($eventsByMonth);
    
    const timelineChartData = {
        labels: eventTimelineData.map(item => {
            const [year, month] = item.month.split('-');
            return `Tháng ${month}/${year}`;
        }),
        datasets: [{
            label: 'Số hoạt động',
            data: eventTimelineData.map(item => item.count),
            borderColor: colors.blue,
            backgroundColor: colors.blue + '20',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: colors.blue,
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    };
    
    new Chart(document.getElementById('eventTimelineChart'), {
        type: 'line',
        data: timelineChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: '#f3f4f6'
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
});
</script>
@endpush
