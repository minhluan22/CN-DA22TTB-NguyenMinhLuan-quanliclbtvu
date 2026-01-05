@php
    $user = Auth::user();
    $currentRoute = request()->route()->getName();
    
    // Xác định menu nào đang active
    $activeMenu = '';
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
    
    // Lấy thông tin CLB mà user là chủ nhiệm
    $chairmanClub = \App\Http\Controllers\Student\ChairmanController::isChairman($user->id);
@endphp

<div class="sidebar">

    <!-- STUDENT INFO (Giống admin sidebar) -->
    <div class="sidebar-header">
        <div class="admin-info-modern">
            <div class="admin-avatar">
                @if($user->avatar ?? null)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                @else
                    <span>{{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}</span>
                @endif
            </div>
            <div class="admin-details">
                <div class="admin-name">{{ $user->name ?? 'Student' }}</div>
                <div class="admin-status">
                    <span class="status-dot"></span>
                    <span>Chủ nhiệm{{ $chairmanClub ? ' - ' . $chairmanClub->name : '' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="menu">

    <!-- TRANG CHỦ SV -->
    <a href="{{ route('student.home') }}"
       class="menu-item {{ request()->is('student/home') ? 'active' : '' }}">
        <span><i class="bi bi-house-door"></i> Trang Chủ SV</span>
    </a>

    <!-- DASHBOARD -->
    <a href="{{ route('student.chairman.dashboard') }}"
       class="menu-item {{ $activeMenu === 'dashboard' ? 'active' : '' }}">
        <span><i class="bi bi-speedometer2"></i> Dashboard</span>
    </a>

    <!-- QUẢN LÝ THÀNH VIÊN -->
    <a href="{{ route('student.chairman.manage-members') }}"
       class="menu-item {{ $activeMenu === 'manage-members' ? 'active' : '' }}">
        <span><i class="bi bi-people"></i> Quản lý thành viên</span>
    </a>

    <!-- ĐƠN ĐĂNG KÝ -->
    <a href="{{ route('student.chairman.manage-registrations') }}"
       class="menu-item {{ $activeMenu === 'manage-registrations' ? 'active' : '' }}">
        <span><i class="bi bi-file-earmark-text"></i> Đơn đăng ký</span>
    </a>

    <!-- GÁN CHỨC VỤ -->
    <a href="{{ route('student.chairman.manage-positions') }}"
       class="menu-item {{ $activeMenu === 'manage-positions' ? 'active' : '' }}">
        <span><i class="bi bi-person-badge"></i> Gán chức vụ</span>
    </a>

    <!-- HOẠT ĐỘNG CLB -->
    <div class="menu-item" onclick="toggleMenu('hoatdong')">
        <span><i class="bi bi-calendar-event"></i> HOẠT ĐỘNG CLB</span>
        <i class="bi bi-chevron-right"></i>
    </div>
    <div class="submenu" id="hoatdong" style="{{ $activeMenu === 'events' ? 'display:block;' : '' }}">
        <a href="{{ route('student.chairman.create-event') }}"
           class="submenu-title {{ strpos($currentRoute, 'student.chairman.create-event') === 0 ? 'active-sub' : '' }}">
            Tạo hoạt động
        </a>
        <a href="{{ route('student.chairman.pending-events') }}"
           class="submenu-title {{ strpos($currentRoute, 'student.chairman.pending-events') === 0 ? 'active-sub' : '' }}">
            Chờ duyệt
        </a>
        <a href="{{ route('student.chairman.approved-events') }}"
           class="submenu-title {{ strpos($currentRoute, 'student.chairman.approved-events') === 0 ? 'active-sub' : '' }}">
            Đã duyệt
        </a>
        <a href="{{ route('student.chairman.pending-registrations') }}"
           class="submenu-title {{ strpos($currentRoute, 'student.chairman.pending-registrations') === 0 ? 'active-sub' : '' }}">
            Đăng ký chờ duyệt
        </a>
        <a href="{{ route('student.chairman.approved-participants') }}"
           class="submenu-title {{ strpos($currentRoute, 'student.chairman.approved-participants') === 0 ? 'active-sub' : '' }}">
            Người tham gia
        </a>
    </div>

    <!-- DUYỆT ĐỀ XUẤT -->
    <a href="{{ route('student.chairman.approve-proposals') }}"
       class="menu-item {{ $activeMenu === 'proposals' ? 'active' : '' }}">
        <span><i class="bi bi-check-circle"></i> Duyệt đề xuất</span>
    </a>

    <!-- DUYỆT HOẠT ĐỘNG -->
    <a href="{{ route('student.chairman.approve-activities') }}"
       class="menu-item {{ $activeMenu === 'activities' ? 'active' : '' }}">
        <span><i class="bi bi-clipboard-check"></i> Duyệt hoạt động</span>
    </a>

    <!-- THÔNG BÁO -->
    <div class="menu-item {{ $activeMenu === 'notifications' ? 'active' : '' }}" onclick="toggleMenu('thongbao')">
        <span><i class="bi bi-bell"></i> THÔNG BÁO</span>
        <i class="bi bi-chevron-right"></i>
    </div>
    <div class="submenu" id="thongbao" style="{{ $activeMenu === 'notifications' ? 'display:block;' : '' }}">
        <a href="{{ route('student.chairman.notifications.inbox') }}" 
           class="submenu-title {{ request()->is('student/chairman/notifications/inbox*') || (request()->is('student/chairman/notifications') && !request()->is('student/chairman/notifications/*')) ? 'active-sub' : '' }}">
            Hộp thư thông báo
        </a>
    </div>

    <!-- HỖ TRỢ -->
    <a href="{{ route('student.chairman.support.index') }}"
       class="menu-item {{ $activeMenu === 'support' ? 'active' : '' }}">
        <span><i class="bi bi-headset"></i> Hỗ trợ</span>
    </a>

    <!-- NỘI QUY - VI PHẠM -->
    <div class="menu-item" onclick="toggleMenu('noiquy')">
        <span><i class="bi bi-shield-exclamation"></i> NỘI QUY - VI PHẠM</span>
        <i class="bi bi-chevron-right"></i>
    </div>
    <div class="submenu" id="noiquy" style="{{ $activeMenu === 'regulations' || $activeMenu === 'discipline-history' ? 'display:block;' : '' }}">
        <a href="{{ route('student.chairman.regulations.index') }}" 
           class="submenu-title {{ strpos($currentRoute, 'student.chairman.regulations') === 0 || strpos($currentRoute, 'student.chairman.violations') === 0 ? 'active-sub' : '' }}">
            Nội quy & Vi phạm
        </a>
        <a href="{{ route('student.chairman.discipline-history.by-time') }}" 
           class="submenu-title {{ strpos($currentRoute, 'student.chairman.discipline-history') === 0 ? 'active-sub' : '' }}">
            Lịch sử kỷ luật
        </a>
    </div>

    <!-- THỐNG KÊ -->
    <a href="{{ route('student.chairman.statistics') }}"
       class="menu-item {{ $activeMenu === 'statistics' ? 'active' : '' }}">
        <span><i class="bi bi-graph-up"></i> Thống kê</span>
    </a>

    <!-- THÔNG TIN CLB -->
    <a href="{{ route('student.chairman.club-info') }}"
       class="menu-item {{ $activeMenu === 'club-info' ? 'active' : '' }}">
        <span><i class="bi bi-info-circle"></i> Thông tin CLB</span>
    </a>

    </div>

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

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ======================================================
       1) SIDEBAR: Auto Active + Auto Open Menu
    ====================================================== */

    // Kiểm tra và mở submenu nếu có active-sub
    const activeSub = document.querySelector(".active-sub");
    if (activeSub) {
        const submenu = activeSub.closest(".submenu");
        if (submenu) {
            submenu.style.display = "block";
            
            const menuParent = submenu?.previousElementSibling;
            if (menuParent && menuParent.classList.contains("menu-item")) {
                menuParent.classList.add("active");
            }
        }

        // Nếu active-sub là trong submenu-child, mở submenu-child và xoay chevron
        const submenuChild = activeSub.closest(".submenu-child");
        if (submenuChild) {
            submenuChild.style.display = "block";
            
            const title = submenuChild.previousElementSibling;
            if (title && title.classList.contains("submenu-title")) {
                title.classList.add("active-sub");
                const chevron = title.querySelector('i.bi-chevron-right');
                if (chevron) {
                    chevron.style.transform = 'translateY(-50%) rotate(90deg)';
                }
            }
        }
    }

    // Nếu link con trùng URL → active luôn
    document.querySelectorAll(".submenu-child a").forEach(link => {
        if (link.href === window.location.href || link.classList.contains("active-sub")) {
            link.classList.add("active-sub");

            const box = link.closest(".submenu-child");
            if (box) box.style.display = "block";

            const title = box?.previousElementSibling;
            if (title && title.classList.contains("submenu-title")) {
                title.classList.add("active-sub");
                const chevron = title.querySelector('i.bi-chevron-right');
                if (chevron) {
                    chevron.style.transform = 'translateY(-50%) rotate(90deg)';
                }
            }

            const menu = box.closest(".submenu");
            if (menu) menu.style.display = "block";

            const parentMenu = menu?.previousElementSibling;
            if (parentMenu && parentMenu.classList.contains("menu-item")) {
                parentMenu.classList.add("active");
            }
        }
    });

    // Kiểm tra submenu-title có active-sub (không phải link)
    document.querySelectorAll(".submenu-title.active-sub").forEach(title => {
        if (!title.href) { // Chỉ xử lý submenu-title không phải link
            const submenu = title.closest(".submenu");
            if (submenu) submenu.style.display = "block";

            const menuParent = submenu?.previousElementSibling;
            if (menuParent && menuParent.classList.contains("menu-item")) {
                menuParent.classList.add("active");
            }

            // Kiểm tra submenu-child của title này
            const nextSibling = title.nextElementSibling;
            if (nextSibling && nextSibling.classList.contains("submenu-child")) {
                nextSibling.style.display = "block";
                const chevron = title.querySelector('i.bi-chevron-right');
                if (chevron) {
                    chevron.style.transform = 'translateY(-50%) rotate(90deg)';
                }
            }
        }
    });

    /* ======================================================
       2) Toggle menu cha
    ====================================================== */
    window.toggleMenu = function (id) {
        const menu = document.getElementById(id);
        const isOpen = menu.style.display === "block";
        
        // Đóng tất cả submenu khác
        document.querySelectorAll('.submenu').forEach(item => {
            if (item.id !== id) {
                item.style.display = 'none';
                // Reset active state của menu-item khác
                const otherMenuParent = item.previousElementSibling;
                if (otherMenuParent && otherMenuParent.classList.contains("menu-item")) {
                    otherMenuParent.classList.remove("active");
                }
            }
        });
        
        // Toggle menu hiện tại
        menu.style.display = isOpen ? "none" : "block";
        
        // Toggle active state của menu-item
        const menuParent = menu.previousElementSibling;
        if (menuParent && menuParent.classList.contains("menu-item")) {
            if (isOpen) {
                menuParent.classList.remove("active");
            } else {
                menuParent.classList.add("active");
            }
        }
        
        // Rotate chevron icon
        const chevron = menuParent?.querySelector('.bi-chevron-right');
        if (chevron) {
            if (isOpen) {
                chevron.style.transform = 'rotate(0deg)';
            } else {
                chevron.style.transform = 'rotate(90deg)';
            }
        }
    };

    /* ======================================================
       3) Toggle submenu con
    ====================================================== */
    window.toggleSubMenu = function (id) {
        const submenu = document.getElementById(id);
        const isOpen = submenu.style.display === "block";
        
        // Đóng tất cả submenu-child khác trong cùng submenu cha
        const parentSubmenu = submenu.closest(".submenu");
        if (parentSubmenu) {
            parentSubmenu.querySelectorAll('.submenu-child').forEach(item => {
                if (item.id !== id) {
                    item.style.display = 'none';
                    // Reset chevron của các submenu khác
                    const title = item.previousElementSibling;
                    if (title && title.classList.contains("submenu-title")) {
                        const chevron = title.querySelector('i.bi-chevron-right');
                        if (chevron) {
                            chevron.style.transform = 'translateY(-50%)';
                        }
                        title.classList.remove("active-sub");
                    }
                }
            });
        }
        
        // Toggle submenu hiện tại
        submenu.style.display = isOpen ? "none" : "block";
        
        // Toggle chevron và active state
        const title = submenu.previousElementSibling;
        if (title && title.classList.contains("submenu-title")) {
            const chevron = title.querySelector('i.bi-chevron-right');
            if (chevron) {
                if (isOpen) {
                    chevron.style.transform = 'translateY(-50%)';
                    title.classList.remove("active-sub");
                } else {
                    chevron.style.transform = 'translateY(-50%) rotate(90deg)';
                    title.classList.add("active-sub");
                }
            }
        }
    };

    /* ======================================================
       4) Click ra ngoài để đóng submenu
    ====================================================== */
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.sidebar')) {
            document.querySelectorAll('.submenu, .submenu-child').forEach(item => {
                item.style.display = 'none';
            });
        }
    });

});
</script>
