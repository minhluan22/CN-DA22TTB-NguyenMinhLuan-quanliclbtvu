<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'Thống kê cá nhân' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .tab:hover {
            color: var(--primary);
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

        <div class="profile-header">
            <div class="avatar-large">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <div class="meta">
                    MSSV: {{ $user->student_code ?? '---' }} | Email: {{ $user->email }}
                </div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab {{ request()->routeIs('student.personal-statistics.activities') ? 'active' : '' }}" onclick="showTab('activities')">🎯 Hoạt động đã tham gia</button>
            <button class="tab {{ request()->routeIs('student.personal-statistics.points') ? 'active' : '' }}" onclick="showTab('points')">⭐ Điểm hoạt động cá nhân</button>
            <button class="tab {{ request()->routeIs('student.personal-statistics.club-history') ? 'active' : '' }}" onclick="showTab('club-history')">📚 Lịch sử tham gia CLB</button>
            <button class="tab {{ request()->routeIs('student.personal-statistics.violations') ? 'active' : '' }}" onclick="showTab('violations')">⚠️ Lịch sử vi phạm</button>
        </div>

        <!-- Tab: Hoạt động đã tham gia -->
        <div id="tab-activities" class="tab-content {{ request()->routeIs('student.personal-statistics.activities') ? 'active' : '' }}">
            @if(request()->routeIs('student.personal-statistics.activities'))
                @yield('activities-content')
            @else
                <div class="card">
                    <p class="text-center text-muted">Đang tải...</p>
                </div>
            @endif
        </div>

        <!-- Tab: Điểm hoạt động cá nhân -->
        <div id="tab-points" class="tab-content {{ request()->routeIs('student.personal-statistics.points') ? 'active' : '' }}">
            @if(request()->routeIs('student.personal-statistics.points'))
                @yield('points-content')
            @else
                <div class="card">
                    <p class="text-center text-muted">Đang tải...</p>
                </div>
            @endif
        </div>

        <!-- Tab: Lịch sử tham gia CLB -->
        <div id="tab-club-history" class="tab-content {{ request()->routeIs('student.personal-statistics.club-history') ? 'active' : '' }}">
            @if(request()->routeIs('student.personal-statistics.club-history'))
                @yield('club-history-content')
            @else
                <div class="card">
                    <p class="text-center text-muted">Đang tải...</p>
                </div>
            @endif
        </div>

        <!-- Tab: Lịch sử vi phạm -->
        <div id="tab-violations" class="tab-content {{ request()->routeIs('student.personal-statistics.violations') ? 'active' : '' }}">
            @if(request()->routeIs('student.personal-statistics.violations'))
                @yield('violations-content')
            @else
                <div class="card">
                    <p class="text-center text-muted">Đang tải...</p>
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

            // Redirect đến route tương ứng
            const routes = {
                'activities': '{{ route("student.personal-statistics.activities") }}',
                'points': '{{ route("student.personal-statistics.points") }}',
                'club-history': '{{ route("student.personal-statistics.club-history") }}',
                'violations': '{{ route("student.personal-statistics.violations") }}'
            };

            if (routes[tabName]) {
                window.location.href = routes[tabName];
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.querySelector('.sidebar-overlay');
            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

