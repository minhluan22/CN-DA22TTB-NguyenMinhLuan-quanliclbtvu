<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $club->name }} - Chi tiết CLB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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

        /* Nút hamburger để mở sidebar khi đóng */
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

        /* Ẩn nút hamburger khi sidebar mở */
        body:not(.sidebar-closed) .sidebar-toggle-fixed {
            display: none;
        }

        /* Hiển thị nút hamburger khi sidebar đóng */
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
        
        /* Đảm bảo nút X luôn có thể click được */
        .sidebar-toggle {
            z-index: 1001 !important;
            position: relative !important;
            pointer-events: auto !important;
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
            flex: 1;
            overflow-y: auto;
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
        }
        .content {
            margin-left: 240px;
            padding: 24px;
            padding-top: 88px;
            width: calc(100% - 240px);
            max-width: 100%;
            flex: 1;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        .header {
            background: var(--card);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .club-logo-large {
            width: 100px;
            height: 100px;
            border-radius: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }
        .club-logo-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }
        .header-info h1 {
            margin: 0;
            font-size: 24px;
            color: var(--text-dark);
        }
        .header-info .meta {
            color: var(--muted);
            margin-top: 8px;
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
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }
        .info-item {
            padding: 12px;
            background: var(--bg);
            border-radius: 8px;
        }
        .info-item .label {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 4px;
        }
        .info-item .value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
        }
        .member-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }
        .member-card {
            padding: 12px;
            background: var(--bg);
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .member-card .name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .member-card .role {
            font-size: 12px;
            color: var(--muted);
        }
        .event-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .event-item {
            padding: 16px;
            background: var(--bg);
            border-radius: 8px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .event-info h4 {
            margin: 0 0 8px 0;
            font-size: 16px;
        }
        .event-info .meta {
            font-size: 14px;
            color: var(--muted);
        }
        .event-cards-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .event-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .event-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        .event-tab-btn.active {
            color: var(--primary-blue) !important;
            border-bottom-color: var(--primary-blue) !important;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: #0a2d6d;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background: #B84A5F;
        }
        .btn-secondary {
            background: var(--muted);
            color: white;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px;
        }
        .badge.success {
            background: #dcfce7;
            color: #166534;
        }
        .badge.warning {
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .badge.info {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .badge.danger {
            background: #FFF3A0;
            color: #B84A5F;
        }
        .points-display {
            text-align: center;
            padding: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            color: white;
        }
        .points-display .total {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .points-display .level {
            font-size: 18px;
            opacity: 0.9;
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
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .table th {
            color: var(--muted);
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--muted);
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
        .filter-section {
            background: white;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .form-control-sm {
            font-size: 13px;
            padding: 6px 10px;
        }
        .form-label {
            font-size: 12px;
            margin-bottom: 4px;
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
            <div class="club-logo-large">
                @if($club->logo)
                    <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }}">
                @else
                    {{ strtoupper(substr($club->name ?? 'CLB', 0, 3)) }}
                @endif
            </div>
            <div class="header-info" style="flex: 1;">
                <h1>{{ $club->name }}</h1>
                <div class="meta">
                    Mã CLB: {{ $club->code }} | Lĩnh vực: {{ $club->field_display }}
                    @if($club->establishment_date)
                        | Ngày thành lập: {{ \Carbon\Carbon::parse($club->establishment_date)->format('d/m/Y') }}
                    @endif
                </div>
                <div style="margin-top: 12px; display: flex; align-items: center; gap: 8px;">
                    <a href="{{ route('student.my-clubs') }}" class="btn btn-secondary" style="text-decoration: none;">← Quay lại</a>
                    @if($isChairman)
                        <a href="{{ route('student.chairman.dashboard', ['club_id' => $club->id]) }}" class="btn btn-primary" style="text-decoration: none;">Quản lý CLB</a>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #FFF3A0; color: #B84A5F; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="tabs">
            <button class="tab active" onclick="showTab('info')">📋 Thông tin CLB</button>
            <button class="tab" onclick="showTab('members')">👥 Thành viên</button>
            <button class="tab" onclick="showTab('events')">🎉 Sự kiện</button>
            <button class="tab" onclick="showTab('proposals')">📝 Danh sách đề xuất</button>
            <button class="tab" onclick="showTab('points')">⭐ Điểm hoạt động</button>
        </div>

        <!-- Tab: Thông tin CLB -->
        <div id="tab-info" class="tab-content active">
            <div class="card">
                <div class="card-title">📋 Giới thiệu CLB</div>
                <div style="color: var(--muted); line-height: 1.6;">
                    {{ $club->description ?? 'Chưa có mô tả.' }}
                </div>
            </div>

            <div class="card">
                <div class="card-title">🎯 Lĩnh vực hoạt động</div>
                <div style="color: var(--muted); line-height: 1.6;">
                    {{ $club->field_display }}
                </div>
            </div>

            <div class="card">
                <div class="card-title">🎯 Mục tiêu hoạt động</div>
                <div style="color: var(--muted); line-height: 1.6;">
                    {{ $club->activity_goals ?? 'Chưa có thông tin mục tiêu hoạt động.' }}
                </div>
            </div>

            <div class="card">
                <div class="card-title">👑 Chủ nhiệm & Ban điều hành</div>
                <div class="info-grid">
                    @if($chairman)
                        <div class="info-item">
                            <div class="label">Chủ nhiệm</div>
                            <div class="value">{{ $chairman->name }}</div>
                            <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                                MSSV: {{ $chairman->student_code }} | Email: {{ $chairman->email }}
                            </div>
                        </div>
                    @endif
                    @if($executives->count() > 0)
                        @foreach($executives as $exec)
                            <div class="info-item">
                                <div class="label">
                                    @if($exec->position === 'vice_chairman') Phó chủ nhiệm
                                    @elseif($exec->position === 'secretary') Thư ký CLB
                                    @elseif($exec->position === 'head_expertise') Trưởng ban Chuyên môn
                                    @elseif($exec->position === 'head_media') Trưởng ban Truyền thông
                                    @elseif($exec->position === 'head_events') Trưởng ban Sự kiện
                                    @elseif($exec->position === 'treasurer') Trưởng ban Tài chính
                                    @endif
                                </div>
                                <div class="value">{{ $exec->name }}</div>
                                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                                    MSSV: {{ $exec->student_code }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-title">📞 Thông tin liên hệ</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Trạng thái CLB</div>
                        <div class="value">
                            @if($club->status === 'active')
                                <span class="badge success">Đang hoạt động</span>
                            @else
                                <span class="badge danger">Tạm dừng</span>
                            @endif
                        </div>
                    </div>
                    @if($club->email)
                        <div class="info-item">
                            <div class="label">Email CLB</div>
                            <div class="value">
                                <a href="mailto:{{ $club->email }}" style="color: var(--primary); text-decoration: none;">{{ $club->email }}</a>
                            </div>
                        </div>
                    @endif
                    @if($club->phone)
                        <div class="info-item">
                            <div class="label">Số điện thoại</div>
                            <div class="value">
                                <a href="tel:{{ $club->phone }}" style="color: var(--primary); text-decoration: none;">{{ $club->phone }}</a>
                            </div>
                        </div>
                    @endif
                    @if($club->fanpage)
                        <div class="info-item">
                            <div class="label">Fanpage</div>
                            <div class="value">
                                <a href="{{ $club->fanpage }}" target="_blank" style="color: var(--primary); text-decoration: none;">{{ $club->fanpage }}</a>
                            </div>
                        </div>
                    @endif
                    @if($club->meeting_place)
                        <div class="info-item">
                            <div class="label">Nơi sinh hoạt</div>
                            <div class="value">{{ $club->meeting_place }}</div>
                        </div>
                    @endif
                    @if($club->meeting_schedule)
                        <div class="info-item">
                            <div class="label">Lịch sinh hoạt</div>
                            <div class="value">{{ $club->meeting_schedule }}</div>
                        </div>
                    @endif
                    @if($club->social_links)
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="label">Liên kết mạng xã hội</div>
                            <div class="value" style="white-space: pre-line; font-size: 14px;">{{ $club->social_links }}</div>
                        </div>
                    @endif
                    <div class="info-item">
                        <div class="label">Vai trò của bạn</div>
                        <div class="value">
                            @if($membership->position === 'chairman')
                                <span class="badge" style="background-color: #0033A0; color: white;">Chủ nhiệm</span>
                            @elseif($membership->position === 'vice_chairman')
                                <span class="badge" style="background-color: #FFE600; color: #000;">Phó chủ nhiệm</span>
                            @elseif($membership->position === 'secretary')
                                <span class="badge" style="background-color: #0B3D91; color: white;">Thư ký CLB</span>
                            @elseif($membership->position === 'head_expertise')
                                <span class="badge" style="background-color: #5FB84A; color: white;">Trưởng ban Chuyên môn</span>
                            @elseif($membership->position === 'head_media')
                                <span class="badge" style="background-color: #8EDC6E; color: #000;">Trưởng ban Truyền thông</span>
                            @elseif($membership->position === 'head_events')
                                <span class="badge" style="background-color: #FFF3A0; color: #000;">Trưởng ban Sự kiện</span>
                            @elseif($membership->position === 'treasurer')
                                <span class="badge" style="background-color: #0066CC; color: white;">Trưởng ban Tài chính</span>
                            @else
                                <span class="badge" style="background-color: #6BCB77; color: white;">Thành viên</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(!$isChairman)
                <div class="card" style="border: 2px solid #FFF3A0;">
                    <div class="card-title" style="color: #B84A5F;">⚠️ Rời khỏi CLB</div>
                    <p style="color: var(--muted); margin-bottom: 16px;">
                        Nếu bạn muốn rời khỏi CLB này, vui lòng xác nhận bên dưới. Hành động này không thể hoàn tác.
                    </p>
                    <form action="{{ route('student.leave-club', $club->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn rời khỏi CLB này?');">
                        @csrf
                        <button type="submit" class="btn btn-danger">Rời khỏi CLB</button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Tab: Thành viên -->
        <div id="tab-members" class="tab-content">
            <div class="card">
                <div class="card-title">👥 Danh sách thành viên ({{ $members->count() }})</div>
                <div class="member-list">
                    @foreach($members as $member)
                        <div class="member-card">
                            <div class="name">{{ $member->name }}</div>
                            <div class="role">
                                @if($member->position === 'chairman')
                                    <span style="background-color: #0033A0; color: white; padding: 4px 8px; border-radius: 4px;">Chủ nhiệm</span>
                                @elseif($member->position === 'vice_chairman')
                                    <span style="background-color: #FFE600; color: #000; padding: 4px 8px; border-radius: 4px;">Phó chủ nhiệm</span>
                                @elseif($member->position === 'secretary')
                                    <span style="background-color: #0B3D91; color: white; padding: 4px 8px; border-radius: 4px;">Thư ký CLB</span>
                                @elseif($member->position === 'head_expertise')
                                    <span style="background-color: #5FB84A; color: white; padding: 4px 8px; border-radius: 4px;">Trưởng ban Chuyên môn</span>
                                @elseif($member->position === 'head_media')
                                    <span style="background-color: #8EDC6E; color: #000; padding: 4px 8px; border-radius: 4px;">Trưởng ban Truyền thông</span>
                                @elseif($member->position === 'head_events')
                                    <span style="background-color: #FFF3A0; color: #000; padding: 4px 8px; border-radius: 4px;">Trưởng ban Sự kiện</span>
                                @elseif($member->position === 'treasurer')
                                    <span style="background-color: #0066CC; color: white; padding: 4px 8px; border-radius: 4px;">Trưởng ban Tài chính</span>
                                @else
                                    <span style="background-color: #6BCB77; color: white; padding: 4px 8px; border-radius: 4px;">Thành viên</span>
                                @endif
                            </div>
                            <div style="font-size: 11px; color: var(--muted); margin-top: 4px;">
                                MSSV: {{ $member->student_code }}
                            </div>
                            @if($member->id === $user->id)
                                <div style="font-size: 11px; color: var(--primary); margin-top: 4px; font-weight: 600;">
                                    (Bạn)
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Tab: Sự kiện -->
        <div id="tab-events" class="tab-content">
            {{-- Tabs: Đang diễn ra | Sắp tới | Đã kết thúc --}}
            <div class="event-tabs mb-3" style="display: flex; gap: 8px; border-bottom: 2px solid var(--border); margin-bottom: 20px;">
                <button class="event-tab-btn {{ !request('event_tab') || request('event_tab') == 'ongoing' ? 'active' : '' }}" 
                        onclick="switchEventTab('ongoing')" 
                        style="padding: 10px 20px; border: none; background: none; font-weight: 600; color: var(--muted); cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px;">
                    Đang diễn ra
                </button>
                <button class="event-tab-btn {{ request('event_tab') == 'upcoming' ? 'active' : '' }}" 
                        onclick="switchEventTab('upcoming')" 
                        style="padding: 10px 20px; border: none; background: none; font-weight: 600; color: var(--muted); cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px;">
                    Sắp tới
                </button>
                <button class="event-tab-btn {{ request('event_tab') == 'finished' ? 'active' : '' }}" 
                        onclick="switchEventTab('finished')" 
                        style="padding: 10px 20px; border: none; background: none; font-weight: 600; color: var(--muted); cursor: pointer; border-bottom: 3px solid transparent; margin-bottom: -2px;">
                    Đã kết thúc
                </button>
            </div>

            {{-- Bộ lọc & tìm kiếm --}}
            <div class="filter-section mb-3">
                <form method="GET" action="{{ route('student.club-detail', $club->id) }}" class="row g-3" id="eventsSearchForm" onsubmit="localStorage.setItem('activeTab', 'events'); return true;">
                    <input type="hidden" name="tab" value="events">
                    <input type="hidden" name="event_tab" id="event_tab_input" value="{{ request('event_tab', 'ongoing') }}">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="font-size: 12px; margin-bottom: 4px;">
                            <i class="bi bi-search"></i> Từ khóa
                        </label>
                        <input type="text" name="event_search" class="form-control form-control-sm" 
                               value="{{ request('event_search') }}" 
                               placeholder="Tên hoạt động, người đề xuất, MSSV..."
                               style="font-size: 13px;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="font-size: 12px; margin-bottom: 4px;">
                            <i class="bi bi-funnel"></i> Trạng thái
                        </label>
                        <select name="event_status" class="form-control form-control-sm" style="font-size: 13px;">
                            <option value="">-- Tất cả --</option>
                            <option value="none" {{ request('event_status') == 'none' ? 'selected' : '' }}>Chưa đăng ký</option>
                            <option value="pending" {{ request('event_status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="approved" {{ request('event_status') == 'approved' ? 'selected' : '' }}>Đã đăng ký</option>
                            <option value="rejected" {{ request('event_status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="font-size: 12px; margin-bottom: 4px;">
                            <i class="bi bi-tag"></i> Loại
                        </label>
                        <select name="event_activity_type" class="form-control form-control-sm" style="font-size: 13px;">
                            <option value="">-- Tất cả --</option>
                            <option value="academic" {{ request('event_activity_type') == 'academic' ? 'selected' : '' }}>Học thuật</option>
                            <option value="arts" {{ request('event_activity_type') == 'arts' ? 'selected' : '' }}>Văn nghệ</option>
                            <option value="volunteer" {{ request('event_activity_type') == 'volunteer' ? 'selected' : '' }}>Tình nguyện</option>
                            <option value="other" {{ request('event_activity_type') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100" style="font-size: 12px; padding: 6px 12px;">
                            <i class="bi bi-funnel"></i> Lọc
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab Content: Đang diễn ra --}}
            <div id="event-tab-ongoing" class="event-tab-content" style="display: {{ !request('event_tab') || request('event_tab') == 'ongoing' ? 'block' : 'none' }};">
                @if($ongoingEvents->count() > 0)
                    <div class="event-cards-grid">
                        @foreach($ongoingEvents as $event)
                            @include('student.partials.event-card', ['event' => $event, 'status' => 'ongoing'])
                        @endforeach
                    </div>
                @else
                    <div class="card">
                        <div class="empty-state text-center" style="padding: 40px;">
                            <div style="font-size: 48px; margin-bottom: 16px;">📅</div>
                            <p style="color: var(--muted);">Chưa có sự kiện đang diễn ra.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tab Content: Sắp tới --}}
            <div id="event-tab-upcoming" class="event-tab-content" style="display: {{ request('event_tab') == 'upcoming' ? 'block' : 'none' }};">
                @if($upcomingEvents->count() > 0)
                    <div class="event-cards-grid">
                        @foreach($upcomingEvents as $event)
                            @include('student.partials.event-card', ['event' => $event, 'status' => 'upcoming'])
                        @endforeach
                    </div>
                @else
                    <div class="card">
                        <div class="empty-state text-center" style="padding: 40px;">
                            <div style="font-size: 48px; margin-bottom: 16px;">📅</div>
                            <p style="color: var(--muted);">Chưa có sự kiện sắp tới.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Tab Content: Đã kết thúc --}}
            <div id="event-tab-finished" class="event-tab-content" style="display: {{ request('event_tab') == 'finished' ? 'block' : 'none' }};">
                @if($attendedEvents->count() > 0)
                    <div class="event-cards-grid">
                        @foreach($attendedEvents as $event)
                            @include('student.partials.event-card', ['event' => $event, 'status' => 'finished'])
                        @endforeach
                    </div>
                @else
                    <div class="card">
                        <div class="empty-state text-center" style="padding: 40px;">
                            <div style="font-size: 48px; margin-bottom: 16px;">✅</div>
                            <p style="color: var(--muted);">Chưa có sự kiện đã kết thúc.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab: Danh sách đề xuất -->
        <div id="tab-proposals" class="tab-content">
            <div class="card" style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="card-title">📝 Danh sách đề xuất hoạt động</div>
                    <a href="{{ route('student.propose-event', ['club_id' => $club->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Đề xuất hoạt động mới
                    </a>
                </div>
            </div>

            @if($proposals->count() > 0)
                <div class="card">
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên hoạt động</th>
                                    <th>CLB</th>
                                    <th>Loại hoạt động</th>
                                    <th>Thời gian dự kiến</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian gửi</th>
                                    <th>Người duyệt</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposals as $index => $proposal)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $proposal->title }}</strong>
                                        </td>
                                        <td>{{ $club->name }}</td>
                                        <td>
                                            @if($proposal->activity_type == 'academic')
                                                <span class="badge" style="background:#e3f2fd;color:#1976d2;">Học thuật</span>
                                            @elseif($proposal->activity_type == 'arts')
                                                <span class="badge" style="background:#fce4ec;color:#c2185b;">Văn nghệ</span>
                                            @elseif($proposal->activity_type == 'volunteer')
                                                <span class="badge" style="background:#e8f5e9;color:#388e3c;">Tình nguyện</span>
                                            @else
                                                <span class="badge" style="background:#fff3e0;color:#f57c00;">Khác</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($proposal->start_at)
                                                {{ \Carbon\Carbon::parse($proposal->start_at)->format('d/m/Y H:i') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($proposal->approval_status == 'pending')
                                                <span class="badge warning">
                                                    <i class="bi bi-clock"></i> Chờ duyệt
                                                </span>
                                            @elseif($proposal->approval_status == 'approved')
                                                <span class="badge success">
                                                    <i class="bi bi-check-circle"></i> Đã duyệt
                                                </span>
                                            @elseif($proposal->approval_status == 'rejected')
                                                <span class="badge" style="background:#FFF3A0;color:#B84A5F;">
                                                    <i class="bi bi-x-circle"></i> Bị từ chối
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($proposal->created_at)->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            @if(isset($proposal->approver_name) && $proposal->approver_name)
                                                {{ $proposal->approver_name }}
                                                @if(isset($proposal->approver_student_code) && $proposal->approver_student_code)
                                                    <br><small class="text-muted">({{ $proposal->approver_student_code }})</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('student.proposal-detail', $proposal->id) }}" class="btn btn-xs btn-info" style="padding: 4px 8px; font-size: 11px;">
                                                <i class="bi bi-eye"></i> Xem chi tiết
                                            </a>
                                            @if($proposal->approval_status == 'approved' && $proposal->status == 'upcoming')
                                                <a href="{{ route('student.activity-detail', $proposal->id) }}" class="btn btn-xs btn-success" style="padding: 4px 8px; font-size: 11px; display: block; margin-top: 4px;">
                                                    <i class="bi bi-calendar-event"></i> Xem hoạt động
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="empty-state">
                        <p>Bạn chưa có đề xuất hoạt động nào.</p>
                        <a href="{{ route('student.propose-event', ['club_id' => $club->id]) }}" class="btn btn-primary" style="margin-top: 16px;">
                            <i class="bi bi-plus-circle"></i> Tạo đề xuất đầu tiên
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Tab: Điểm hoạt động -->
        <div id="tab-points" class="tab-content">
            <div class="card">
                <div class="points-display">
                    <div class="total">{{ $totalActivityPoints }}</div>
                    <div class="level">{{ $activityLevel }}</div>
                </div>
            </div>

            @if($pointsByEvent->count() > 0)
                <div class="card">
                    <div class="card-title">📊 Điểm theo sự kiện</div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sự kiện</th>
                                <th>Ngày diễn ra</th>
                                <th>Điểm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pointsByEvent as $point)
                                <tr>
                                    <td>{{ $point->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($point->start_at)->format('d/m/Y H:i') }}</td>
                                    <td><strong>{{ $point->activity_points }} điểm</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card">
                    <div class="empty-state">
                        <p>Bạn chưa có điểm hoạt động nào.</p>
                    </div>
                </div>
            @endif
        </div>
    </main>
    </div>

    @include('student.footer')

    <script>
        function showTab(tabName) {
            // Ẩn tất cả tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            // Hiển thị tab được chọn
            document.getElementById('tab-' + tabName).classList.add('active');
            document.querySelector(`.tab[onclick="showTab('${tabName}')"]`).classList.add('active');
        }

        // Tự động mở tab và scroll đến sự kiện
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash;
            const urlParams = new URLSearchParams(window.location.search);
            const tabFromUrl = urlParams.get('tab');
            const savedTab = localStorage.getItem('activeTab');
            
            // Kiểm tra nếu có scroll_to_event từ session
            @if(session('scroll_to_event'))
                const eventId = {{ session('scroll_to_event') }};
                showTab('events');
                setTimeout(function() {
                    const eventElement = document.getElementById('event-' + eventId);
                    if (eventElement) {
                        eventElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Highlight sự kiện
                        eventElement.style.backgroundColor = '#fff3cd';
                        setTimeout(function() {
                            eventElement.style.backgroundColor = '';
                        }, 2000);
                    }
                }, 300);
            @else
                // Ưu tiên tab từ URL
                if (tabFromUrl === 'events') {
                    showTab('events');
                    localStorage.setItem('activeTab', 'events');
                } else if (hash === '#proposals' || savedTab === 'proposals') {
                    showTab('proposals');
                    localStorage.removeItem('activeTab'); // Xóa sau khi dùng
                } else if (savedTab) {
                    showTab(savedTab);
                }
            @endif
        });

        // Function để đóng sidebar khi click vào menu item (trên mobile)
        function closeSidebarOnClick() {
            // Chỉ đóng trên mobile (< 900px)
            if (window.innerWidth < 900) {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar && !sidebar.classList.contains('sidebar-collapsed')) {
                    toggleSidebar();
                }
            }
        }

        // Function để chuyển event tabs
        function switchEventTab(tab) {
            // Cập nhật hidden input
            const tabInput = document.getElementById('event_tab_input');
            if (tabInput) {
                tabInput.value = tab;
            }
            
            // Submit form để reload với tab mới
            const form = document.getElementById('eventsSearchForm');
            if (form) {
                form.submit();
            }
        }
    </script>
</body>
</html>

