<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thống kê CLB - Chủ nhiệm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #072C6A;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF7B0;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .sidebar {
            width: 240px;
            background: var(--primary-blue);
            color: var(--text-light);
            padding: 24px 16px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .logo {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 24px;
        }
        .nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .nav a {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.9);
            padding: 10px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, color 0.2s;
        }
        .nav a:hover {
            background: var(--primary-blue-hover);
            color: var(--text-light);
        }
        .nav a.active {
            background: var(--accent-yellow);
            color: var(--text-dark);
        }
        .logout-btn {
            margin-top: auto;
            background: #ef4444;
            color: #fff;
            border: none;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
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
            text-align: center;
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: 700;
            color: #0B3D91;
            margin: 10px 0;
        }
        .submenu {
            margin-left: 20px;
            margin-top: 4px;
        }
        .submenu a {
            font-size: 13px;
            padding: 8px 12px;
        }
        .stat-card .label {
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    @include('student.sidebar')

    <div class="content">
        <h3 class="fw-bold mb-4">Thống kê CLB</h3>

        {{-- THÔNG TIN CLB --}}
        <div class="alert alert-info mb-4">
            <strong>CLB:</strong> {{ $club->name }} ({{ $club->code }})
        </div>

        {{-- THỐNG KÊ TỔNG QUAN --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <i class="bi bi-people" style="font-size: 32px; color: #0B3D91;"></i>
                    <div class="number">{{ $totalMembers }}</div>
                    <div class="label">Tổng thành viên</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <i class="bi bi-exclamation-triangle" style="font-size: 32px; color: #dc3545;"></i>
                    <div class="number">{{ $totalViolations }}</div>
                    <div class="label">Số vi phạm</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <i class="bi bi-calendar-check" style="font-size: 32px; color: #28a745;"></i>
                    <div class="number">{{ $totalEvents }}</div>
                    <div class="label">Hoạt động đã tổ chức</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <i class="bi bi-star" style="font-size: 32px; color: #ffc107;"></i>
                    <div class="number">{{ $totalActivityPoints }}</div>
                    <div class="label">Tổng điểm hoạt động</div>
                </div>
            </div>
        </div>

        {{-- THÀNH VIÊN TÍCH CỰC NHẤT --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-trophy"></i> Thành viên tích cực nhất (Top 5)</h5>
            </div>
            <div class="card-body">
                @if ($topMembers->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Hạng</th>
                                    <th>Tên thành viên</th>
                                    <th>MSSV</th>
                                    <th>Số hoạt động tham gia</th>
                                    <th>Tổng điểm</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topMembers as $index => $member)
                                    <tr>
                                        <td>
                                            @if ($index == 0)
                                                <span class="badge bg-warning">🥇</span>
                                            @elseif ($index == 1)
                                                <span class="badge bg-secondary">🥈</span>
                                            @elseif ($index == 2)
                                                <span class="badge" style="background-color: #cd7f32; color: white;">🥉</span>
                                            @else
                                                <strong>#{{ $index + 1 }}</strong>
                                            @endif
                                        </td>
                                        <td><strong>{{ $member->name }}</strong></td>
                                        <td>{{ $member->student_code }}</td>
                                        <td>{{ $member->events_attended }}</td>
                                        <td><span class="badge bg-success">{{ $member->total_points }} điểm</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Chưa có dữ liệu</p>
                @endif
            </div>
        </div>

        {{-- BIỂU ĐỒ SỰ KIỆN THEO THÁNG --}}
        @if ($monthlyStats->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Hoạt động đã tổ chức (6 tháng gần nhất)</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @if ($monthlyStats->count() > 0)
    <script>
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = @json($monthlyStats);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Số hoạt động',
                    data: monthlyData.map(item => item.event_count),
                    backgroundColor: '#0B3D91',
                    borderColor: '#0a2d6d',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
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
    @endif
</body>
</html>

