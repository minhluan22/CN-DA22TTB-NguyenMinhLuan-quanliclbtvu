@php
    $user = Auth::user();
    $route = request()->route();
    $currentRoute = $route ? $route->getName() : '';
    
    // Kiểm tra xem có phải route của chairman không - CHỈ khi route BẮT ĐẦU bằng 'student.chairman.'
    // Đảm bảo logic nhất quán: chỉ hiển thị menu chairman khi đang ở trang chairman
    $isChairmanRoute = false;
    if (!empty($currentRoute) && is_string($currentRoute)) {
        $isChairmanRoute = strpos($currentRoute, 'student.chairman.') === 0;
    }
    
    // Xác định menu nào đang active - Logic chung cho cả student và chairman
    $activeMenu = '';
    
    if ($isChairmanRoute) {
        // Logic cho chairman routes
        if ($currentRoute === 'student.chairman.dashboard') {
            $activeMenu = 'dashboard';
        } elseif (strpos($currentRoute, 'student.chairman.manage-members') === 0) {
            $activeMenu = 'manage-members';
        } elseif (strpos($currentRoute, 'student.chairman.manage-registrations') === 0) {
            $activeMenu = 'manage-registrations';
        } elseif (strpos($currentRoute, 'student.chairman.manage-positions') === 0) {
            $activeMenu = 'manage-positions';
        } elseif (strpos($currentRoute, 'student.chairman.create-event') === 0 || 
                  strpos($currentRoute, 'student.chairman.pending-events') === 0 ||
                  strpos($currentRoute, 'student.chairman.approved-events') === 0 ||
                  strpos($currentRoute, 'student.chairman.pending-registrations') === 0 ||
                  strpos($currentRoute, 'student.chairman.approved-participants') === 0) {
            $activeMenu = 'events';
        } elseif (strpos($currentRoute, 'student.chairman.approve-proposals') === 0 ||
                  strpos($currentRoute, 'student.chairman.event-proposals') === 0) {
            $activeMenu = 'proposals';
        } elseif (strpos($currentRoute, 'student.chairman.approve-activities') === 0 ||
                  strpos($currentRoute, 'student.chairman.activity-points-history') === 0) {
            $activeMenu = 'activities';
        } elseif (strpos($currentRoute, 'student.chairman.club-info') === 0) {
            $activeMenu = 'club-info';
        } elseif (strpos($currentRoute, 'student.chairman.notifications') === 0) {
            $activeMenu = 'notifications';
        } elseif (strpos($currentRoute, 'student.chairman.support') === 0) {
            $activeMenu = 'support';
        } elseif (strpos($currentRoute, 'student.chairman.regulations') === 0 ||
                  strpos($currentRoute, 'student.chairman.violations') === 0) {
            $activeMenu = 'regulations';
        } elseif (strpos($currentRoute, 'student.chairman.discipline-history') === 0) {
            $activeMenu = 'discipline-history';
        } elseif (strpos($currentRoute, 'student.chairman.statistics') === 0) {
            $activeMenu = 'statistics';
        }
    } else {
        // Logic cho student routes
        if ($currentRoute === 'student.home') {
            $activeMenu = 'home';
        } elseif ($currentRoute === 'student.all-clubs' || $currentRoute === 'student.club-public-detail') {
            $activeMenu = 'all-clubs';
        } elseif ($currentRoute === 'student.my-clubs' || $currentRoute === 'student.club-detail') {
            $activeMenu = 'my-clubs';
        } elseif ($currentRoute === 'student.activities' || $currentRoute === 'student.activity-detail') {
            $activeMenu = 'activities';
        } elseif ($currentRoute === 'student.profile' || $currentRoute === 'student.propose-event' || 
                  $currentRoute === 'student.change-password' || $currentRoute === 'student.change-password.post') {
            $activeMenu = 'profile';
        } elseif (strpos($currentRoute, 'student.personal-statistics') === 0) {
            $activeMenu = 'personal-statistics';
        } elseif (strpos($currentRoute, 'student.support') === 0) {
            $activeMenu = 'support';
        }
    }
    
    // Kiểm tra user có phải chairman không
    $chairmanClub = null;
    if ($user) {
        $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
    }
@endphp

