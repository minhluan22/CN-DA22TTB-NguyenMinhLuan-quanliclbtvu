<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Danh Sách CLB</title>
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
            justify-content: space-between;
            gap: 20px;
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
        .filters {
            background: var(--card);
            padding: 20px 24px;
            border-radius: 16px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
        .filter-group input,
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
        }
        .club-description {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 16px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
            min-height: 40px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .badge.success { background: #dcfce7; color: #166534; }
        .badge.warning { background: var(--soft-yellow); color: var(--text-dark); }
        .badge.info { background: #dbeafe; color: #1d4ed8; }
        
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
        .btn-access.member {
            background: #10b981;
        }
        .btn-access.member:hover {
            background: #5FB84A;
        }
        .btn-access.pending {
            background: #f59e0b;
        }
        .btn-access.pending:hover {
            background: #FFE600;
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
            <h1>📋 Danh Sách CLB</h1>
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

        <div class="filters">
            <form method="GET" action="{{ route('student.all-clubs') }}">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Tìm kiếm theo tên CLB</label>
                        <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Nhập tên CLB...">
                    </div>
                    <div class="filter-group">
                        <label>Lọc theo Khoa/Bộ môn</label>
                        <select name="field">
                            <option value="">Tất cả</option>
                            @foreach($fields as $f)
                                <option value="{{ $f }}" {{ $field === $f ? 'selected' : '' }}>
                                    {{ \App\Models\Club::getFieldDisplay($f) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Số thành viên tối thiểu</label>
                        <input type="number" name="min_members" value="{{ $minMembers }}" placeholder="Ví dụ: 10" min="0">
                    </div>
                </div>
                <button type="submit" class="btn-filter">🔍 Tìm kiếm</button>
                @if($keyword || $field || $minMembers)
                    <a href="{{ route('student.all-clubs') }}" style="margin-left: 8px; color: var(--muted); text-decoration: none;">Xóa bộ lọc</a>
                @endif
            </form>
        </div>

        @if($clubs->count() > 0)
            <div class="club-grid">
                @foreach($clubs as $club)
                    <div class="club-card" id="club-{{ $club->id }}">
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

                        <div class="club-description">
                            {{ $club->description ?? 'Chưa có mô tả.' }}
                        </div>

                        <div class="club-stats">
                            <span>👥 {{ $club->member_count }} thành viên</span>
                            <span>🎉 {{ $club->event_count }} sự kiện</span>
                            <span>📚 {{ \App\Models\Club::getFieldDisplay($club->club_type ?? $club->field) }}</span>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <span class="badge success">Đang hoạt động</span>
                        </div>

                        @php
                            $isMember = in_array($club->id, $userClubIds);
                            $hasRegistration = in_array($club->id, $userRegistrationIds);
                        @endphp

                        @if($isMember)
                            <a href="{{ route('student.club-detail', $club->id) }}?{{ http_build_query(request()->query()) }}" class="btn-access member">✅ Bạn là thành viên</a>
                        @elseif($hasRegistration)
                            <a href="{{ route('student.club-public-detail', $club->id) }}?{{ http_build_query(request()->query()) }}" class="btn-access pending">⏳ Đang chờ phê duyệt</a>
                        @else
                            <a href="{{ route('student.club-public-detail', $club->id) }}?{{ http_build_query(request()->query()) }}" class="btn-access">Xem chi tiết</a>
                        @endif
                    </div>
                @endforeach
            </div>
            
            {{-- PHÂN TRANG --}}
            @if($clubs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $clubs->links('vendor.pagination.custom') }}
                </div>
            @endif
        @else
            <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
                <div style="font-size: 64px; margin-bottom: 16px;">📋</div>
                <h3>Không tìm thấy CLB nào</h3>
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

        // Scroll về vị trí club khi quay lại từ trang chi tiết
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const clubIdFromUrl = window.location.pathname.split('/').pop();
            
            // Lấy club_id từ referrer nếu quay lại từ trang chi tiết
            const referrer = document.referrer;
            if (referrer && (referrer.includes('/club-public/') || referrer.includes('/club-detail/'))) {
                const clubIdMatch = referrer.match(/\/(club-public|club-detail)\/(\d+)/);
                if (clubIdMatch) {
                    const clubId = clubIdMatch[2];
                    const clubCard = document.getElementById('club-' + clubId);
                    
                    if (clubCard) {
                        setTimeout(() => {
                            clubCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            clubCard.style.boxShadow = '0 0 0 3px #FFE600';
                            setTimeout(() => {
                                clubCard.style.boxShadow = '';
                            }, 2000);
                        }, 100);
                    }
                }
            }
        });
    </script>
</body>
</html>

