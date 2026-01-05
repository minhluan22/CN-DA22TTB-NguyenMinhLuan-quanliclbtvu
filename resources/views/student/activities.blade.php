<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hoạt Động CLB</title>
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

        .content {
            margin-left: 240px;
            padding: 24px;
            width: calc(100% - 240px);
            max-width: 100%;
            flex: 1;
            transition: margin-left 0.3s ease, width 0.3s ease;
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
            margin-top: 64px;
            min-height: 100vh;
            width: calc(100% - 240px);
            max-width: 100%;
            box-sizing: border-box;
        }
        .header {
            background: var(--card);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: var(--text-dark);
        }
        .filters {
            background: var(--card);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            box-sizing: border-box;
        }
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 6px;
            font-weight: 600;
        }
        .filter-group select {
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
        }
        .btn-filter {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .event-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            align-items: start;
            width: 100%;
            box-sizing: border-box;
        }
        
        @media (max-width: 1400px) {
            .event-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 1000px) {
            .event-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 600px) {
            .event-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .event-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        .event-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .event-image {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .event-info {
            flex: 1;
            min-width: 0;
        }
        .event-info h3 {
            margin: 0 0 4px 0;
            font-size: 18px;
            color: var(--text-dark);
        }
        .event-info .club-name {
            color: var(--muted);
            font-size: 14px;
        }
        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 14px;
            color: var(--muted);
            flex-shrink: 0;
        }
        .event-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .event-description {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
            min-height: 40px;
        }
        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
            margin-top: auto;
            gap: 12px;
        }
        .btn-view {
            background: var(--primary-blue);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            white-space: nowrap;
        }
        .btn-view:hover {
            background: var(--primary-blue-hover);
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

        /* =========================================================
           CUSTOM PAGINATION STYLE
           → Style cho phân trang tùy chỉnh (giống y hệt trang Danh sách tài khoản Admin)
        ========================================================= */
        .pagination {
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0;
            list-style: none;
            padding: 0;
        }

        .pagination .page-item {
            margin: 0 2px;
            list-style: none;
        }

        .pagination .page-link {
            color: #0B3D91;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 6px 12px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.15s ease;
            min-width: 38px;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            line-height: 1.42857143;
            cursor: pointer;
        }

        .pagination .page-link:hover:not(.disabled):not([aria-disabled="true"]) {
            color: white;
            background-color: #0B3D91;
            border-color: #0B3D91;
            text-decoration: none;
        }

        .pagination .page-item.active .page-link {
            color: white;
            background-color: #0B3D91;
            border-color: #0B3D91;
            font-weight: 600;
            cursor: default;
            z-index: 1;
        }

        .pagination .page-item.active .page-link:hover {
            color: white;
            background-color: #0B3D91;
            border-color: #0B3D91;
        }

        .pagination .page-item.disabled .page-link,
        .pagination .page-item.disabled .page-link:hover,
        .pagination .page-item.disabled .page-link:focus {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }

        /* Đảm bảo phân trang hiển thị đúng trong container */
        nav[aria-label="Page navigation"] {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        nav[aria-label="Page navigation"] .pagination {
            margin: 0;
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
            <h1>🗓️ Hoạt Động CLB</h1>
        </div>

        <div class="filters">
            <form method="GET" action="{{ route('student.activities') }}">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Lọc theo CLB</label>
                        <select name="club_id">
                            <option value="">Tất cả CLB</option>
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}" {{ $clubId == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Lọc theo thời gian</label>
                        <select name="time_filter">
                            <option value="">Tất cả</option>
                            <option value="week" {{ $timeFilter === 'week' ? 'selected' : '' }}>Tuần này</option>
                            <option value="month" {{ $timeFilter === 'month' ? 'selected' : '' }}>Tháng này</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Lọc theo trạng thái</label>
                        <select name="status_filter">
                            <option value="">Tất cả</option>
                            <option value="upcoming" {{ $statusFilter === 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
                            <option value="ongoing" {{ $statusFilter === 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                            <option value="finished" {{ $statusFilter === 'finished' ? 'selected' : '' }}>Đã kết thúc</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-filter">🔍 Lọc</button>
                @if($clubId || $timeFilter || $statusFilter)
                    <a href="{{ route('student.activities') }}" style="margin-left: 8px; color: var(--muted); text-decoration: none;">Xóa bộ lọc</a>
                @endif
            </form>
        </div>

        @if($events->count() > 0)
            <div class="event-grid">
                @foreach($events as $event)
                    <div class="event-card" id="event-{{ $event->id }}">
                        <div class="event-header">
                            <div class="event-image">🎉</div>
                            <div class="event-info">
                                <h3>{{ $event->title }}</h3>
                                <div class="club-name">🏢 {{ $event->club_name }}</div>
                            </div>
                        </div>

                        <div class="event-meta">
                            <span>📅 {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}</span>
                            <span>📍 {{ $event->location ?? 'Chưa cập nhật' }}</span>
                            <span>👥 {{ $event->participant_count }} người tham gia</span>
                        </div>

                        <div class="event-description">
                            {{ $event->description ?? 'Chưa có mô tả.' }}
                        </div>

                        <div style="margin-bottom: 12px;">
                            @if($event->status === 'upcoming')
                                <span class="badge info">Sắp diễn ra</span>
                            @elseif($event->status === 'ongoing')
                                <span class="badge success">Đang diễn ra</span>
                            @elseif($event->status === 'finished')
                                <span class="badge danger">Đã kết thúc</span>
                            @endif
                        </div>

                        <a href="{{ route('student.activity-detail', $event->id) }}?{{ http_build_query(request()->query()) }}" class="btn-view">Xem chi tiết</a>
                    </div>
                @endforeach
            </div>
            
            {{-- PHÂN TRANG --}}
            @if($events->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $events->links('vendor.pagination.custom') }}
                </div>
            @endif
        @else
            <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
                <div style="font-size: 64px; margin-bottom: 16px;">🗓️</div>
                <h3>Không có sự kiện nào</h3>
                <p>Hãy thử thay đổi bộ lọc tìm kiếm.</p>
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

        // Scroll về vị trí event khi quay lại từ trang chi tiết
        document.addEventListener('DOMContentLoaded', function() {
            const referrer = document.referrer;
            if (referrer && referrer.includes('/activity/')) {
                const eventIdMatch = referrer.match(/\/activity\/(\d+)/);
                if (eventIdMatch) {
                    const eventId = eventIdMatch[1];
                    const eventCard = document.getElementById('event-' + eventId);
                    
                    if (eventCard) {
                        setTimeout(() => {
                            eventCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            eventCard.style.boxShadow = '0 0 0 3px #FFE600';
                            setTimeout(() => {
                                eventCard.style.boxShadow = '';
                            }, 2000);
                        }, 100);
                    }
                }
            }
        });
    </script>
</body>
</html>