<aside class="sidebar">
    <button class="sidebar-toggle-btn" onclick="toggleSidebar()" title="Đóng menu">
        <span class="toggle-icon">☰</span>
        <span class="toggle-icon close-icon" style="display: none;">✕</span>
    </button>

    <!-- MENU -->
    <nav class="nav">
        @if($isChairmanRoute)
            {{-- MENU CHO CHỦ NHIỆM CLB --}}
            <!-- Trang Chủ SV -->
            <a href="{{ route('student.home') }}" class="menu-item" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-house-door"></i> Trang Chủ SV</span>
            </a>

            <!-- Dashboard -->
            <a href="{{ route('student.chairman.dashboard') }}" class="menu-item {{ $activeMenu === 'dashboard' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-speedometer2"></i> Dashboard</span>
            </a>

            <!-- Quản lý thành viên -->
            <a href="{{ route('student.chairman.manage-members') }}" class="menu-item {{ $activeMenu === 'manage-members' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-people"></i> Quản lý thành viên</span>
            </a>

            <!-- Quản lý đơn đăng ký -->
            <a href="{{ route('student.chairman.manage-registrations') }}" class="menu-item {{ $activeMenu === 'manage-registrations' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-file-earmark-text"></i> Đơn đăng ký</span>
            </a>

            <!-- Gán chức vụ -->
            <a href="{{ route('student.chairman.manage-positions') }}" class="menu-item {{ $activeMenu === 'manage-positions' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-person-badge"></i> Gán chức vụ</span>
            </a>

            <!-- Hoạt động CLB -->
            <a href="javascript:void(0)" class="menu-item" onclick="toggleMenu('events-menu'); showScrollbarOnMenuClick();">
                <span><i class="bi bi-calendar-event"></i> Hoạt động CLB</span>
                <i class="bi bi-chevron-right"></i>
            </a>
            <div id="events-menu" class="submenu">
                <a href="{{ route('student.chairman.create-event') }}" class="submenu-link {{ strpos($currentRoute, 'student.chairman.create-event') === 0 ? 'active-sub' : '' }}" onclick="closeSidebarOnClick()">
                    Tạo hoạt động
                </a>
                <a href="{{ route('student.chairman.pending-events') }}" class="submenu-link {{ strpos($currentRoute, 'student.chairman.pending-events') === 0 ? 'active-sub' : '' }}" onclick="closeSidebarOnClick()">
                    Chờ duyệt
                </a>
                <a href="{{ route('student.chairman.approved-events') }}" class="submenu-link {{ strpos($currentRoute, 'student.chairman.approved-events') === 0 ? 'active-sub' : '' }}" onclick="closeSidebarOnClick()">
                    Đã duyệt
                </a>
                <a href="{{ route('student.chairman.pending-registrations') }}" class="submenu-link {{ strpos($currentRoute, 'student.chairman.pending-registrations') === 0 ? 'active-sub' : '' }}" onclick="closeSidebarOnClick()">
                    Đăng ký chờ duyệt
                </a>
                <a href="{{ route('student.chairman.approved-participants') }}" class="submenu-link {{ strpos($currentRoute, 'student.chairman.approved-participants') === 0 ? 'active-sub' : '' }}" onclick="closeSidebarOnClick()">
                    Người tham gia
                </a>
            </div>

            <!-- Duyệt đề xuất -->
            <a href="{{ route('student.chairman.approve-proposals') }}" class="menu-item {{ $activeMenu === 'proposals' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-check-circle"></i> Duyệt đề xuất</span>
            </a>

            <!-- Duyệt hoạt động -->
            <a href="{{ route('student.chairman.approve-activities') }}" class="menu-item {{ $activeMenu === 'activities' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-clipboard-check"></i> Duyệt hoạt động</span>
            </a>

            <!-- Thông báo -->
            <a href="{{ route('student.chairman.notifications.inbox') }}" class="menu-item {{ $activeMenu === 'notifications' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-bell"></i> Thông báo</span>
            </a>

            <!-- Hỗ trợ -->
            <a href="{{ route('student.chairman.support.index') }}" class="menu-item {{ $activeMenu === 'support' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-headset"></i> Hỗ trợ</span>
            </a>

            <!-- Nội quy - Vi phạm -->
            <a href="{{ route('student.chairman.regulations.index') }}" class="menu-item {{ $activeMenu === 'regulations' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-shield-exclamation"></i> Nội quy - Vi phạm</span>
            </a>

            <!-- Lịch sử kỷ luật -->
            <a href="{{ route('student.chairman.discipline-history.by-time') }}" class="menu-item {{ $activeMenu === 'discipline-history' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-clock-history"></i> Lịch sử kỷ luật</span>
            </a>

            <!-- Thống kê -->
            <a href="{{ route('student.chairman.statistics') }}" class="menu-item {{ $activeMenu === 'statistics' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-graph-up"></i> Thống kê</span>
            </a>

            <!-- Thông tin CLB -->
            <a href="{{ route('student.chairman.club-info') }}" class="menu-item {{ $activeMenu === 'club-info' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-info-circle"></i> Thông tin CLB</span>
            </a>
        @else
            {{-- MENU CHO SINH VIÊN THÔNG THƯỜNG --}}
            <!-- Trang chủ -->
            <a href="{{ route('student.home') }}" class="menu-item {{ $activeMenu === 'home' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-house-door"></i> Trang chủ</span>
            </a>

            <!-- Danh sách CLB -->
            <a href="{{ route('student.all-clubs') }}" class="menu-item {{ $activeMenu === 'all-clubs' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-list-ul"></i> Danh sách CLB</span>
            </a>

            <!-- CLB của tôi -->
            <a href="{{ route('student.my-clubs') }}" class="menu-item {{ $activeMenu === 'my-clubs' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-star"></i> CLB của tôi</span>
            </a>

            <!-- Hoạt động CLB -->
            <a href="{{ route('student.activities') }}" class="menu-item {{ $activeMenu === 'activities' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-calendar-event"></i> Hoạt động CLB</span>
            </a>

            <!-- THỐNG KÊ - CÁ NHÂN -->
            <a href="{{ route('student.personal-statistics') }}" class="menu-item {{ $activeMenu === 'personal-statistics' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-graph-up"></i> THỐNG KÊ - CÁ NHÂN</span>
            </a>

            <!-- Hỗ trợ -->
            <a href="{{ route('student.support.index') }}" class="menu-item {{ $activeMenu === 'support' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-headset"></i> Hỗ trợ</span>
            </a>

            <!-- Hồ Sơ Cá Nhân -->
            <a href="{{ route('student.profile') }}" class="menu-item {{ $activeMenu === 'profile' ? 'active' : '' }}" onclick="closeSidebarOnClick()">
                <span><i class="bi bi-person-circle"></i> Hồ Sơ Cá Nhân</span>
            </a>
        @endif
    </nav>

    <!-- LOGOUT -->
    <div class="logout">
        <form action="{{ route('logout') }}" method="POST">
        @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Đăng xuất</span>
        </button>
    </form>
    </div>
