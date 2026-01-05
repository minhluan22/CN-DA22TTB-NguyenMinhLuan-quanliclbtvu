<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cài đặt tài khoản</title>
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
            width: calc(100% - 240px);
            max-width: 100%;
            flex: 1;
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .header {
            background: var(--card);
            padding: 20px 24px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .form-container {
            background: var(--card);
            padding: 32px;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background: var(--primary-blue);
            color: var(--text-light);
            border: none;
            padding: 12px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: var(--primary-blue-hover);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
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
    

    
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <div class="body-wrapper">
        @include('student.sidebar')

        <main class="content">
            <div class="header">
                <h1>⚙️ Cài đặt tài khoản</h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-container">
                <!-- Tabs -->
                <div class="settings-tabs">
                    <button class="tab-btn active" onclick="showSettingsTab('security')">🔒 Bảo mật</button>
                    <button class="tab-btn" onclick="showSettingsTab('notifications')">🔔 Thông báo</button>
                    <button class="tab-btn" onclick="showSettingsTab('general')">⚙️ Tùy chọn chung</button>
                </div>

                <!-- Tab: Bảo mật -->
                <div id="tab-security" class="settings-tab-content active">
                    <form action="{{ route('student.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="settings_type" value="security">
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="two_factor_enabled" value="1" {{ old('two_factor_enabled', $user->two_factor_enabled ?? false) ? 'checked' : '' }}>
                                Bật xác thực hai bước (2FA)
                            </label>
                            <small style="color: var(--muted); font-size: 13px; display: block; margin-top: 4px;">
                                Tăng cường bảo mật bằng mã xác thực từ ứng dụng
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Thiết bị đã đăng nhập</label>
                            <div style="background: #f3f4f6; padding: 12px; border-radius: 8px; font-size: 14px; color: var(--muted);">
                                Hiện tại: {{ request()->ip() }} - {{ request()->userAgent() }}
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn-danger" onclick="if(confirm('Bạn có chắc muốn đăng xuất khỏi tất cả thiết bị?')) { document.getElementById('logout-all-form').submit(); }">
                                🚪 Đăng xuất khỏi tất cả thiết bị
                            </button>
                        </div>

                        <button type="submit" class="btn-submit">💾 Lưu cài đặt bảo mật</button>
                    </form>
                    
                    <form id="logout-all-form" action="{{ route('student.logout-all') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>

                <!-- Tab: Thông báo -->
                <div id="tab-notifications" class="settings-tab-content">
                    <form action="{{ route('student.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="settings_type" value="notifications">
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="email_notifications" value="1" {{ old('email_notifications', $user->email_notifications ?? true) ? 'checked' : '' }}>
                                Nhận email từ hệ thống
                            </label>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="event_notifications" value="1" {{ old('event_notifications', $user->event_notifications ?? true) ? 'checked' : '' }}>
                                Thông báo sự kiện
                            </label>
                            <small style="color: var(--muted); font-size: 13px; display: block; margin-top: 4px;">
                                Nhận thông báo khi có sự kiện mới hoặc sắp diễn ra
                            </small>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="club_notifications" value="1" {{ old('club_notifications', $user->club_notifications ?? true) ? 'checked' : '' }}>
                                Thông báo từ CLB
                            </label>
                            <small style="color: var(--muted); font-size: 13px; display: block; margin-top: 4px;">
                                Nhận thông báo từ các CLB bạn đang tham gia
                            </small>
                        </div>

                        <button type="submit" class="btn-submit">💾 Lưu cài đặt thông báo</button>
                    </form>
                </div>

                <!-- Tab: Tùy chọn chung -->
                <div id="tab-general" class="settings-tab-content">
                    <form action="{{ route('student.settings.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="settings_type" value="general">
                        
                        <div class="form-group">
                            <label>Ngôn ngữ</label>
                            <select name="language">
                                <option value="vi" {{ old('language', $user->language ?? 'vi') === 'vi' ? 'selected' : '' }}>Tiếng Việt</option>
                                <option value="en" {{ old('language', $user->language ?? 'vi') === 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="dark_mode" value="1" {{ old('dark_mode', $user->dark_mode ?? false) ? 'checked' : '' }} onchange="toggleDarkModeFromSettings(this)">
                                Chế độ tối (Dark Mode)
                            </label>
                            <small style="color: var(--muted); font-size: 13px; display: block; margin-top: 4px;">
                                Chuyển đổi giao diện sang chế độ tối
                            </small>
                        </div>

                        <button type="submit" class="btn-submit">💾 Lưu tùy chọn</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    @include('student.footer')

    <script>
        function closeSidebarOnClick() {
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

