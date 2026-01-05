<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
            margin-top: 0;
            padding-top: 0;
        }
        /* Sidebar styles được định nghĩa trong student.sidebar.blade.php */

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
        .logo {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
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
            padding-top: 24px;
            margin-top: 64px;
            width: calc(100% - 240px);
            max-width: 100%;
            flex: 1;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        .header {
            background: linear-gradient(135deg, #ffffff 0%, #E6F0FF 100%);
            padding: 28px 32px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 24px;
            border: 2px solid rgba(11, 61, 145, 0.1);
            box-shadow: 0 4px 16px rgba(11, 61, 145, 0.1), 0 8px 32px rgba(11, 61, 145, 0.05);
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(11, 61, 145, 0.05) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .header:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(11, 61, 145, 0.15), 0 12px 40px rgba(11, 61, 145, 0.08);
        }
        
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
            display: grid;
            place-items: center;
            font-weight: 800;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(11, 61, 145, 0.3);
            position: relative;
            z-index: 1;
            transition: all 0.3s;
        }
        
        .avatar:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 24px rgba(11, 61, 145, 0.4);
        }
        
        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .header-info {
            flex: 1;
            min-width: 0;
            position: relative;
            z-index: 1;
        }
        .header-info .name {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .header-info .meta {
            color: var(--muted);
            font-size: 15px;
            line-height: 1.6;
            font-weight: 500;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }
        @media (max-width: 1400px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--primary-blue), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 32px rgba(0,0,0,0.12), 0 4px 16px rgba(0,0,0,0.08);
        }
        
        .card:hover::before {
            opacity: 1;
        }
        
        .stat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 120px;
            gap: 16px;
            position: relative;
        }
        
        .stat > div {
            flex: 1;
            min-width: 0;
        }
        
        .stat .label { 
            color: var(--muted); 
            font-size: 14px; 
            margin-bottom: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat .value { 
            font-size: 42px; 
            font-weight: 800; 
            background: linear-gradient(135deg, var(--primary-blue) 0%, #0033A0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-top: 8px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            white-space: nowrap;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .badge.success { 
            background: linear-gradient(135deg, #dcfce7 0%, #5FB84A 100%);
            color: #166534;
        }
        .badge.warning { 
            background: linear-gradient(135deg, var(--soft-yellow) 0%, #FFE600 100%);
            color: var(--text-dark);
        }
        .badge.info { 
            background: linear-gradient(135deg, #dbeafe 0%, #0B3D91 100%);
            color: white;
        }
        .section-title {
            font-weight: 800;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }
        
        .notification-item, .activity-item {
            transition: all 0.3s;
            cursor: pointer;
            border-radius: 12px;
            margin-bottom: 8px;
        }
        
        .notification-item:hover, .activity-item:hover {
            background: linear-gradient(135deg, rgba(11, 61, 145, 0.08) 0%, rgba(11, 61, 145, 0.03) 100%);
            transform: translateX(4px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: 12px;
            overflow: hidden;
        }
        .table thead {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #0033A0 100%);
            color: var(--text-light);
        }
        .table th, .table td {
            padding: 16px 18px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }
        .table th { 
            color: var(--text-light); 
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table td { 
            color: var(--secondary);
            font-weight: 500;
        }
        .table tbody tr {
            transition: all 0.3s;
        }
        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(11, 61, 145, 0.08) 0%, rgba(11, 61, 145, 0.03) 100%);
            transform: translateX(4px);
        }
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        .empty {
            text-align: center;
            color: var(--muted);
            padding: 32px 20px;
            font-size: 14px;
        }
        .section-title {
            font-size: 18px;
            margin-bottom: 16px;
        }
        .two-column-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 0;
        }
        @keyframes pulse-badge {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5);
            }
        }
        
        @media (max-width: 900px) {
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
            .grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .two-column-grid {
                grid-template-columns: 1fr;
            }
            .header {
                flex-direction: column;
                text-align: center;
            }
            .header-info .meta {
                font-size: 12px;
            }
            .table {
                font-size: 12px;
            }
            .table th, .table td {
                padding: 10px 8px;
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
            <div class="avatar">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name ?? 'SV', 0, 1)) }}
                @endif
            </div>
            <div class="header-info">
                <div class="name">Xin chào, {{ $user->name ?? 'Sinh viên' }} 👋</div>
                <div class="meta">
                    MSSV: {{ $user->student_code ?? '---' }} | Email: {{ $user->email ?? '---' }} |
                    Hiện bạn tham gia {{ $stats['joined'] }} CLB
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card stat" style="border-left: 4px solid #5FB84A;">
                <div>
                    <div class="label" style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 20px;">🏢</span>
                        <span>CLB tham gia</span>
                    </div>
                    <div class="value" style="background: linear-gradient(135deg, #5FB84A 0%, #166534 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ $stats['joined'] }}</div>
                </div>
                <span class="badge success">✔ Hoạt động</span>
            </div>
            <div class="card stat" style="border-left: 4px solid #FFE600;">
                <div>
                    <div class="label" style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 20px;">⭐</span>
                        <span>Điểm hoạt động</span>
                    </div>
                    <div class="value" style="background: linear-gradient(135deg, #FFE600 0%, #fbbf24 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ number_format($stats['activity_points']) }}</div>
                </div>
                <span class="badge" style="background: linear-gradient(135deg, #fbbf24 0%, #FFE600 100%); color: #000;">⭐ Điểm</span>
            </div>
            <div class="card stat" style="border-left: 4px solid #0B3D91;">
                <div>
                    <div class="label" style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 20px;">🎉</span>
                        <span>Sự kiện đã tham gia</span>
                    </div>
                    <div class="value">{{ $stats['events_attended'] }}</div>
                </div>
                <span class="badge success">✓ Hoàn thành</span>
            </div>
            <div class="card stat" style="border-left: 4px solid #9333ea;">
                <div>
                    <div class="label" style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 20px;">📅</span>
                        <span>Sắp diễn ra</span>
                    </div>
                    <div class="value" style="background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ $stats['upcoming'] }}</div>
                </div>
                <span class="badge info">📅 Lịch</span>
            </div>
        </div>

        <div class="card" style="margin-bottom: 32px;">
            <div class="section-title">
                <span style="font-size: 24px;">⭐</span>
                <span>CLB của tôi</span>
                @if($stats['joined'] > 0)
                    <span class="badge" style="background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%); color: white; margin-left: auto;">{{ $stats['joined'] }} CLB</span>
                @endif
            </div>
            <div style="overflow-x: auto;">
                <table class="table">
                <thead>
                    <tr>
                        <th>Mã CLB</th>
                        <th>Tên CLB</th>
                        <th>Chức vụ</th>
                        <th>Trạng thái</th>
                        <th>Ngày tham gia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($memberships as $m)
                        <tr>
                            <td>{{ $m->club_code }}</td>
                            <td>
                                <a href="{{ route('student.club-detail', $m->club_id) }}" 
                                   style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">
                                    {{ $m->club_name }}
                                </a>
                            </td>
                            <td>
                                @if ($m->position === 'chairman')
                                    <span class="badge warning">👑 Chủ nhiệm</span>
                                @elseif ($m->position === 'vice_chairman')
                                    <span class="badge info">⭐ Phó chủ nhiệm</span>
                                @elseif ($m->position === 'secretary')
                                    <span class="badge" style="background:#0dcaf0;color:#000;">📝 Thư ký CLB</span>
                                @elseif ($m->position === 'head_expertise')
                                    <span class="badge" style="background:#0d6efd;color:#fff;">🎓 Trưởng ban Chuyên môn</span>
                                @elseif ($m->position === 'head_media')
                                    <span class="badge success">📢 Trưởng ban Truyền thông</span>
                                @elseif ($m->position === 'head_events')
                                    <span class="badge" style="background:#9333ea;color:#fff;">🎉 Trưởng ban Sự kiện</span>
                                @elseif ($m->position === 'treasurer')
                                    <span class="badge" style="background:#f59e0b;color:#fff;">💰 Trưởng ban Tài chính</span>
                                @else
                                    <span class="badge" style="background:#f1f5f9;color:#0f172a;">Thành viên</span>
                                @endif
                            </td>
                            <td>
                                @if ($m->status === 'approved')
                                    <span class="badge success">Đang tham gia</span>
                                @elseif ($m->status === 'pending')
                                    <span class="badge warning">Chờ duyệt</span>
                                @elseif ($m->status === 'suspended')
                                    <span class="badge" style="background:#FFF3A0;color:#B84A5F;">Đình chỉ</span>
                                @elseif ($m->status === 'left')
                                    <span class="badge" style="background:#e5e7eb;color:#111827;">Đã rời</span>
                                @else
                                    <span class="badge" style="background:#FFF3A0;color:#B84A5F;">Từ chối</span>
                                @endif
                            </td>
                            <td>{{ $m->joined_date ? \Carbon\Carbon::parse($m->joined_date)->format('d/m/Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Bạn chưa tham gia CLB nào</td></tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>

        <div class="two-column-grid">
            <!-- Hoạt động sắp tới -->
            <div class="card">
                <div class="section-title">
                    <span style="font-size: 24px;">📅</span>
                    <span>Hoạt động sắp tới</span>
                    @if($stats['upcoming'] > 0)
                        <span class="badge" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; margin-left: auto; animation: pulse-badge 2s ease-in-out infinite;">{{ $stats['upcoming'] }}</span>
                    @endif
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    @forelse($upcomingEvents as $event)
                        <div class="activity-item" style="padding: 12px; border-bottom: 1px solid var(--border); display: flex; align-items: start; gap: 12px;">
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 4px; font-size: 14px;">
                                    {{ $event->title }}
                                </div>
                                <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">
                                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                                        🏢 {{ $event->club_name ?? 'CLB' }}
                                    </span>
                                </div>
                                <div style="font-size: 12px; color: var(--muted);">
                                    <span style="display: inline-flex; align-items: center; gap: 4px;">
                                        📅 {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('student.activity-detail', $event->id) }}" 
                               class="badge info" 
                               style="text-decoration: none; flex-shrink: 0;">
                                Xem
                            </a>
                        </div>
                    @empty
                        <div class="empty">Không có hoạt động sắp tới</div>
                    @endforelse
                </div>
                @if($stats['upcoming'] > 5)
                    <div style="padding: 12px; text-align: center; border-top: 1px solid var(--border);">
                        <a href="{{ route('student.activities') }}" style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">
                            Xem tất cả hoạt động →
                        </a>
                    </div>
                @endif
            </div>

            <!-- Thông báo realtime -->
            <div class="card">
                <div class="section-title">
                    <span style="font-size: 24px;">🔔</span>
                    <span>Thông báo mới</span>
                    @if($recentNotifications->count() > 0)
                        <span class="badge" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; margin-left: auto; animation: pulse-badge 2s ease-in-out infinite;">{{ $recentNotifications->count() }}</span>
                    @endif
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentNotifications as $notification)
                        <div class="notification-item" style="padding: 12px; border-bottom: 1px solid var(--border);">
                            <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 6px; font-size: 14px;">
                                {{ $notification->title }}
                            </div>
                            <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">
                                @if($notification->notification_source === 'admin')
                                    <span class="badge" style="background:#3b82f6;color:#fff;font-size:10px;">Admin</span>
                                @else
                                    <span class="badge" style="background:#10b981;color:#fff;font-size:10px;">CLB</span>
                                @endif
                                <span style="margin-left: 8px;">
                                    {{ $notification->sent_at ? \Carbon\Carbon::parse($notification->sent_at)->format('d/m/Y H:i') : '' }}
                                </span>
                            </div>
                            <div style="font-size: 12px; color: var(--muted); margin-top: 8px;">
                                {{ mb_substr(strip_tags($notification->body), 0, 80) }}{{ mb_strlen(strip_tags($notification->body)) > 80 ? '...' : '' }}
                            </div>
                            <div style="margin-top: 8px;">
                                <a href="{{ route('student.notifications.show', $notification->id) }}" 
                                   style="color: var(--primary-blue); text-decoration: none; font-size: 12px; font-weight: 600;">
                                    Xem chi tiết →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty">Chưa có thông báo mới</div>
                    @endforelse
                </div>
                @if($recentNotifications->count() > 0)
                    <div style="padding: 12px; text-align: center; border-top: 1px solid var(--border);">
                        <a href="{{ route('student.notifications') }}" style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">
                            Xem tất cả thông báo →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Hoạt động gần đây -->
        @if($recentActivities->count() > 0)
            <div class="card" style="margin-top: 32px;">
                <div class="section-title">
                    <span style="font-size: 24px;">📊</span>
                    <span>Hoạt động gần đây</span>
                    <span class="badge" style="background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%); color: white; margin-left: auto;">{{ $recentActivities->count() }} hoạt động</span>
                </div>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên hoạt động</th>
                                <th>CLB</th>
                                <th>Ngày tham gia</th>
                                <th>Điểm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivities as $index => $activity)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('student.activity-detail', $activity->id) }}" 
                                           style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">
                                            {{ $activity->title }}
                                        </a>
                                    </td>
                                    <td>{{ $activity->club_name ?? 'CLB' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($activity->start_at)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge" style="background:#fbbf24;color:#000;">
                                            +{{ $activity->activity_points ?? 0 }} điểm
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        </main>
    </div>

    @include('student.footer')

    <script>
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
    </script>
</body>
</html>
