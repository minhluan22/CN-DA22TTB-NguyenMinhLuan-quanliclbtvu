<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CLB của tôi</title>
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
        .club-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            align-items: start;
            width: 100%;
            box-sizing: border-box;
        }
        
        @media (max-width: 1400px) {
            .club-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 1000px) {
            .club-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 600px) {
            .club-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .club-card {
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
        .club-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        .club-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .club-info {
            flex: 1;
            min-width: 0;
        }
        .club-logo {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }
        .club-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        .club-info h3 {
            margin: 0;
            font-size: 18px;
            color: var(--text-dark);
        }
        .club-info .code {
            color: var(--muted);
            font-size: 14px;
            margin-top: 4px;
        }
        .club-stats {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
            font-size: 14px;
            color: var(--muted);
            flex-shrink: 0;
            flex-wrap: wrap;
        }
        .club-stats span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .club-role {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 12px;
        }
        .btn-access {
            width: 100%;
            background: var(--primary-blue);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
            flex-shrink: 0;
            margin-top: auto;
        }
        .btn-access:hover {
            background: var(--primary-blue-hover);
        }
        .pending-section {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .pending-section h2 {
            margin: 0 0 16px 0;
            font-size: 18px;
            color: var(--secondary);
        }
        .pending-item {
            padding: 12px;
            background: var(--bg);
            border-radius: 8px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
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
            <h1>⭐ CLB của tôi</h1>
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

        @if(isset($clubProposals) && $clubProposals->count() > 0)
            <div class="pending-section" style="background: var(--card); padding: 20px; border-radius: 16px; margin-bottom: 24px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px; color: var(--text-dark);">📝 Đơn đề nghị CLB của tôi</h2>
                @foreach($clubProposals as $proposal)
                    <div class="pending-item" style="display: flex; justify-content: space-between; align-items: center; padding: 16px; background: var(--soft-yellow); border-radius: 8px; margin-bottom: 12px;">
                        <div>
                            <strong>{{ $proposal->club_name }}</strong>
                            <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                                Ngày gửi: {{ \Carbon\Carbon::parse($proposal->created_at)->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        @if($proposal->status === 'pending')
                            <span class="badge warning">⏳ Chờ duyệt</span>
                        @elseif($proposal->status === 'approved')
                            <span class="badge success">✅ Đã duyệt</span>
                        @else
                            <span class="badge" style="background: #FFF3A0; color: #B84A5F;">❌ Từ chối</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if($pendingRegistrations->count() > 0)
            <div class="pending-section">
                <h2>⏳ Đơn đăng ký đang chờ duyệt</h2>
                @foreach($pendingRegistrations as $reg)
                    <div class="pending-item">
                        <div>
                            <strong>{{ $reg->club_name }}</strong> ({{ $reg->club_code }})
                            <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                                Ngày gửi: {{ \Carbon\Carbon::parse($reg->created_at)->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <span class="badge warning">Chờ duyệt</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if($myClubs->count() > 0)
            <div class="club-grid">
                @foreach($myClubs as $club)
                    <div class="club-card">
                        <div class="club-header">
                            <div class="club-logo">
                                @if($club->logo)
                                    <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }}">
                                @else
                                    {{ strtoupper(substr($club->name ?? 'CLB', 0, 3)) }}
                                @endif
                            </div>
                            <div class="club-info">
                                <h3>{{ $club->name }}</h3>
                                <div class="code">{{ $club->code }}</div>
                            </div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            @if($club->position === 'chairman')
                                <span class="badge" style="background: #0033A0; color: white;">👑 Chủ nhiệm</span>
                            @elseif($club->position === 'vice_chairman')
                                <span class="badge" style="background: #FFE600; color: #000;">⭐ Phó chủ nhiệm</span>
                            @elseif($club->position === 'secretary')
                                <span class="badge" style="background: #0B3D91; color: white;">📝 Thư ký CLB</span>
                            @elseif($club->position === 'head_expertise')
                                <span class="badge" style="background: #5FB84A; color: white;">🎓 Trưởng ban Chuyên môn</span>
                            @elseif($club->position === 'head_media')
                                <span class="badge" style="background: #8EDC6E; color: #000;">📢 Trưởng ban Truyền thông</span>
                            @elseif($club->position === 'head_events')
                                <span class="badge" style="background: #FFF3A0; color: #000;">🎉 Trưởng ban Sự kiện</span>
                            @elseif($club->position === 'treasurer')
                                <span class="badge" style="background: #0066CC; color: white;">💰 Trưởng ban Tài chính</span>
                            @else
                                <span class="badge" style="background: #dcfce7; color: #166534;">Thành viên</span>
                            @endif
                        </div>

                        <div class="club-stats">
                            <span>👥 {{ $club->member_count }} thành viên</span>
                            <span>🎉 {{ $club->events_attended }} đã tham gia</span>
                            <span>📅 {{ $club->upcoming_events }} sắp tới</span>
                            <span>⭐ {{ $club->activity_points }} điểm</span>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <span class="badge success">Đang hoạt động</span>
                        </div>

                        <a href="{{ route('student.club-detail', $club->id) }}" class="btn-access">Truy cập CLB</a>
                    </div>
                @endforeach
            </div>
            
            {{-- PHÂN TRANG --}}
            @if($myClubs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $myClubs->links('vendor.pagination.custom') }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">⭐</div>
                <h3>Bạn chưa tham gia CLB nào</h3>
                <p>Hãy tìm và đăng ký tham gia các CLB bạn quan tâm!</p>
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

