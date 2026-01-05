<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} - Chi tiết hoạt động</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #072C6A;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF3A0;
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
            padding-top: 88px;
            width: calc(100% - 240px);
            max-width: 100%;
            flex: 1;
            transition: margin-left 0.3s ease, width 0.3s ease;
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
        }
        .event-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 24px;
        }
        .event-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            font-size: 16px;
        }
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .detail-card h4 {
            color: var(--primary-blue);
            margin-bottom: 16px;
            font-weight: 700;
        }
        .btn-back {
            background: var(--primary-blue);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: background 0.2s;
        }
        .btn-back:hover {
            background: var(--primary-blue-hover);
            color: white;
        }
        .btn-register {
            background: var(--accent-yellow);
            color: var(--text-dark);
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-register:hover {
            background: #ffd700;
        }
        .participant-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 16px;
        }
        .participant-item {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            border-left: 4px solid var(--primary-blue);
        }
        .badge-status {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }
        .badge-upcoming {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-ongoing {
            background: #dcfce7;
            color: #166534;
        }
        .badge-finished {
            background: #FFF3A0;
            color: #B84A5F;
        }
        
        @media (max-width: 900px) {
            .event-header {
                padding: 24px;
            }
            .event-title {
                font-size: 24px;
            }
            .event-meta {
                flex-direction: column;
                gap: 12px;
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
        <a href="{{ route('student.activities') }}?{{ request()->getQueryString() }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Event Header -->
        <div class="event-header">
            <h1 class="event-title">{{ $event->title }}</h1>
            <div class="event-meta">
                <div class="event-meta-item">
                    <i class="bi bi-building"></i>
                    <span>{{ $event->club_name }} ({{ $event->club_code }})</span>
                </div>
                <div class="event-meta-item">
                    <i class="bi bi-calendar"></i>
                    <span>{{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}</span>
                </div>
                @if($event->end_at)
                <div class="event-meta-item">
                    <i class="bi bi-calendar-check"></i>
                    <span>Kết thúc: {{ \Carbon\Carbon::parse($event->end_at)->format('d/m/Y H:i') }}</span>
                </div>
                @endif
                @if($event->location)
                <div class="event-meta-item">
                    <i class="bi bi-geo-alt"></i>
                    <span>{{ $event->location }}</span>
                </div>
                @endif
                <div class="event-meta-item">
                    <i class="bi bi-people"></i>
                    <span>{{ $participantCount }} người tham gia</span>
                </div>
            </div>
            <div class="mt-3">
                @if($event->status === 'upcoming')
                    <span class="badge-status badge-upcoming">Sắp diễn ra</span>
                @elseif($event->status === 'ongoing')
                    <span class="badge-status badge-ongoing">Đang diễn ra</span>
                @elseif($event->status === 'finished')
                    <span class="badge-status badge-finished">Đã kết thúc</span>
                @endif
            </div>
        </div>

        <!-- Description -->
        @if($event->description)
        <div class="detail-card">
            <h4><i class="bi bi-file-text"></i> Mô tả</h4>
            <div style="color: var(--muted); line-height: 1.8; white-space: pre-wrap;">{{ $event->description }}</div>
        </div>
        @endif

        <!-- Registration Section -->
        @if($user)
            @if($event->status === 'upcoming' || $event->status === 'ongoing')
                <div class="detail-card">
                    <h4><i class="bi bi-person-plus"></i> Đăng ký tham gia</h4>
                    @if(!$isMember && !$hasClubRegistration)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Bạn cần là thành viên của CLB này để đăng ký tham gia hoạt động.
                            <div style="margin-top: 12px;">
                                <a href="{{ route('student.club-public-detail', $event->club_id) }}" class="btn btn-primary" style="text-decoration: none; padding: 8px 16px; display: inline-block;">
                                    <i class="bi bi-box-arrow-in-right"></i> Đăng ký tham gia CLB
                                </a>
                            </div>
                        </div>
                    @elseif(!$isMember && $hasClubRegistration)
                        <div class="alert alert-info">
                            <i class="bi bi-clock-history"></i> Bạn đã đăng ký tham gia CLB này. Vui lòng chờ phê duyệt để có thể đăng ký hoạt động.
                        </div>
                    @elseif($userRegistration)
                        @if($userRegistration->status === 'pending')
                            <div class="alert alert-info">
                                <i class="bi bi-clock-history"></i> Bạn đã đăng ký tham gia. Đang chờ phê duyệt.
                            </div>
                        @elseif($userRegistration->status === 'approved')
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> Đăng ký của bạn đã được duyệt. Hẹn gặp tại hoạt động!
                            </div>
                        @elseif($userRegistration->status === 'rejected')
                            <div class="alert alert-danger">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-x-circle me-2" style="font-size: 20px; margin-top: 2px;"></i>
                                    <div>
                                        <strong>Đăng ký của bạn đã bị từ chối.</strong>
                                        @if(!empty($userRegistration->notes))
                                            <div class="mt-2" style="margin-top: 8px;">
                                                <strong>Lý do:</strong>
                                                <div style="background: rgba(255,255,255,0.3); padding: 10px; border-radius: 6px; margin-top: 6px;">
                                                    {{ $userRegistration->notes }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <form action="{{ route('student.register-event', $event->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-register">
                                <i class="bi bi-person-plus"></i> Đăng ký tham gia
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        @else
            <div class="detail-card">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đăng ký tham gia hoạt động.
                </div>
            </div>
        @endif

        <!-- Participants List -->
        @if($participants->count() > 0)
        <div class="detail-card">
            <h4><i class="bi bi-people"></i> Danh sách người tham gia ({{ $participants->count() }})</h4>
            <div class="participant-list">
                @foreach($participants as $participant)
                    <div class="participant-item">
                        <div style="font-weight: 600; margin-bottom: 4px;">{{ $participant->name }}</div>
                        <div style="color: var(--muted); font-size: 14px;">MSSV: {{ $participant->student_code }}</div>
                        @if($participant->activity_points > 0)
                            <div style="color: var(--primary-blue); font-size: 14px; margin-top: 4px;">
                                <i class="bi bi-star-fill"></i> {{ $participant->activity_points }} điểm
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        </main>
    </div>

    @include('student.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

