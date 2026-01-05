<header class="student-header">
    <div class="header-left">
        <div class="header-logo">
            @if(file_exists(public_path('images/tvu-logo.png')))
                <img src="{{ asset('images/tvu-logo.png') }}" alt="TVU Logo" class="logo-img">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
            @else
                <div class="logo-placeholder">🎓</div>
            @endif
            <h1>CLB ĐẠI HỌC TRÀ VINH</h1>
        </div>
    </div>
    
    <div class="header-right">
        <div class="header-nav">
            <a href="{{ route('student.home') }}" class="nav-link">
                🏠 Trang chủ
            </a>
            <a href="{{ route('student.notifications') }}" class="nav-link" style="position: relative;">
                📢 Tin tức
                @if(isset($unreadNotifications) && $unreadNotifications > 0)
                    <span style="position: absolute; top: -4px; right: -4px; background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 10px; font-weight: 600; min-width: 18px; text-align: center;">
                        {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                    </span>
                @endif
            </a>
        </div>
        <div class="header-user" onclick="toggleUserDropdown()">
            @php
                $user = Auth::user();
                // Xác định vai trò
                $roles = [];
                if($user->hasRole('Admin')) $roles[] = 'Admin';
                if($user->hasRole('Chủ nhiệm')) $roles[] = 'Chủ nhiệm';
                if($user->hasRole('Student')) $roles[] = 'Student';
                $roleText = !empty($roles) ? implode(' / ', $roles) : 'Student';
            @endphp
            <div class="user-avatar">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name ?? 'SV', 0, 1)) }}
                @endif
            </div>
            <div class="user-info">
                <div class="user-name">{{ $user->name ?? 'Sinh viên' }}</div>
                <div class="user-role">Sinh viên</div>
            </div>
            
            <!-- User Dropdown Menu -->
            <div class="user-dropdown" id="userDropdown">
                <a href="{{ route('student.profile') }}" class="dropdown-item">
                    <span class="dropdown-icon">📄</span>
                    <span>Hồ sơ cá nhân</span>
                </a>
                <a href="{{ route('student.change-password') }}" class="dropdown-item">
                    <span class="dropdown-icon">🔐</span>
                    <span>Đổi mật khẩu</span>
                </a>
                <a href="{{ route('student.notifications') }}" class="dropdown-item">
                    <span class="dropdown-icon">📬</span>
                    <span>Thông báo</span>
                    @if(isset($unreadNotifications) && $unreadNotifications > 0)
                        <span class="notification-badge">{{ $unreadNotifications }}</span>
                    @endif
                </a>
                <div class="dropdown-item dropdown-divider">
                    <span class="dropdown-icon">📜</span>
                    <span>Vai trò: {{ $roleText }}</span>
                </div>
                @if(count($roles) > 1)
                    <div class="dropdown-submenu">
                        <div style="padding: 8px 20px; font-size: 12px; color: var(--muted); font-weight: 600;">Chuyển sang:</div>
                        @if($user->hasRole('Admin'))
                            <a href="{{ route('admin.dashboard') }}" class="dropdown-item" style="padding-left: 40px;">
                                <span class="dropdown-icon">👑</span>
                                <span>Dashboard Admin</span>
                            </a>
                        @endif
                        @if($user->hasRole('Chủ nhiệm'))
                            <a href="{{ route('student.chairman.manage-members') }}" class="dropdown-item" style="padding-left: 40px;">
                                <span class="dropdown-icon">⭐</span>
                                <span>Quản lý CLB (Chủ nhiệm)</span>
                            </a>
                        @endif
                        @if($user->hasRole('Student'))
                            <a href="{{ route('student.home') }}" class="dropdown-item" style="padding-left: 40px;">
                                <span class="dropdown-icon">👤</span>
                                <span>Dashboard Student</span>
                            </a>
                        @endif
                    </div>
                    <div class="dropdown-divider"></div>
                @endif
                <div class="dropdown-item" onclick="toggleDarkMode(event)">
                    <span class="dropdown-icon">🌓</span>
                    <span>Chế độ tối</span>
                    <span class="dark-mode-toggle">
                        <span class="toggle-switch" id="darkModeToggle"></span>
                    </span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="dropdown-item logout-item">
                        <span class="dropdown-icon">🚪</span>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<style>
    .student-header {
        background: var(--primary-blue, #0B3D91);
        color: var(--text-light, #ffffff);
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 999;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        height: 64px;
        box-sizing: border-box;
        transition: margin-left 0.3s ease, width 0.3s ease;
    }

    /* Khi sidebar mở - header thu hẹp bằng với content */
    body:not(.sidebar-closed) .student-header {
        margin-left: 240px;
        width: calc(100% - 240px);
    }

    /* Khi sidebar đóng - header full width */
    body.sidebar-closed .student-header {
        margin-left: 0;
        width: 100%;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-logo {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logo-img {
        height: 40px;
        width: auto;
        object-fit: contain;
    }

    .logo-placeholder {
        font-size: 32px;
        display: flex;
        align-items: center;
    }

    .header-logo h1 {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        color: var(--text-light);
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header-nav {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .nav-link {
        color: var(--text-light);
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--accent-yellow, #FFE600);
    }

    .header-user {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        cursor: pointer;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--text-light);
        font-size: 16px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-light);
    }

    .user-role {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.7);
    }

    /* User Dropdown Menu */
    .user-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        background: var(--text-light, #ffffff);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        min-width: 280px;
        padding: 8px 0;
        display: none;
        z-index: 1000;
        overflow: hidden;
    }

    .user-dropdown.show {
        display: block;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: var(--text-dark, #1f1f1f);
        text-decoration: none;
        font-size: 14px;
        transition: background 0.2s;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        font-family: inherit;
    }

    .dropdown-item:hover {
        background: rgba(11, 61, 145, 0.08);
    }

    .dropdown-item.logout-item {
        color: #ef4444;
    }

    .dropdown-item.logout-item:hover {
        background: rgba(239, 68, 68, 0.1);
    }

    .dropdown-icon {
        font-size: 18px;
        width: 24px;
        text-align: center;
        flex-shrink: 0;
    }

    .dropdown-divider {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        margin: 4px 0;
        padding: 8px 20px;
        font-weight: 600;
        color: var(--muted, #6b7280);
        cursor: default;
    }

    .dropdown-divider:hover {
        background: transparent;
    }

    .notification-badge {
        background: #ef4444;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        margin-left: auto;
    }

    .dark-mode-toggle {
        margin-left: auto;
    }

    .toggle-switch {
        display: inline-block;
        width: 44px;
        height: 24px;
        background: #ccc;
        border-radius: 12px;
        position: relative;
        transition: background 0.3s;
        cursor: pointer;
    }

    .toggle-switch::after {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        background: white;
        border-radius: 50%;
        top: 3px;
        left: 3px;
        transition: transform 0.3s;
    }

    body.dark-mode .toggle-switch {
        background: var(--primary-blue, #0B3D91);
    }

    body.dark-mode .toggle-switch::after {
        transform: translateX(20px);
    }

    @media (max-width: 900px) {
        .student-header {
            padding: 12px 16px;
            height: 56px;
            width: 100%;
            margin-left: 0;
        }

        body.sidebar-closed .student-header {
            margin-left: 0;
            width: 100%;
        }

        body:not(.sidebar-closed) .student-header {
            margin-left: 0;
            width: 100%;
        }

        .header-logo h1 {
            font-size: 16px;
        }

        .logo-img {
            height: 32px;
        }

        .header-nav {
            gap: 8px;
        }

        .nav-link {
            padding: 6px 12px;
            font-size: 13px;
        }

        .user-info {
            display: none;
        }
    }
</style>

<script>
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        
        const body = document.body;
        const overlay = document.querySelector('.sidebar-overlay');
        const toggleBtn = document.querySelector('.sidebar-toggle-btn');
        
        if (toggleBtn) {
            const hamburgerIcon = toggleBtn.querySelector('.toggle-icon:not(.close-icon)');
            const closeIcon = toggleBtn.querySelector('.close-icon');
            
            if (sidebar.classList.contains('sidebar-collapsed')) {
                // Mở sidebar - hiển thị icon đóng
                sidebar.classList.remove('sidebar-collapsed');
                body.classList.remove('sidebar-closed');
                body.classList.add('sidebar-open');
                if (overlay) overlay.style.display = 'block';
                if (hamburgerIcon) hamburgerIcon.style.display = 'none';
                if (closeIcon) closeIcon.style.display = 'block';
            } else {
                // Đóng sidebar - hiển thị icon hamburger
                sidebar.classList.add('sidebar-collapsed');
                body.classList.add('sidebar-closed');
                body.classList.remove('sidebar-open');
                if (overlay) overlay.style.display = 'none';
                if (hamburgerIcon) hamburgerIcon.style.display = 'block';
                if (closeIcon) closeIcon.style.display = 'none';
            }
        } else {
            // Fallback nếu không có toggle button trong sidebar
            if (sidebar.classList.contains('sidebar-collapsed')) {
                sidebar.classList.remove('sidebar-collapsed');
                body.classList.remove('sidebar-closed');
                body.classList.add('sidebar-open');
                if (overlay) overlay.style.display = 'block';
            } else {
                sidebar.classList.add('sidebar-collapsed');
                body.classList.add('sidebar-closed');
                body.classList.remove('sidebar-open');
                if (overlay) overlay.style.display = 'none';
            }
        }
    }

    // Khởi tạo sidebar khi trang load
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const body = document.body;
        const overlay = document.querySelector('.sidebar-overlay');
        
        const toggleBtn = document.querySelector('.sidebar-toggle-btn');
        
        // Trên desktop (>= 900px): sidebar mở mặc định
        // Trên mobile (< 900px): sidebar đóng mặc định
        if (window.innerWidth >= 900) {
            // Desktop: đảm bảo sidebar mở
            if (sidebar) {
                sidebar.classList.remove('sidebar-collapsed');
            }
            body.classList.remove('sidebar-closed');
            body.classList.remove('sidebar-open');
            if (overlay) overlay.style.display = 'none';
            // Hiển thị icon đóng trên desktop
            if (toggleBtn) {
                const hamburgerIcon = toggleBtn.querySelector('.toggle-icon:not(.close-icon)');
                const closeIcon = toggleBtn.querySelector('.close-icon');
                if (hamburgerIcon) hamburgerIcon.style.display = 'none';
                if (closeIcon) closeIcon.style.display = 'block';
            }
        } else {
            // Mobile: sidebar đóng mặc định
            if (sidebar) {
                sidebar.classList.add('sidebar-collapsed');
            }
            body.classList.add('sidebar-closed');
            body.classList.remove('sidebar-open');
            if (overlay) overlay.style.display = 'none';
            // Hiển thị icon hamburger trên mobile
            if (toggleBtn) {
                const hamburgerIcon = toggleBtn.querySelector('.toggle-icon:not(.close-icon)');
                const closeIcon = toggleBtn.querySelector('.close-icon');
                if (hamburgerIcon) hamburgerIcon.style.display = 'block';
                if (closeIcon) closeIcon.style.display = 'none';
            }
        }

        // Đóng sidebar khi click overlay (mobile)
        if (overlay) {
            overlay.addEventListener('click', function() {
                toggleSidebar();
            });
        }

        // Xử lý khi resize window
        window.addEventListener('resize', function() {
            const toggleBtn = document.querySelector('.sidebar-toggle-btn');
            if (window.innerWidth >= 900) {
                // Desktop: mở sidebar nếu đang đóng
                if (sidebar && sidebar.classList.contains('sidebar-collapsed')) {
                    sidebar.classList.remove('sidebar-collapsed');
                }
                body.classList.remove('sidebar-closed');
                body.classList.remove('sidebar-open');
                if (overlay) overlay.style.display = 'none';
                // Cập nhật icon
                if (toggleBtn) {
                    const hamburgerIcon = toggleBtn.querySelector('.toggle-icon:not(.close-icon)');
                    const closeIcon = toggleBtn.querySelector('.close-icon');
                    if (hamburgerIcon) hamburgerIcon.style.display = 'none';
                    if (closeIcon) closeIcon.style.display = 'block';
                }
            }
        });

        // Kiểm tra dark mode từ localStorage
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    });

    // Toggle Dark Mode (có thể gọi từ bất kỳ đâu)
    function toggleDarkMode(event) {
        if (event) {
            event.stopPropagation();
        }
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', isDark);
    }

    // Toggle User Dropdown
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    // Đóng dropdown khi click outside
    document.addEventListener('click', function(event) {
        const headerUser = document.querySelector('.header-user');
        const dropdown = document.getElementById('userDropdown');
        
        if (headerUser && dropdown && !headerUser.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Toggle Dark Mode
    function toggleDarkMode(event) {
        event.stopPropagation();
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', isDark);
    }
</script>