</aside>

<style>
.sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    background: var(--primary-blue, #0B3D91);
    color: var(--text-light, #ffffff);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 998;
    transition: transform 0.3s ease;
    box-sizing: border-box;
    padding: 0;
    max-height: 100vh;
}

.sidebar-collapsed {
    transform: translateX(-100%);
}

/* Toggle Button */
.sidebar-toggle-btn {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.3s ease;
    z-index: 100;
    padding: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.sidebar-toggle-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: var(--accent-yellow, #FFE600);
    transform: scale(1.1);
}

.sidebar-toggle-btn .toggle-icon {
    display: block;
    line-height: 1;
}

.sidebar-toggle-btn .close-icon {
    font-size: 20px;
    font-weight: bold;
}

@media (max-width: 900px) {
    .sidebar-toggle-btn {
        display: none;
    }
}

/* Menu Items - Scrollable Menu (Giống admin sidebar) - Override tất cả CSS khác */
/* Sử dụng specificity cao nhất để override CSS inline từ các file khác */
aside.sidebar > nav.nav,
.sidebar > nav.nav,
aside.sidebar nav.nav,
.sidebar nav.nav,
aside.sidebar .nav,
.sidebar .nav,
body aside.sidebar .nav,
body .sidebar .nav,
nav.nav {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    min-height: 0 !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 16px !important;
    padding-top: 88px !important;
    padding-right: 8px !important;
    padding-bottom: 0 !important;
    gap: 0 !important;
    scrollbar-width: thin !important;
    scrollbar-color: rgba(255, 255, 255, 0.5) rgba(255, 255, 255, 0.15) !important;
    /* Đảm bảo có chiều cao để scroll hoạt động */
    height: 0 !important;
    max-height: none !important;
}
/* Hiển thị scrollbar khi hover vào sidebar hoặc khi click vào menu */
aside.sidebar:hover > nav.nav,
.sidebar:hover > nav.nav,
aside.sidebar:hover nav.nav,
.sidebar:hover nav.nav,
aside.sidebar:hover .nav,
.sidebar:hover .nav,
body aside.sidebar:hover .nav,
body .sidebar:hover .nav,
nav.nav:hover,
aside.sidebar.nav-scrolling > nav.nav,
.sidebar.nav-scrolling > nav.nav,
aside.sidebar.nav-scrolling nav.nav,
.sidebar.nav-scrolling nav.nav,
aside.sidebar.nav-scrolling .nav,
.sidebar.nav-scrolling .nav,
body aside.sidebar.nav-scrolling .nav,
body .sidebar.nav-scrolling .nav,
aside.sidebar.show-scrollbar > nav.nav,
.sidebar.show-scrollbar > nav.nav,
aside.sidebar.show-scrollbar nav.nav,
.sidebar.show-scrollbar nav.nav,
aside.sidebar.show-scrollbar .nav,
.sidebar.show-scrollbar .nav,
body aside.sidebar.show-scrollbar .nav,
body .sidebar.show-scrollbar .nav {
    scrollbar-color: rgba(255, 255, 255, 0.7) rgba(255, 255, 255, 0.2) !important;
    /* Force scrollbar hiển thị ngay cả khi nội dung chưa đủ dài */
    overflow-y: scroll !important;
}
aside.sidebar > nav.nav::-webkit-scrollbar,
.sidebar > nav.nav::-webkit-scrollbar,
aside.sidebar nav.nav::-webkit-scrollbar,
.sidebar nav.nav::-webkit-scrollbar,
aside.sidebar .nav::-webkit-scrollbar,
.sidebar .nav::-webkit-scrollbar,
body aside.sidebar .nav::-webkit-scrollbar,
body .sidebar .nav::-webkit-scrollbar,
nav.nav::-webkit-scrollbar {
    width: 8px !important;
    display: block !important;
}
aside.sidebar > nav.nav::-webkit-scrollbar-track,
.sidebar > nav.nav::-webkit-scrollbar-track,
aside.sidebar nav.nav::-webkit-scrollbar-track,
.sidebar nav.nav::-webkit-scrollbar-track,
aside.sidebar .nav::-webkit-scrollbar-track,
.sidebar .nav::-webkit-scrollbar-track,
body aside.sidebar .nav::-webkit-scrollbar-track,
body .sidebar .nav::-webkit-scrollbar-track,
nav.nav::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.15) !important;
    border-radius: 4px;
}
aside.sidebar > nav.nav::-webkit-scrollbar-thumb,
.sidebar > nav.nav::-webkit-scrollbar-thumb,
aside.sidebar nav.nav::-webkit-scrollbar-thumb,
.sidebar nav.nav::-webkit-scrollbar-thumb,
aside.sidebar .nav::-webkit-scrollbar-thumb,
.sidebar .nav::-webkit-scrollbar-thumb,
body aside.sidebar .nav::-webkit-scrollbar-thumb,
body .sidebar .nav::-webkit-scrollbar-thumb,
nav.nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.4) !important;
    border-radius: 4px;
    transition: background 0.2s ease !important;
}
/* Scrollbar rõ hơn khi hover vào sidebar hoặc khi click menu */
aside.sidebar:hover > nav.nav::-webkit-scrollbar-thumb,
.sidebar:hover > nav.nav::-webkit-scrollbar-thumb,
aside.sidebar:hover nav.nav::-webkit-scrollbar-thumb,
.sidebar:hover nav.nav::-webkit-scrollbar-thumb,
aside.sidebar:hover .nav::-webkit-scrollbar-thumb,
.sidebar:hover .nav::-webkit-scrollbar-thumb,
body aside.sidebar:hover .nav::-webkit-scrollbar-thumb,
body .sidebar:hover .nav::-webkit-scrollbar-thumb,
nav.nav:hover::-webkit-scrollbar-thumb,
aside.sidebar.nav-scrolling > nav.nav::-webkit-scrollbar-thumb,
.sidebar.nav-scrolling > nav.nav::-webkit-scrollbar-thumb,
aside.sidebar.nav-scrolling nav.nav::-webkit-scrollbar-thumb,
.sidebar.nav-scrolling nav.nav::-webkit-scrollbar-thumb,
aside.sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb,
.sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb,
body aside.sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb,
body .sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb,
aside.sidebar.show-scrollbar > nav.nav::-webkit-scrollbar-thumb,
.sidebar.show-scrollbar > nav.nav::-webkit-scrollbar-thumb,
aside.sidebar.show-scrollbar nav.nav::-webkit-scrollbar-thumb,
.sidebar.show-scrollbar nav.nav::-webkit-scrollbar-thumb,
aside.sidebar.show-scrollbar .nav::-webkit-scrollbar-thumb,
.sidebar.show-scrollbar .nav::-webkit-scrollbar-thumb,
body aside.sidebar.show-scrollbar .nav::-webkit-scrollbar-thumb,
body .sidebar.show-scrollbar .nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.6) !important;
}
aside.sidebar > nav.nav::-webkit-scrollbar-thumb:hover,
.sidebar > nav.nav::-webkit-scrollbar-thumb:hover,
aside.sidebar nav.nav::-webkit-scrollbar-thumb:hover,
.sidebar nav.nav::-webkit-scrollbar-thumb:hover,
aside.sidebar .nav::-webkit-scrollbar-thumb:hover,
.sidebar .nav::-webkit-scrollbar-thumb:hover,
body aside.sidebar .nav::-webkit-scrollbar-thumb:hover,
body .sidebar .nav::-webkit-scrollbar-thumb:hover,
nav.nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.8) !important;
}

