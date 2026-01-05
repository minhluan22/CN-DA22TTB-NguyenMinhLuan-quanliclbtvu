<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thống kê tham gia - Chủ nhiệm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --primary-blue: #0B3D91;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF7B0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--soft-yellow);
        }
        .content {
            margin-left: 260px;
            padding: 24px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-blue);
        }
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
    @include('student.sidebar')

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">
                <i class="bi bi-bar-chart"></i> Thống kê tham gia
            </h3>
            <div class="badge bg-primary" style="font-size: 14px; padding: 10px 16px;">
                <i class="bi bi-building"></i> CLB: {{ $club->name }} ({{ $club->code }})
            </div>
        </div>

        <!-- Bộ lọc thời gian -->
        <div class="stat-card mb-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-funnel"></i> Bộ lọc thống kê
            </h5>
            <form method="GET" action="{{ route('student.chairman.participation-statistics') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Từ ngày</label>
                    <input type="date" name="start_date" class="form-control" 
                           value="{{ $startDate ?? now()->subMonths(12)->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Đến ngày</label>
                    <input type="date" name="end_date" class="form-control" 
                           value="{{ $endDate ?? now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Lọc dữ liệu
                    </button>
                </div>
            </form>
        </div>

        <!-- Tổng quan -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted mb-2">Tổng số hoạt động</div>
                            <div class="stat-number">{{ $totalEvents }}</div>
                            <small class="text-muted">Trong khoảng thời gian đã chọn</small>
                        </div>
                        <i class="bi bi-calendar-event" style="font-size: 48px; color: var(--primary-blue); opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted mb-2">Tổng lượt tham gia</div>
                            <div class="stat-number">{{ $totalParticipations }}</div>
                            <small class="text-muted">Đã duyệt và tham gia</small>
                        </div>
                        <i class="bi bi-people" style="font-size: 48px; color: var(--primary-blue); opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted mb-2">Tổng số sinh viên tham gia</div>
                            <div class="stat-number">{{ $totalUniqueParticipants }}</div>
                            <small class="text-muted">Sinh viên không trùng</small>
                        </div>
                        <i class="bi bi-person-check" style="font-size: 48px; color: var(--primary-blue); opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ: Hoạt động theo tháng -->
        <div class="chart-container">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-graph-up"></i> Hoạt động theo tháng
            </h5>
            <div class="chart-wrapper">
                <canvas id="monthlyEventsChart"></canvas>
            </div>
        </div>

        <!-- Biểu đồ: Số lượng sinh viên tham gia -->
        <div class="chart-container">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-person-check"></i> Số lượng sinh viên tham gia theo tháng
            </h5>
            <div class="chart-wrapper">
                <canvas id="monthlyParticipantsChart"></canvas>
            </div>
        </div>

        <!-- Top hoạt động -->
        <div class="stat-card">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-trophy"></i> Top hoạt động có nhiều người tham gia
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Tên hoạt động</th>
                            <th>Ngày diễn ra</th>
                            <th>Số người tham gia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topEvents as $index => $event)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $event->title }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $event->participant_count }} người</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Chưa có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Biểu đồ hoạt động theo tháng
        const monthlyEventsCtx = document.getElementById('monthlyEventsChart').getContext('2d');
        new Chart(monthlyEventsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyEvents->pluck('month')) !!},
                datasets: [{
                    label: 'Số hoạt động',
                    data: {!! json_encode($monthlyEvents->pluck('event_count')) !!},
                    borderColor: '#0B3D91',
                    backgroundColor: 'rgba(11, 61, 145, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
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
                        }
                    }
                }
            }
        });

        // Biểu đồ số lượng sinh viên tham gia
        const monthlyParticipantsCtx = document.getElementById('monthlyParticipantsChart').getContext('2d');
        new Chart(monthlyParticipantsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyParticipants->pluck('month')) !!},
                datasets: [{
                    label: 'Số sinh viên',
                    data: {!! json_encode($monthlyParticipants->pluck('participant_count')) !!},
                    backgroundColor: '#FFE600',
                    borderColor: '#0B3D91',
                    borderWidth: 2
                }]
            },
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
                        }
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

