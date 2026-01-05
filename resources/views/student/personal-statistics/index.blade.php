<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thống kê cá nhân</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #072C6A;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF9D6;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
            --secondary: #2b2f3a;
            --card: #ffffff;
            --muted: #6b7280;
            --border: #e5e7eb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 0;
        }
        
        .body-wrapper {
            display: flex;
            flex: 1;
        }
        .sidebar {
            width: 240px;
            background: var(--primary-blue);
            color: var(--text-light);
            padding: 24px 16px;
            padding-top: 88px;
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 998;
            transition: transform 0.3s ease;
            box-sizing: border-box;
            margin: 0;
        }

        .sidebar-collapsed {
            transform: translateX(-100%);
        }

        .sidebar-toggle-fixed {
            position: fixed;
            top: 80px;
            left: 20px;
            z-index: 1001;
            background: var(--primary-blue);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: var(--text-light);
            width: 44px;
            height: 44px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .sidebar-toggle-fixed:hover {
            background: var(--primary-blue-hover);
            border-color: var(--accent-yellow);
            transform: scale(1.05);
        }

        body:not(.sidebar-closed) .sidebar-toggle-fixed {
            display: none;
        }

        body.sidebar-closed .sidebar-toggle-fixed {
            display: flex;
        }

        body.sidebar-closed .content {
            margin-left: 0;
            width: 100%;
        }

        .sidebar-overlay {
            display: none;
        }

        .content {
            margin-left: 240px;
            padding: 24px;
            margin-top: 64px;
            min-height: 100vh;
            width: calc(100% - 240px);
            max-width: 100%;
            box-sizing: border-box;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        .header {
            background: var(--card);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            box-sizing: border-box;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: var(--text-dark);
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            box-sizing: border-box;
        }
        .card:last-child {
            margin-bottom: 0;
        }
        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            color: white;
            margin-bottom: 24px;
            width: 100%;
            box-sizing: border-box;
        }
        .avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 700;
            flex-shrink: 0;
            border: 4px solid white;
        }
        .avatar-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .profile-info h2 {
            margin: 0 0 8px 0;
            font-size: 24px;
        }
        .profile-info .meta {
            opacity: 0.9;
            font-size: 14px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .stat-card {
            text-align: center;
            padding: 20px;
            background: var(--card);
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .stat-card .label {
            font-size: 14px;
            color: var(--muted);
        }
        .tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border);
            width: 100%;
            box-sizing: border-box;
        }
        .tab {
            padding: 12px 20px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            font-weight: 600;
            color: var(--muted);
            transition: all 0.2s;
        }
        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        .tab-content {
            display: none;
            width: 100%;
            box-sizing: border-box;
        }
        .tab-content.active {
            display: block;
            width: 100%;
            box-sizing: border-box;
        }
        .table-role {
            width: 100%;
            border-collapse: collapse;
        }
        .table-role thead {
            background: #eaf2ff;
            color: #0B3D91;
        }
        .table-role thead th {
            padding: 12px;
            font-weight: 700;
            text-align: left;
        }
        .table-role tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.2s;
        }
        .table-role tbody tr:hover {
            background: #f8fafc;
        }
        .table-role tbody td {
            padding: 12px;
        }
        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #FFF3A0; color: #B84A5F; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-secondary { background: #e5e7eb; color: #374151; }
        .badge-primary { background: #dbeafe; color: #1e40af; }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 16px;
            display: block;
        }
        .total-points-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 24px;
        }
        .total-points-card .value {
            font-size: 64px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .total-points-card .label {
            font-size: 18px;
            opacity: 0.9;
        }
        .total-violations-card {
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            color: white;
            text-align: center;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 24px;
        }
        .total-violations-card .value {
            font-size: 64px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .total-violations-card .label {
            font-size: 18px;
            opacity: 0.9;
        }
        .chart-container {
            height: 300px;
            position: relative;
        }
        .no-violations {
            text-align: center;
            padding: 60px 20px;
            background: #dcfce7;
            border-radius: 16px;
            color: #166534;
        }
        .no-violations i {
            font-size: 64px;
            margin-bottom: 16px;
            display: block;
        }
        @media (max-width: 900px) {
            .sidebar-toggle-fixed {
                top: 16px;
                left: 16px;
                width: 40px;
                height: 40px;
                font-size: 20px;
            }

            .sidebar { 
                top: 56px;
                height: calc(100vh - 56px);
                width: 280px;
            }
            .content { 
                margin-left: 0;
                padding: 16px;
                width: 100%;
            }
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            body.sidebar-open .sidebar-overlay {
                display: block;
            }

            body.sidebar-closed .student-footer {
                margin-left: 0;
                width: 100%;
            }

            body:not(.sidebar-closed) .student-footer {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @include('student.header')
    
    <!-- Nút hamburger cố định để mở sidebar khi đóng -->
    <button class="sidebar-toggle-fixed" onclick="toggleSidebar()" title="Mở menu">
        ☰
    </button>
    
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <div class="body-wrapper">
        @include('student.sidebar')

    <main class="content">
        <div class="header">
            <h1>📊 Thống Kê - Cá Nhân</h1>
        </div>

        <div class="tabs">
            <button class="tab {{ request('tab') == 'activities' || !request('tab') ? 'active' : '' }}" onclick="showTab('activities')">🎯 Hoạt động đã tham gia</button>
            <button class="tab {{ request('tab') == 'points' ? 'active' : '' }}" onclick="showTab('points')">⭐ Điểm hoạt động cá nhân</button>
            <button class="tab {{ request('tab') == 'club-history' ? 'active' : '' }}" onclick="showTab('club-history')">📚 Lịch sử tham gia CLB</button>
            <button class="tab {{ request('tab') == 'violations' ? 'active' : '' }}" onclick="showTab('violations')">⚠️ Lịch sử vi phạm</button>
        </div>

        <!-- Tab: Hoạt động đã tham gia -->
        <div id="tab-activities" class="tab-content {{ request('tab') == 'activities' || !request('tab') ? 'active' : '' }}">
            {{-- SUMMARY CARDS --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="value">{{ $totalRegistered ?? 0 }}</div>
                    <div class="label">Tổng đã đăng ký</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $attended ?? 0 }}</div>
                    <div class="label">Đã tham gia</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $absent ?? 0 }}</div>
                    <div class="label">Đăng ký nhưng không tham gia</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $cancelled ?? 0 }}</div>
                    <div class="label">Bị hủy</div>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="card">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="tab" value="activities">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Tìm kiếm</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoạt động..." class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">CLB</label>
                        <select name="club_id" class="form-control">
                            <option value="">-- Tất cả --</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->code }} - {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="all">-- Tất cả --</option>
                            <option value="attended" {{ request('status') == 'attended' ? 'selected' : '' }}>Đã tham gia</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Từ ngày</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Đến ngày</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Tìm
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="card">
                <h5 class="mb-3">Danh sách hoạt động đã tham gia</h5>
                <table class="table-role">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên hoạt động</th>
                            <th>CLB tổ chức</th>
                            <th>Thời gian</th>
                            <th>Địa điểm</th>
                            <th>Trạng thái tham gia</th>
                            <th>Điểm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $index => $activity)
                            <tr>
                                <td>{{ ($activities->currentPage() - 1) * $activities->perPage() + $index + 1 }}</td>
                                <td><strong>{{ $activity->title }}</strong></td>
                                <td>{{ $activity->club_name }} ({{ $activity->club_code }})</td>
                                <td>
                                    @if($activity->start_at)
                                        {{ \Carbon\Carbon::parse($activity->start_at)->format('d/m/Y H:i') }}
                                        @if($activity->end_at)
                                            <br><small>→ {{ \Carbon\Carbon::parse($activity->end_at)->format('d/m/Y H:i') }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $activity->location ?? 'Chưa cập nhật' }}</td>
                                <td>
                                    @if($activity->registration_status == 'attended')
                                        <span class="badge badge-success">Đã tham gia</span>
                                    @elseif($activity->registration_status == 'approved')
                                        <span class="badge badge-info">Đã duyệt</span>
                                    @elseif($activity->registration_status == 'pending')
                                        <span class="badge badge-warning">Chờ duyệt</span>
                                    @elseif($activity->registration_status == 'rejected')
                                        <span class="badge badge-danger">Từ chối</span>
                                    @elseif($activity->event_status == 'cancelled')
                                        <span class="badge badge-secondary">Bị hủy</span>
                                    @else
                                        <span class="badge badge-secondary">Đăng ký nhưng không tham gia</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activity->activity_points > 0)
                                        <span class="badge badge-success">{{ $activity->activity_points }} điểm</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>Chưa có hoạt động nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-4">
                    {{ $activities->links() }}
                </div>
            </div>
        </div>

        <!-- Tab: Điểm hoạt động cá nhân -->
        <div id="tab-points" class="tab-content {{ request('tab') == 'points' ? 'active' : '' }}">
            {{-- TOTAL POINTS CARD --}}
            <div class="total-points-card">
                <div class="value">{{ number_format($totalPoints ?? 0) }}</div>
                <div class="label">Tổng điểm hoạt động tích lũy</div>
            </div>

            {{-- CHART --}}
            @if(isset($pointsByYear) && count($pointsByYear) > 0)
                <div class="card">
                    <h5 class="mb-3">Điểm hoạt động theo năm học</h5>
                    <div class="chart-container">
                        <canvas id="pointsByYearChart"></canvas>
                    </div>
                </div>
            @endif

            {{-- FILTER --}}
            <div class="card">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="tab" value="points">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Năm học</label>
                        <select name="year" class="form-control">
                            <option value="">-- Tất cả --</option>
                            @for($year = date('Y'); $year >= date('Y') - 3; $year--)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    Năm học {{ $year }}-{{ $year + 1 }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">CLB</label>
                        <select name="club_id" class="form-control">
                            <option value="">-- Tất cả --</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->code }} - {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Tìm
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="card">
                <h5 class="mb-3">Chi tiết điểm từng hoạt động</h5>
                <table class="table-role">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên hoạt động</th>
                            <th>CLB</th>
                            <th>Thời gian</th>
                            <th>Điểm</th>
                            <th>Ngày ghi nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pointsDetail as $index => $point)
                            <tr>
                                <td>{{ ($pointsDetail->currentPage() - 1) * $pointsDetail->perPage() + $index + 1 }}</td>
                                <td><strong>{{ $point->title }}</strong></td>
                                <td>{{ $point->club_name }} ({{ $point->club_code }})</td>
                                <td>
                                    @if($point->start_at)
                                        {{ \Carbon\Carbon::parse($point->start_at)->format('d/m/Y H:i') }}
                                        @if($point->end_at)
                                            <br><small>→ {{ \Carbon\Carbon::parse($point->end_at)->format('d/m/Y H:i') }}</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-success">{{ $point->activity_points }} điểm</span>
                                </td>
                                <td>
                                    @if($point->point_date)
                                        {{ \Carbon\Carbon::parse($point->point_date)->format('d/m/Y H:i') }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>Chưa có điểm hoạt động nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-4">
                    {{ $pointsDetail->links() }}
                </div>
            </div>
        </div>

        <!-- Tab: Lịch sử tham gia CLB -->
        <div id="tab-club-history" class="tab-content {{ request('tab') == 'club-history' ? 'active' : '' }}">
            {{-- SUMMARY CARDS --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="value">{{ $totalClubs ?? 0 }}</div>
                    <div class="label">Tổng CLB đã tham gia</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $activeClubs ?? 0 }}</div>
                    <div class="label">Đang tham gia</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $leftClubs ?? 0 }}</div>
                    <div class="label">Đã rời CLB</div>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="card">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="tab" value="club-history">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tìm kiếm</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoặc mã CLB..." class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="all">-- Tất cả --</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang tham gia</option>
                            <option value="left" {{ request('status') == 'left' ? 'selected' : '' }}>Đã rời CLB</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Tìm
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="card">
                <h5 class="mb-3">Danh sách CLB đã và đang tham gia</h5>
                <table class="table-role">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên CLB</th>
                            <th>Lĩnh vực</th>
                            <th>Vai trò</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clubHistory as $index => $club)
                            <tr>
                                <td>{{ ($clubHistory->currentPage() - 1) * $clubHistory->perPage() + $index + 1 }}</td>
                                <td><strong>{{ $club->club_name }}</strong><br><small>{{ $club->club_code }}</small></td>
                                <td>{{ $club->field_display ?? 'Chưa xác định' }}</td>
                                <td>
                                    @if($club->position == 'chairman')
                                        <span class="badge badge-primary">Chủ nhiệm</span>
                                    @elseif($club->position == 'vice_chairman')
                                        <span class="badge badge-warning">Phó Chủ nhiệm</span>
                                    @elseif($club->position == 'secretary')
                                        <span class="badge badge-info">Thư ký</span>
                                    @elseif($club->position == 'head_expertise')
                                        <span class="badge badge-info">Trưởng ban Chuyên môn</span>
                                    @elseif($club->position == 'head_media')
                                        <span class="badge badge-info">Trưởng ban Truyền thông</span>
                                    @elseif($club->position == 'head_events')
                                        <span class="badge badge-info">Trưởng ban Sự kiện</span>
                                    @else
                                        <span class="badge badge-secondary">Thành viên</span>
                                    @endif
                                </td>
                                <td>
                                    @if($club->joined_at)
                                        {{ \Carbon\Carbon::parse($club->joined_at)->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if($club->left_at)
                                        {{ \Carbon\Carbon::parse($club->left_at)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($club->status == 'approved')
                                        <span class="badge badge-success">Đang tham gia</span>
                                    @elseif($club->status == 'left')
                                        <span class="badge badge-secondary">Đã rời CLB</span>
                                    @else
                                        <span class="badge badge-warning">{{ ucfirst($club->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>Chưa có lịch sử tham gia CLB nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-4">
                    {{ $clubHistory->links() }}
                </div>
            </div>
        </div>

        <!-- Tab: Lịch sử vi phạm -->
        <div id="tab-violations" class="tab-content {{ request('tab') == 'violations' ? 'active' : '' }}">
            @if($totalViolations > 0)
                {{-- TOTAL VIOLATIONS CARD --}}
                <div class="total-violations-card">
                    <div class="value">{{ $totalViolations }}</div>
                    <div class="label">Tổng số vi phạm</div>
                </div>

                {{-- VIOLATIONS BY SEVERITY --}}
                @if(isset($violationsBySeverity) && $violationsBySeverity->count() > 0)
                    <div class="stats-grid">
                        @foreach($violationsBySeverity as $severity => $count)
                            <div class="stat-card">
                                <div class="value">{{ $count }}</div>
                                <div class="label">
                                    @if($severity == 'light') Nhẹ
                                    @elseif($severity == 'medium') Trung bình
                                    @elseif($severity == 'serious') Nghiêm trọng
                                    @else {{ $severity }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- FILTER --}}
                <div class="card">
                    <form method="GET" class="row g-3">
                        <input type="hidden" name="tab" value="violations">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Mức độ</label>
                            <select name="severity" class="form-control">
                                <option value="all">-- Tất cả --</option>
                                <option value="light" {{ request('severity') == 'light' ? 'selected' : '' }}>Nhẹ</option>
                                <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="serious" {{ request('severity') == 'serious' ? 'selected' : '' }}>Nghiêm trọng</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">CLB</label>
                            <select name="club_id" class="form-control">
                                <option value="">-- Tất cả --</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                        {{ $club->code }} - {{ $club->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Từ ngày</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Đến ngày</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                        </div>
                    </form>
                </div>

                {{-- TABLE --}}
                <div class="card">
                    <h5 class="mb-3">Danh sách vi phạm của bản thân</h5>
                    <table class="table-role">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên hoạt động</th>
                                <th>CLB</th>
                                <th>Loại vi phạm</th>
                                <th>Mức độ</th>
                                <th>Trạng thái</th>
                                <th>Ngày phát hiện</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($violations as $index => $violation)
                                <tr>
                                    <td>{{ ($violations->currentPage() - 1) * $violations->perPage() + $index + 1 }}</td>
                                    <td><strong>{{ $violation->title }}</strong></td>
                                    <td>{{ $violation->club_name }} ({{ $violation->club_code }})</td>
                                    <td>{{ $violation->violation_type ?? 'Chưa xác định' }}</td>
                                    <td>
                                        @if($violation->violation_severity == 'light')
                                            <span class="badge badge-warning">Nhẹ</span>
                                        @elseif($violation->violation_severity == 'medium')
                                            <span class="badge badge-info">Trung bình</span>
                                        @elseif($violation->violation_severity == 'serious')
                                            <span class="badge badge-danger">Nghiêm trọng</span>
                                        @else
                                            <span class="badge badge-secondary">Chưa xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($violation->violation_status == 'pending')
                                            <span class="badge badge-warning">Chờ xử lý</span>
                                        @elseif($violation->violation_status == 'processing')
                                            <span class="badge badge-info">Đang xử lý</span>
                                        @elseif($violation->violation_status == 'processed')
                                            <span class="badge badge-success">Đã xử lý</span>
                                        @else
                                            <span class="badge badge-secondary">Chưa xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($violation->violation_detected_at)
                                            {{ \Carbon\Carbon::parse($violation->violation_detected_at)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($violation->violation_notes)
                                            <small>{{ Str::limit($violation->violation_notes, 50) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>Không có vi phạm nào trong khoảng thời gian này</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $violations->links() }}
                    </div>
                </div>
            @else
                {{-- NO VIOLATIONS --}}
                <div class="no-violations">
                    <i class="bi bi-check-circle"></i>
                    <h3>Chúc mừng!</h3>
                    <p>Bạn chưa có vi phạm nào. Hãy tiếp tục duy trì tinh thần tham gia tích cực và tuân thủ nội quy CLB.</p>
                </div>
            @endif
        </div>
    </main>
    </div>

    @include('student.footer')

    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-closed');
            document.body.classList.toggle('sidebar-open');
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.toggle('sidebar-collapsed');
            }
        }

        function closeSidebarOnClick() {
            if (window.innerWidth <= 900) {
                document.body.classList.remove('sidebar-open');
                document.body.classList.add('sidebar-closed');
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.add('sidebar-collapsed');
                }
            }
        }

        function showTab(tabName) {
            // Ẩn tất cả tab content
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            // Bỏ active từ tất cả tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Hiển thị tab được chọn
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');
        }

        // Tự động mở tab từ URL parameter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabFromUrl = urlParams.get('tab');
            
            if (tabFromUrl) {
                // Ẩn tất cả tab content
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                // Bỏ active từ tất cả tabs
                document.querySelectorAll('.tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Hiển thị tab được chọn
                const targetTab = document.getElementById('tab-' + tabFromUrl);
                if (targetTab) {
                    targetTab.classList.add('active');
                }
                
                // Cập nhật active tab button
                document.querySelectorAll('.tab').forEach(tab => {
                    if (tab.getAttribute('onclick') === `showTab('${tabFromUrl}')`) {
                        tab.classList.add('active');
                    }
                });
            }

            // Chart cho điểm hoạt động
            @if(isset($pointsByYear) && count($pointsByYear) > 0)
            const ctx = document.getElementById('pointsByYearChart');
            if (ctx) {
                const data = {!! json_encode($pointsByYear) !!};
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(data).map(year => 'Năm ' + year),
                        datasets: [{
                            label: 'Điểm hoạt động',
                            data: Object.values(data),
                            backgroundColor: '#0B3D91',
                            borderColor: '#072C6A',
                            borderWidth: 1
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
                                    stepSize: 10
                                }
                            }
                        }
                    }
                });
            }
            @endif

            const overlay = document.querySelector('.sidebar-overlay');
            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
