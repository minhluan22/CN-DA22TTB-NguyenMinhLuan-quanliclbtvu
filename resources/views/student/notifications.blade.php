<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thông báo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        .notification-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .notification-item {
            background: var(--card);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.08);
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .notification-title {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-dark);
            margin: 0;
        }
        .notification-time {
            font-size: 13px;
            color: var(--muted);
        }
        .notification-message {
            color: var(--text-dark);
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }
        .notification-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
        }
        .status-approved {
            background: #dcfce7;
            color: #166534;
        }
        .status-rejected {
            background: #FFF3A0;
            color: #B84A5F;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }
        .empty-state-text {
            color: var(--muted);
            font-size: 16px;
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
                <h1>📬 Thông báo</h1>
            </div>

            <div class="profile-header">
                <div class="avatar-large">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        {{ strtoupper(substr($user->name ?? 'SV', 0, 1)) }}
                    @endif
                </div>
                <div class="profile-info">
                    <h2>{{ $user->name ?? 'Sinh viên' }}</h2>
                    <div class="meta">
                        MSSV: {{ $user->student_code ?? '---' }} | Email: {{ $user->email ?? '---' }}
                    </div>
                </div>
            </div>

            @if($notifications->count() > 0)
                <div class="notification-list">
                    @foreach($notifications as $notification)
                        <div class="notification-item">
                            <div class="notification-header">
                                <h3 class="notification-title">{{ $notification->title }}</h3>
                                <span class="notification-time">
                                    {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <p class="notification-message">{{ $notification->message }}</p>
                            <span class="notification-status status-{{ $notification->status }}">
                                {{ $notification->status === 'approved' ? 'Đã duyệt' : 
                                   ($notification->status === 'rejected' ? 'Từ chối' : 'Chờ duyệt') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p class="empty-state-text">Chưa có thông báo nào</p>
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