.menu-item {
    text-decoration: none;
    color: rgba(255, 255, 255, 0.9);
    padding: 12px 14px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    border-left: 3px solid transparent;
    /* Đảm bảo menu item không bị co lại */
    flex-shrink: 0;
}
/* Khi click vào menu item, hiển thị scrollbar rõ hơn */
.menu-item:active,
.menu-item:focus {
    outline: none;
}

.menu-item span {
    display: flex;
    align-items: center;
    gap: 10px;
}

.menu-item i {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

.menu-item .bi-chevron-right {
    transition: transform 0.3s ease;
}

.menu-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--accent-yellow, #FFE600);
    border-left-color: var(--accent-yellow, #FFE600);
    padding-left: 18px;
}

.menu-item.active {
    background: var(--accent-yellow, #FFE600);
    color: var(--primary-blue, #0B3D91);
    font-weight: 700;
    border-left-color: var(--primary-blue, #0B3D91);
    box-shadow: 0 4px 12px rgba(255, 230, 0, 0.3);
}

/* Submenu Styles */
.submenu {
    display: none;
    background: rgba(255, 255, 255, 0.05);
    padding: 4px 0;
    margin-left: 0;
    width: 100%;
}

.submenu-link {
    display: block;
    padding: 10px 20px 10px 50px;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    font-size: 13px;
    transition: all 0.3s ease;
}

.submenu-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--accent-yellow, #FFE600);
    padding-left: 55px;
}

.submenu-link.active-sub {
    background: var(--accent-yellow, #FFE600);
    color: var(--primary-blue, #0B3D91);
    font-weight: 600;
    border-left: 3px solid var(--primary-blue, #0B3D91);
}

/* Logout */
.logout {
    flex-shrink: 0;
    padding: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.15);
    margin-top: auto;
}

.logout-btn {
    background: #ef4444;
    color: #fff;
    border: none;
    padding: 12px 14px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    width: 100%;
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: center;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

/* Fixed toggle button (mobile) and overlay - centralized here */
.sidebar-toggle-fixed {
    position: fixed;
    top: 80px;
    left: 20px;
    z-index: 1001;
    background: var(--primary-blue, #0B3D91);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: var(--text-light, #ffffff);
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
    background: var(--primary-blue-hover, #0C4CB8);
    border-color: var(--accent-yellow, #FFE600);
    transform: scale(1.05);
}

body:not(.sidebar-closed) .sidebar-toggle-fixed {
    display: none;
}

body.sidebar-closed .sidebar-toggle-fixed {
    display: flex;
}

.sidebar-overlay {
    display: none;
}

@media (max-width: 900px) {
    .sidebar-toggle-btn {
        display: none;
    }
    /* When sidebar is open on mobile, show overlay */
    body.sidebar-open .sidebar-overlay {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 999;
    }
}

.sidebar:not(.sidebar-collapsed) {
    transform: translateX(0);
}
</style>

<!-- Fixed toggle and overlay (shared) -->
<button class="sidebar-toggle-fixed" onclick="toggleSidebar()" title="Mở menu">☰</button>
<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        const body = document.body;
        const overlay = document.querySelector('.sidebar-overlay');
        const fixedBtn = document.querySelector('.sidebar-toggle-fixed');
        const toggleBtn = document.querySelector('.sidebar-toggle-btn');

        if (sidebar.classList.contains('sidebar-collapsed')) {
            // Open
            sidebar.classList.remove('sidebar-collapsed');
            body.classList.remove('sidebar-closed');
            body.classList.add('sidebar-open');
            if (overlay) overlay.style.display = 'block';
            if (fixedBtn) fixedBtn.style.display = 'none';
            if (toggleBtn) {
                const hamburger = toggleBtn.querySelector('.toggle-icon:not(.close-icon)');
                const closeIcon = toggleBtn.querySelector('.close-icon');
                if (hamburger) hamburger.style.display = 'none';
                if (closeIcon) closeIcon.style.display = 'block';
            }
        } else {
            // Close
            sidebar.classList.add('sidebar-collapsed');
            body.classList.add('sidebar-closed');
            body.classList.remove('sidebar-open');
            if (overlay) overlay.style.display = 'none';
            if (fixedBtn) fixedBtn.style.display = 'flex';
            if (toggleBtn) {
                const hamburger = toggleBtn.querySelector('.toggle-icon:not(.close-icon)');
                const closeIcon = toggleBtn.querySelector('.close-icon');
                if (hamburger) hamburger.style.display = 'block';
                if (closeIcon) closeIcon.style.display = 'none';
            }
        }
    }

    function closeSidebarOnClick() {
        if (window.innerWidth < 900) {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar && !sidebar.classList.contains('sidebar-collapsed')) {
                toggleSidebar();
            }
        }
        // Hiển thị scrollbar khi click vào menu item
        showScrollbarOnMenuClick();
    }
    
    function showScrollbarOnMenuClick() {
        const nav = document.querySelector('.sidebar .nav');
        const sidebar = document.querySelector('.sidebar');
        if (nav && sidebar) {
            // Thêm class để hiển thị scrollbar rõ hơn
            sidebar.classList.add('show-scrollbar', 'nav-scrolling');
            
            // Force scrollbar hiển thị bằng cách đặt overflow-y: scroll
            const originalOverflow = nav.style.overflowY || window.getComputedStyle(nav).overflowY;
            nav.style.overflowY = 'scroll';
            
            // Force scrollbar hiển thị ngay lập tức
            const scrollHeight = nav.scrollHeight;
            const clientHeight = nav.clientHeight;
            const currentScroll = nav.scrollTop;
            
            // Luôn force scroll một chút để trigger scrollbar hiển thị
            if (scrollHeight > clientHeight) {
                // Nếu có nội dung scroll được, scroll một chút
                nav.scrollTop = currentScroll + 1;
                setTimeout(() => {
                    nav.scrollTop = currentScroll;
                }, 10);
            } else {
                // Nếu chưa có scroll, tạm thời thêm nội dung để tạo scroll
                const tempDiv = document.createElement('div');
                tempDiv.style.height = '1px';
                tempDiv.style.visibility = 'hidden';
                nav.appendChild(tempDiv);
                
                // Scroll xuống 1px
                nav.scrollTop = 1;
                
                setTimeout(() => {
                    nav.scrollTop = 0;
                    nav.removeChild(tempDiv);
                }, 100);
            }
            
            // Giữ scrollbar sáng trong 4 giây
            setTimeout(() => {
                sidebar.classList.remove('nav-scrolling');
            }, 4000);
            
            // Giữ scrollbar hiển thị (show-scrollbar) trong 8 giây để người dùng thấy rõ
            setTimeout(() => {
                sidebar.classList.remove('show-scrollbar');
                // Chỉ restore overflow nếu không có nội dung scroll
                if (nav.scrollHeight <= nav.clientHeight) {
                    nav.style.overflowY = originalOverflow || 'auto';
                }
            }, 8000);
        }
    }

    function toggleMenu(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.style.display = (el.style.display === 'block') ? 'none' : 'block';
        // Hiển thị scrollbar khi toggle menu
        showScrollbarOnMenuClick();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const body = document.body;
        const overlay = document.querySelector('.sidebar-overlay');
        const fixedBtn = document.querySelector('.sidebar-toggle-fixed');
        const toggleBtn = document.querySelector('.sidebar-toggle-btn');

        if (sidebar && sidebar.classList.contains('sidebar-collapsed')) {
            if (toggleBtn) {
                const hamburger = toggleBtn.querySelector('.toggle-icon:not(.close-icon)');
                const closeIcon = toggleBtn.querySelector('.close-icon');
                if (hamburger) hamburger.style.display = 'block';
                if (closeIcon) closeIcon.style.display = 'none';
            }
            if (fixedBtn) fixedBtn.style.display = 'flex';
            if (overlay) overlay.style.display = 'none';
            body.classList.add('sidebar-closed');
        } else {
            if (toggleBtn) {
                const hamburger = toggleBtn.querySelector('.toggle-icon:not(.close-icon)');
                const closeIcon = toggleBtn.querySelector('.close-icon');
                if (hamburger) hamburger.style.display = 'none';
                if (closeIcon) closeIcon.style.display = 'block';
            }
            if (fixedBtn) fixedBtn.style.display = 'none';
            if (overlay) overlay.style.display = 'none';
            body.classList.remove('sidebar-closed');
        }
        
        // Thêm event listener cho tất cả menu items để hiển thị scrollbar khi click
        const menuItems = document.querySelectorAll('.sidebar .menu-item, .sidebar .submenu-link');
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                showScrollbarOnMenuClick();
            });
        });
    });
</script>

<script>
// Close sidebar on mobile when clicking a link
function closeSidebarOnClick() {
    if (window.innerWidth < 900) {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.add('sidebar-collapsed');
        }
        document.body.classList.add('sidebar-closed');
        const overlay = document.querySelector('.sidebar-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }
    // Hiển thị scrollbar khi click vào menu item
    showScrollbarOnMenuClick();
}

// Function để hiển thị scrollbar khi click vào menu
function showScrollbarOnMenuClick() {
    const nav = document.querySelector('.sidebar .nav');
    const sidebar = document.querySelector('.sidebar');
    if (nav && sidebar) {
        // Thêm class để hiển thị scrollbar rõ hơn
        sidebar.classList.add('show-scrollbar', 'nav-scrolling');
        
        // Force scrollbar hiển thị bằng cách đặt overflow-y: scroll
        const originalOverflow = nav.style.overflowY || window.getComputedStyle(nav).overflowY;
        nav.style.overflowY = 'scroll';
        
        // Force scrollbar hiển thị ngay lập tức
        const scrollHeight = nav.scrollHeight;
        const clientHeight = nav.clientHeight;
        const currentScroll = nav.scrollTop;
        
        // Luôn force scroll một chút để trigger scrollbar hiển thị
        if (scrollHeight > clientHeight) {
            // Nếu có nội dung scroll được, scroll một chút
            nav.scrollTop = currentScroll + 1;
            setTimeout(() => {
                nav.scrollTop = currentScroll;
            }, 10);
        } else {
            // Nếu chưa có scroll, tạm thời thêm nội dung để tạo scroll
            const tempDiv = document.createElement('div');
            tempDiv.style.height = '1px';
            tempDiv.style.visibility = 'hidden';
            nav.appendChild(tempDiv);
            
            // Scroll xuống 1px
            nav.scrollTop = 1;
            
            setTimeout(() => {
                nav.scrollTop = 0;
                nav.removeChild(tempDiv);
            }, 100);
        }
        
        // Giữ scrollbar sáng trong 4 giây
        setTimeout(() => {
            sidebar.classList.remove('nav-scrolling');
        }, 4000);
        
        // Giữ scrollbar hiển thị (show-scrollbar) trong 8 giây để người dùng thấy rõ
        setTimeout(() => {
            sidebar.classList.remove('show-scrollbar');
            // Chỉ restore overflow nếu không có nội dung scroll
            if (nav.scrollHeight <= nav.clientHeight) {
                nav.style.overflowY = originalOverflow || 'auto';
            }
        }, 8000);
    }
}

// Toggle submenu
function toggleMenu(menuId) {
    const menu = document.getElementById(menuId);
    if (menu) {
        const isOpen = menu.style.display === 'block';
        
        // Close all other submenus
        document.querySelectorAll('.submenu').forEach(item => {
            if (item.id !== menuId) {
                item.style.display = 'none';
            }
        });
        
        // Toggle current submenu
        menu.style.display = isOpen ? 'none' : 'block';
        
        // Rotate chevron icon
        const chevron = menu.previousElementSibling?.querySelector('.bi-chevron-right');
        if (chevron) {
            if (isOpen) {
                chevron.style.transform = 'rotate(0deg)';
            } else {
                chevron.style.transform = 'rotate(90deg)';
            }
        }
    }
}

// Auto open active submenu on page load
document.addEventListener('DOMContentLoaded', function() {
    const activeSub = document.querySelector('.submenu-link.active-sub');
    if (activeSub) {
        const submenu = activeSub.closest('.submenu');
        if (submenu) {
            submenu.style.display = 'block';
            const chevron = submenu.previousElementSibling?.querySelector('.bi-chevron-right');
            if (chevron) {
                chevron.style.transform = 'rotate(90deg)';
            }
        }
    }
    
        // Thêm event listener cho tất cả menu items để hiển thị scrollbar khi click
        const menuItems = document.querySelectorAll('.sidebar .menu-item, .sidebar .submenu-link');
        menuItems.forEach(item => {
            // Sử dụng capture phase để đảm bảo chạy trước các onclick khác
            item.addEventListener('click', function(e) {
                // Đảm bảo scrollbar hiển thị ngay lập tức
                showScrollbarOnMenuClick();
            }, true); // true = capture phase
            
            // Cũng thêm vào bubble phase để đảm bảo
            item.addEventListener('click', function(e) {
                showScrollbarOnMenuClick();
            }, false);
        });
        
        // Thêm event listener cho toggle menu (Hoạt động CLB)
        const toggleMenuItems = document.querySelectorAll('.sidebar .menu-item[onclick*="toggleMenu"]');
        toggleMenuItems.forEach(item => {
            item.addEventListener('click', function() {
                showScrollbarOnMenuClick();
            }, true);
        });
});
</script>

<style>
/* Force override tất cả CSS inline từ các file khác - Đảm bảo thanh trượt luôn hoạt động */
aside.sidebar > nav.nav,
.sidebar > nav.nav,
aside.sidebar nav.nav,
.sidebar nav.nav,
aside.sidebar .nav,
.sidebar .nav,
body aside.sidebar .nav,
body .sidebar .nav,
html body aside.sidebar .nav,
html body .sidebar .nav,
nav.nav {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 !important;
    min-height: 0 !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 16px !important;
    padding-top: 88px !important;
    padding-right: 8px !important;
    padding-bottom: 0 !important;
    gap: 0 !important;
    scrollbar-width: thin !important;
    scrollbar-color: rgba(255, 255, 255, 0.5) rgba(255, 255, 255, 0.15) !important;
    height: 0 !important;
    max-height: none !important;
}
/* Hiển thị scrollbar rõ hơn khi hover hoặc khi click vào menu */
aside.sidebar:hover > nav.nav,
.sidebar:hover > nav.nav,
aside.sidebar:hover nav.nav,
.sidebar:hover nav.nav,
aside.sidebar:hover .nav,
.sidebar:hover .nav,
body aside.sidebar:hover .nav,
body .sidebar:hover .nav,
html body aside.sidebar:hover .nav,
html body .sidebar:hover .nav,
nav.nav:hover,
aside.sidebar.nav-scrolling > nav.nav,
.sidebar.nav-scrolling > nav.nav,
aside.sidebar.nav-scrolling nav.nav,
.sidebar.nav-scrolling nav.nav,
aside.sidebar.nav-scrolling .nav,
.sidebar.nav-scrolling .nav,
body aside.sidebar.nav-scrolling .nav,
body .sidebar.nav-scrolling .nav,
html body aside.sidebar.nav-scrolling .nav,
html body .sidebar.nav-scrolling .nav {
    scrollbar-color: rgba(255, 255, 255, 0.7) rgba(255, 255, 255, 0.2) !important;
}
aside.sidebar > nav.nav::-webkit-scrollbar,
.sidebar > nav.nav::-webkit-scrollbar,
aside.sidebar nav.nav::-webkit-scrollbar,
.sidebar nav.nav::-webkit-scrollbar,
aside.sidebar .nav::-webkit-scrollbar,
.sidebar .nav::-webkit-scrollbar,
body aside.sidebar .nav::-webkit-scrollbar,
body .sidebar .nav::-webkit-scrollbar,
html body aside.sidebar .nav::-webkit-scrollbar,
html body .sidebar .nav::-webkit-scrollbar,
nav.nav::-webkit-scrollbar {
    width: 8px !important;
    display: block !important;
}
aside.sidebar > nav.nav::-webkit-scrollbar-track,
.sidebar > nav.nav::-webkit-scrollbar-track,
aside.sidebar nav.nav::-webkit-scrollbar-track,
.sidebar nav.nav::-webkit-scrollbar-track,
aside.sidebar .nav::-webkit-scrollbar-track,
.sidebar .nav::-webkit-scrollbar-track,
body aside.sidebar .nav::-webkit-scrollbar-track,
body .sidebar .nav::-webkit-scrollbar-track,
html body aside.sidebar .nav::-webkit-scrollbar-track,
html body .sidebar .nav::-webkit-scrollbar-track,
nav.nav::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.15) !important;
    border-radius: 4px;
}
aside.sidebar > nav.nav::-webkit-scrollbar-thumb,
.sidebar > nav.nav::-webkit-scrollbar-thumb,
aside.sidebar nav.nav::-webkit-scrollbar-thumb,
.sidebar nav.nav::-webkit-scrollbar-thumb,
aside.sidebar .nav::-webkit-scrollbar-thumb,
.sidebar .nav::-webkit-scrollbar-thumb,
body aside.sidebar .nav::-webkit-scrollbar-thumb,
body .sidebar .nav::-webkit-scrollbar-thumb,
html body aside.sidebar .nav::-webkit-scrollbar-thumb,
html body .sidebar .nav::-webkit-scrollbar-thumb,
nav.nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.4) !important;
    border-radius: 4px;
    transition: background 0.2s ease !important;
}
/* Scrollbar rõ hơn khi hover vào sidebar */
aside.sidebar:hover > nav.nav::-webkit-scrollbar-thumb,
.sidebar:hover > nav.nav::-webkit-scrollbar-thumb,
aside.sidebar:hover nav.nav::-webkit-scrollbar-thumb,
.sidebar:hover nav.nav::-webkit-scrollbar-thumb,
aside.sidebar:hover .nav::-webkit-scrollbar-thumb,
.sidebar:hover .nav::-webkit-scrollbar-thumb,
body aside.sidebar:hover .nav::-webkit-scrollbar-thumb,
body .sidebar:hover .nav::-webkit-scrollbar-thumb,
html body aside.sidebar:hover .nav::-webkit-scrollbar-thumb,
html body .sidebar:hover .nav::-webkit-scrollbar-thumb,
nav.nav:hover::-webkit-scrollbar-thumb,
aside.sidebar.nav-scrolling > nav.nav::-webkit-scrollbar-thumb,
.sidebar.nav-scrolling > nav.nav::-webkit-scrollbar-thumb,
aside.sidebar.nav-scrolling nav.nav::-webkit-scrollbar-thumb,
.sidebar.nav-scrolling nav.nav::-webkit-scrollbar-thumb,
aside.sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb,
.sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb,
body aside.sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb,
body .sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb,
html body aside.sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb,
html body .sidebar.nav-scrolling .nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.6) !important;
}
aside.sidebar > nav.nav::-webkit-scrollbar-thumb:hover,
.sidebar > nav.nav::-webkit-scrollbar-thumb:hover,
aside.sidebar nav.nav::-webkit-scrollbar-thumb:hover,
.sidebar nav.nav::-webkit-scrollbar-thumb:hover,
aside.sidebar .nav::-webkit-scrollbar-thumb:hover,
.sidebar .nav::-webkit-scrollbar-thumb:hover,
body aside.sidebar .nav::-webkit-scrollbar-thumb:hover,
body .sidebar .nav::-webkit-scrollbar-thumb:hover,
html body aside.sidebar .nav::-webkit-scrollbar-thumb:hover,
html body .sidebar .nav::-webkit-scrollbar-thumb:hover,
nav.nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.8) !important;
}
</style>
