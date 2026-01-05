<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết đề xuất hoạt động</title>
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

        .detail-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .detail-section {
            margin-bottom: 24px;
        }
        .detail-section:last-child {
            margin-bottom: 0;
        }
        .detail-section h4 {
            color: var(--primary);
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--accent-yellow);
        }
        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            width: 200px;
            color: var(--text-dark);
        }
        .detail-value {
            flex: 1;
        }
        .badge-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .activity-type {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .type-academic {
            background: #e3f2fd;
            color: #1976d2;
        }
        .type-arts {
            background: #fce4ec;
            color: #c2185b;
        }
        .type-volunteer {
            background: #e8f5e9;
            color: #388e3c;
        }
        .type-other {
            background: #fff3e0;
            color: #f57c00;
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
            <div style="margin-bottom: 24px;">
                <a href="{{ route('student.club-detail', $proposal->club_id) }}#proposals" class="btn btn-secondary" onclick="localStorage.setItem('activeTab', 'proposals');">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="detail-card">
                <h2 style="color: var(--primary); margin-bottom: 24px;">
                    <i class="bi bi-lightbulb"></i> Chi tiết đề xuất hoạt động
                </h2>

                {{-- THÔNG TIN CHUNG --}}
                <div class="detail-section">
                    <h4>1. Thông tin chung</h4>
                    <div class="detail-row">
                        <div class="detail-label">Tên hoạt động:</div>
                        <div class="detail-value"><strong>{{ $proposal->title }}</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">CLB:</div>
                        <div class="detail-value">{{ $proposal->club_name }} ({{ $proposal->club_code }})</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Loại hoạt động:</div>
                        <div class="detail-value">
                            @if($proposal->activity_type == 'academic')
                                <span class="activity-type type-academic">Học thuật</span>
                            @elseif($proposal->activity_type == 'arts')
                                <span class="activity-type type-arts">Văn nghệ</span>
                            @elseif($proposal->activity_type == 'volunteer')
                                <span class="activity-type type-volunteer">Tình nguyện</span>
                            @else
                                <span class="activity-type type-other">Khác</span>
                            @endif
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Mục tiêu:</div>
                        <div class="detail-value">{{ $proposal->goal ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Nội dung chi tiết:</div>
                        <div class="detail-value">{{ $proposal->description ?? 'N/A' }}</div>
                    </div>
                </div>

                {{-- KẾ HOẠCH --}}
                <div class="detail-section">
                    <h4>2. Kế hoạch</h4>
                    <div class="detail-row">
                        <div class="detail-label">Thời gian dự kiến:</div>
                        <div class="detail-value">
                            @if($proposal->start_at)
                                Từ: {{ \Carbon\Carbon::parse($proposal->start_at)->format('d/m/Y H:i') }}
                                @if($proposal->end_at)
                                    <br>Đến: {{ \Carbon\Carbon::parse($proposal->end_at)->format('d/m/Y H:i') }}
                                @endif
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Địa điểm dự kiến:</div>
                        <div class="detail-value">{{ $proposal->location ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Số lượng dự kiến:</div>
                        <div class="detail-value">{{ $proposal->expected_participants ?? 'N/A' }} người</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Kinh phí dự kiến:</div>
                        <div class="detail-value">
                            @if($proposal->expected_budget)
                                {{ number_format($proposal->expected_budget, 0, ',', '.') }} VNĐ
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>

                {{-- THÔNG TIN XỬ LÝ --}}
                <div class="detail-section">
                    <h4>3. Thông tin xử lý</h4>
                    <div class="detail-row">
                        <div class="detail-label">Trạng thái hiện tại:</div>
                        <div class="detail-value">
                            @if($proposal->approval_status == 'pending')
                                <span class="badge-status status-pending">
                                    <i class="bi bi-clock"></i> Chờ duyệt
                                </span>
                            @elseif($proposal->approval_status == 'approved')
                                <span class="badge-status status-approved">
                                    <i class="bi bi-check-circle"></i> Đã duyệt
                                </span>
                            @elseif($proposal->approval_status == 'rejected')
                                <span class="badge-status status-rejected">
                                    <i class="bi bi-x-circle"></i> Bị từ chối
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($approver)
                        <div class="detail-row">
                            <div class="detail-label">Người duyệt:</div>
                            <div class="detail-value">
                                {{ $approver->name }}
                                @if($approver->student_code)
                                    ({{ $approver->student_code }})
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($proposal->updated_at && in_array($proposal->approval_status, ['approved', 'rejected']))
                        <div class="detail-row">
                            <div class="detail-label">Thời gian {{ $proposal->approval_status == 'approved' ? 'duyệt' : 'từ chối' }}:</div>
                            <div class="detail-value">{{ \Carbon\Carbon::parse($proposal->updated_at)->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif
                    @if($proposal->approval_status == 'rejected' && $proposal->violation_notes)
                        <div class="detail-row">
                            <div class="detail-label">Lý do từ chối:</div>
                            <div class="detail-value">
                                <div class="alert alert-danger" style="margin: 0;">
                                    {{ $proposal->violation_notes }}
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-label">Thời gian gửi:</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($proposal->created_at)->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                {{-- FILE ĐÍNH KÈM --}}
                @if($proposal->attachment)
                    <div class="detail-section">
                        <h4>4. File đính kèm</h4>
                        <div class="detail-row">
                            <div class="detail-label">File:</div>
                            <div class="detail-value">
                                <a href="{{ asset('storage/' . $proposal->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-download"></i> Tải xuống
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- NÚT HÀNH ĐỘNG --}}
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e9ecef;">
                    @if($proposal->approval_status == 'approved' && $proposal->status == 'upcoming')
                        <a href="{{ route('student.activity-detail', $proposal->id) }}" class="btn btn-success">
                            <i class="bi bi-calendar-event"></i> Xem hoạt động chính thức
                        </a>
                    @endif
                    @if($proposal->approval_status == 'rejected')
                        <a href="{{ route('student.propose-event', ['club_id' => $proposal->club_id]) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tạo đề xuất mới
                        </a>
                    @endif
                </div>
            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
