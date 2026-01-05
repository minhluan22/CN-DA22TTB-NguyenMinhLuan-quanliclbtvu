<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $club->name }} - Chi tiết CLB</title>
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
            background: #f9fafb;
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
            background: #f9fafb;
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
            background: #f9fafb;
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
                <div style="margin-top: 12px;">
                    @if($user && !$isMember && !$hasRegistration)
                        <button type="button" class="btn btn-primary" onclick="openRegisterModal()">Tham gia CLB</button>
                    @elseif($hasRegistration)
                        <span class="badge warning">⏳ Đang chờ phê duyệt</span>
                    @elseif($isMember)
                        <a href="{{ route('student.my-clubs') }}" class="btn btn-primary" style="text-decoration: none; margin-left: 8px;">Xem trang thành viên</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary" style="text-decoration: none;">Đăng nhập để tham gia</a>
                    @endif
                    <a href="{{ route('student.all-clubs') }}?{{ request()->getQueryString() }}" class="btn btn-secondary" style="text-decoration: none; margin-left: 8px;">← Quay lại</a>
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
            <button class="tab" onclick="showTab('events')">🎉 Sự kiện</button>
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
                                <a href="{{ $club->fanpage }}" target="_blank" style="color: var(--primary); text-decoration: none;">Xem Fanpage</a>
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
                </div>
            </div>
        </div>

        <!-- Tab: Sự kiện -->
        <div id="tab-events" class="tab-content">
            @if($upcomingEvents->count() > 0)
                <div class="card">
                    <div class="card-title">📅 Sự kiện sắp tới</div>
                    <div class="event-list">
                        @foreach($upcomingEvents as $event)
                            <div class="event-item">
                                <div class="event-info">
                                    <h4>{{ $event->title }}</h4>
                                    <div class="meta">
                                        📅 {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                                        @if($event->location)
                                            | 📍 {{ $event->location }}
                                        @endif
                                    </div>
                                    @if($event->description)
                                        <div style="font-size: 14px; color: var(--muted); margin-top: 8px;">
                                            {{ Str::limit($event->description, 100) }}
                                        </div>
                                    @endif
                                </div>
                                @if(!$isMember)
                                    <div>
                                        <div style="padding: 8px; background: var(--soft-yellow); border-radius: 6px; font-size: 12px; color: var(--muted);">
                                            💡 Bạn cần tham gia CLB để đăng ký
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($pastEvents->count() > 0)
                <div class="card">
                    <div class="card-title">✅ Hoạt động đã tổ chức</div>
                    <div class="event-list">
                        @foreach($pastEvents as $event)
                            <div class="event-item">
                                <div class="event-info">
                                    <h4>{{ $event->title }}</h4>
                                    <div class="meta">
                                        📅 {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                                        @if($event->location)
                                            | 📍 {{ $event->location }}
                                        @endif
                                    </div>
                                    @if($event->description)
                                        <div style="font-size: 14px; color: var(--muted); margin-top: 8px;">
                                            {{ Str::limit($event->description, 100) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($upcomingEvents->count() === 0 && $pastEvents->count() === 0)
                <div class="card">
                    <div class="empty-state">
                        <div style="font-size: 48px; margin-bottom: 16px;">🎉</div>
                        <p>Chưa có sự kiện nào.</p>
                    </div>
                </div>
            @endif
        </div>
    </main>
    </div>

    @include('student.footer')

    <!-- Modal đăng ký tham gia CLB -->
    <div id="registerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 16px; padding: 24px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: var(--primary);">Đăng ký tham gia CLB</h3>
                <button onclick="closeRegisterModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--muted);">&times;</button>
            </div>
            <form action="{{ route('student.register-club', $club->id) }}" method="POST" id="registerForm">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-dark);">
                        Lý do tham gia CLB <span style="color: var(--muted); font-weight: normal;">(Tùy chọn)</span>
                    </label>
                    <textarea 
                        name="reason" 
                        id="reason" 
                        rows="5" 
                        style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; resize: vertical; box-sizing: border-box;"
                        placeholder="Nhập lý do bạn muốn tham gia CLB này..."></textarea>
                    <small style="color: var(--muted); font-size: 12px;">Bạn có thể để trống hoặc nhập lý do tham gia của mình</small>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="closeRegisterModal()" class="btn btn-secondary">Hủy</button>
                    <button type="submit" class="btn btn-primary">Xác nhận tham gia</button>
                </div>
            </form>
        </div>
    </div>

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
            event.target.classList.add('active');
        }

        function openRegisterModal() {
            document.getElementById('registerModal').style.display = 'flex';
        }

        function closeRegisterModal() {
            document.getElementById('registerModal').style.display = 'none';
            document.getElementById('reason').value = '';
        }

        // Đóng modal khi click bên ngoài
        document.getElementById('registerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRegisterModal();
            }
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
    </script>
</body>
</html>

