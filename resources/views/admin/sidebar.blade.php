<div class="sidebar">

    <!-- ADMIN INFO -->
    <div class="sidebar-header">
        <div class="admin-info-modern">
            <label for="admin-avatar-upload" class="admin-avatar" style="cursor: pointer;" title="Click để đổi ảnh đại diện">
                @if(Auth::user()->avatar ?? null)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                @else
                    <span>{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</span>
                @endif
            </label>
            <input type="file" id="admin-avatar-upload" accept="image/*" style="display: none;" onchange="uploadAdminAvatar(this)">
            <div class="admin-details">
                <div class="admin-name">{{ Auth::user()->name ?? 'Administrator' }}</div>
                <div class="admin-status">
                    <span class="status-dot"></span>
                    <span>Online</span>
                </div>
            </div>
        </div>
    </div>

    <div class="menu">

    <!-- DASHBOARD -->
    <a href="{{ route('admin.dashboard') }}"
       class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <span><i class="bi bi-speedometer2"></i> Dashboard</span>
    </a>

    <!-- QUẢN TRỊ -->
    <div class="menu-item" onclick="toggleMenu('quantri')">
        <span><i class="bi bi-grid"></i> QUẢN LÝ HỆ THỐNG</span>
        <i class="bi bi-chevron-right"></i>
    </div>

    <div class="submenu" id="quantri">

        <!-- QUẢN LÝ TÀI KHOẢN -->
        <a href="{{ route('admin.users.index') }}"
        class="submenu-title {{ request()->is('admin/users*') ? 'active-sub' : '' }}">
            Quản lý tài khoản
        </a>

        <!-- PHÂN QUYỀN & VAI TRÒ -->
        <div class="submenu-title 
            {{ request()->is('admin/roles*') || request()->is('admin/assign*') ? 'active-sub' : '' }}"
            onclick="toggleSubMenu('qt_phanquyen')">
            Phân quyền & vai trò
            <i class="bi bi-chevron-right" style="float: right; margin-top: 2px; font-size: 12px;"></i>
        </div>

        <div class="submenu-child" id="qt_phanquyen"
            style="{{ request()->is('admin/roles*') || request()->is('admin/assign*') ? 'display:block;' : '' }}">

            <!-- DANH SÁCH VAI TRÒ -->
            <a href="{{ route('admin.roles.index') }}"
                class="submenu-link {{ request()->is('admin/roles*') ? 'active-sub' : '' }}">
                Danh sách vai trò
            </a>

            <!-- GÁN QUYỀN CHO TÀI KHOẢN -->
            <a href="{{ route('admin.assign.index') }}"
            class="submenu-link {{ request()->is('admin/assign*') ? 'active-sub' : '' }}">
            Gán quyền cho tài khoản
            </a>

        </div>


        <!-- QUẢN LÝ CLB -->
        <div class="submenu-title {{ request()->is('admin/clubs*') || request()->is('admin/club-members*') ? 'active-sub' : '' }}" 
             onclick="toggleSubMenu('qt_clb')">
            Quản lý CLB
            <i class="bi bi-chevron-right" style="float: right; margin-top: 2px; font-size: 12px;"></i>
        </div>
        <div class="submenu-child" id="qt_clb" style="{{ request()->is('admin/clubs*') || request()->is('admin/club-members*') ? 'display:block;' : '' }}">
            <a href="{{ route('admin.clubs.index') }}" class="submenu-link {{ request()->is('admin/clubs*') && !request()->is('admin/club-members*') ? 'active-sub' : '' }}">Danh sách CLB</a>
            <a href="{{ route('admin.club-members.index') }}" class="submenu-link {{ request()->is('admin/club-members*') ? 'active-sub' : '' }}">Danh sách thành viên CLB</a>
            
        </div>

    </div>

    <!-- HOẠT ĐỘNG -->
    <div class="menu-item" onclick="toggleMenu('hoatdong')">
        <span><i class="bi bi-calendar-event"></i> QUẢN LÝ HOẠT ĐỘNG</span>
        <i class="bi bi-chevron-right"></i>
    </div>
    <div class="submenu" id="hoatdong" style="{{ request()->is('admin/activities*') ? 'display:block;' : '' }}">

        <!-- Danh sách hoạt động -->
        <a href="{{ route('admin.activities.index') }}"
           class="submenu-title {{ request()->is('admin/activities') && !request()->is('admin/activities/violations*') && !request()->is('admin/activities/statistics*') && !request()->is('admin/activities/*/detail') ? 'active-sub' : '' }}">
            Danh sách hoạt động
        </a>

        <!-- Danh sách hoạt động vi phạm -->
        <a href="{{ route('admin.activities.violations') }}"
           class="submenu-title {{ request()->is('admin/activities/violations*') ? 'active-sub' : '' }}">
            Danh sách hoạt động vi phạm
        </a>

        <!-- Thống kê hoạt động -->
        <div class="submenu-title {{ request()->is('admin/activities/statistics*') ? 'active-sub' : '' }}"
             onclick="toggleSubMenu('hd_thongke')">
            Thống kê hoạt động
            <i class="bi bi-chevron-right" style="float: right; margin-top: 2px; font-size: 12px;"></i>
        </div>
        <div class="submenu-child" id="hd_thongke" style="{{ request()->is('admin/activities/statistics*') ? 'display:block;' : '' }}">
            <a href="{{ route('admin.activities.statistics.by-club') }}" class="submenu-link {{ request()->is('admin/activities/statistics/by-club*') ? 'active-sub' : '' }}">Thống kê theo CLB</a>
            <a href="{{ route('admin.activities.statistics.by-time') }}" class="submenu-link {{ request()->is('admin/activities/statistics/by-time*') ? 'active-sub' : '' }}">Thống kê theo thời gian</a>
            <a href="{{ route('admin.activities.statistics.export') }}" class="submenu-link {{ request()->is('admin/activities/statistics/export*') ? 'active-sub' : '' }}">Xuất báo cáo</a>
        </div>

    </div>

    <!-- NỘI QUY - VI PHẠM -->
    <div class="menu-item" onclick="toggleMenu('noiquy')">
        <span><i class="bi bi-exclamation-diamond"></i> NỘI QUY - VI PHẠM</span>
        <i class="bi bi-chevron-right"></i>
    </div>
    <div class="submenu" id="noiquy" style="{{ request()->is('admin/regulations*') || request()->is('admin/violations*') ? 'display:block;' : '' }}">

        <div class="submenu-title {{ request()->is('admin/regulations*') ? 'active-sub' : '' }}" 
             onclick="toggleSubMenu('nq_noiquy')">
            Nội quy
            <i class="bi bi-chevron-right" style="float: right; margin-top: 2px; font-size: 12px;"></i>
        </div>
        <div class="submenu-child" id="nq_noiquy" style="{{ request()->is('admin/regulations*') ? 'display:block;' : '' }}">
            <a href="{{ route('admin.regulations.index') }}" 
               class="submenu-link {{ request()->is('admin/regulations') && !request()->is('admin/regulations/*/edit') && !request()->is('admin/regulations/create') ? 'active-sub' : '' }}">
                Danh sách nội quy
            </a>
            <a href="{{ route('admin.regulations.create') }}" 
               class="submenu-link {{ request()->is('admin/regulations/create') || request()->is('admin/regulations/*/edit') ? 'active-sub' : '' }}">
                Cập nhật nội quy
            </a>
        </div>

        <div class="submenu-title {{ request()->is('admin/violations*') ? 'active-sub' : '' }}" 
             onclick="toggleSubMenu('nq_vipham')">
            Vi phạm & kỷ luật
            <i class="bi bi-chevron-right" style="float: right; margin-top: 2px; font-size: 12px;"></i>
        </div>
        <div class="submenu-child" id="nq_vipham" style="{{ request()->is('admin/violations*') ? 'display:block;' : '' }}">
            <a href="{{ route('admin.violations.index') }}" 
               class="submenu-link {{ request()->is('admin/violations') && !request()->is('admin/violations/*') && !request()->is('admin/violations/handle*') && !request()->is('admin/violations/history*') ? 'active-sub' : '' }}">
                Danh sách vi phạm
            </a>
            <a href="{{ route('admin.violations.handle-list') }}" 
               class="submenu-link {{ request()->is('admin/violations/handle') && !request()->is('admin/violations/*/handle') ? 'active-sub' : '' }}">
                Xử lý kỷ luật
            </a>
            <a href="{{ route('admin.violations.history') }}" 
               class="submenu-link {{ request()->is('admin/violations/history*') ? 'active-sub' : '' }}">
                Lịch sử kỷ luật
            </a>
        </div>

    </div>

    <!-- THỐNG KÊ - BÁO CÁO -->
    <div class="menu-item {{ request()->is('admin/statistics*') ? 'active' : '' }}" onclick="toggleMenu('thongke')">
        <span><i class="bi bi-bar-chart"></i> THỐNG KÊ - BÁO CÁO</span>
        <i class="bi bi-chevron-right"></i>
    </div>
    <div class="submenu" id="thongke" style="{{ request()->is('admin/statistics*') ? 'display:block;' : '' }}">

        <!-- Tổng quan hệ thống -->
        <a href="{{ route('admin.statistics.overview') }}" 
           class="submenu-title {{ request()->is('admin/statistics/overview') ? 'active-sub' : '' }}">
            Tổng quan hệ thống
        </a>

        <!-- Thống kê câu lạc bộ -->
        <a href="{{ route('admin.statistics.clubs') }}" 
           class="submenu-title {{ request()->is('admin/statistics/clubs') ? 'active-sub' : '' }}">
            Thống kê câu lạc bộ
        </a>

        <!-- Thống kê thành viên -->
        <a href="{{ route('admin.statistics.members') }}" 
           class="submenu-title {{ request()->is('admin/statistics/members') ? 'active-sub' : '' }}">
            Thống kê thành viên
        </a>

        <!-- Thống kê hoạt động - sự kiện -->
        <a href="{{ route('admin.statistics.activities') }}" 
           class="submenu-title {{ request()->is('admin/statistics/activities') ? 'active-sub' : '' }}">
            Thống kê hoạt động - sự kiện
        </a>

        <!-- Thống kê vi phạm - kỷ luật -->
        <a href="{{ route('admin.statistics.violations') }}" 
           class="submenu-title {{ request()->is('admin/statistics/violations') ? 'active-sub' : '' }}">
            Thống kê vi phạm - kỷ luật
        </a>

        <!-- Báo cáo tài chính CLB -->
        <a href="{{ route('admin.statistics.financial') }}" 
           class="submenu-title {{ request()->is('admin/statistics/financial') ? 'active-sub' : '' }}">
            Báo cáo tài chính CLB
        </a>

        <!-- Xuất báo cáo -->
        <a href="{{ route('admin.statistics.export') }}" 
           class="submenu-title {{ request()->is('admin/statistics/export') ? 'active-sub' : '' }}">
            Xuất báo cáo
        </a>

    </div>

    <!-- THÔNG BÁO -->
    <div class="menu-item {{ request()->is('admin/notifications*') || request()->is('admin/support*') ? 'active' : '' }}" onclick="toggleMenu('thongbao')">
        <span><i class="bi bi-bell"></i> THÔNG BÁO HỆ THỐNG</span>
        <i class="bi bi-chevron-right"></i>
    </div>
    <div class="submenu" id="thongbao" style="{{ request()->is('admin/notifications*') || request()->is('admin/support*') ? 'display:block;' : '' }}">
        <a href="{{ route('admin.notifications.inbox') }}" 
           class="submenu-title {{ request()->is('admin/notifications/inbox*') || (request()->is('admin/notifications') && !request()->is('admin/notifications/send*') && !request()->is('admin/notifications/history*') && !request()->is('admin/notifications/*') && !request()->is('admin/notifications/*/show*')) ? 'active-sub' : '' }}">
            Hộp thư thông báo
        </a>
        <a href="{{ route('admin.notifications.send') }}" 
           class="submenu-title {{ request()->is('admin/notifications/send*') ? 'active-sub' : '' }}">
            Gửi thông báo
        </a>
        <a href="{{ route('admin.notifications.history') }}" 
           class="submenu-title {{ request()->is('admin/notifications/history*') ? 'active-sub' : '' }}">
            Lịch sử thông báo
        </a>

        <!-- LIÊN HỆ / PHẢN HỒI -->
        <div class="submenu-title {{ request()->is('admin/support*') ? 'active-sub' : '' }}" 
             onclick="toggleSubMenu('tb_support')">
            Liên hệ / Phản hồi
            <i class="bi bi-chevron-right" style="float: right; margin-top: 2px; font-size: 12px;"></i>
        </div>
        <div class="submenu-child" id="tb_support" style="{{ request()->is('admin/support*') ? 'display:block;' : '' }}">
            <a href="{{ route('admin.support.guest-contacts') }}" class="submenu-link {{ request()->is('admin/support/guest-contacts*') ? 'active-sub' : '' }}">Liên hệ từ Guest</a>
            <a href="{{ route('admin.support.student-requests') }}" class="submenu-link {{ request()->is('admin/support/student-requests*') ? 'active-sub' : '' }}">Yêu cầu từ Sinh viên</a>
            <a href="{{ route('admin.support.chairman-requests') }}" class="submenu-link {{ request()->is('admin/support/chairman-requests*') ? 'active-sub' : '' }}">Yêu cầu từ Chủ nhiệm</a>
        </div>
    </div>

    <!-- VẬN HÀNH HỆ THỐNG -->
    <div class="menu-item {{ request()->is('admin/backup*') || request()->is('admin/admin-log*') || request()->is('admin/system-config*') ? 'active' : '' }}" onclick="toggleMenu('vanhanh')">
        <span><i class="bi bi-gear"></i> VẬN HÀNH HỆ THỐNG</span>
        <i class="bi bi-chevron-right"></i>
    </div>
    <div class="submenu" id="vanhanh" style="{{ request()->is('admin/backup*') || request()->is('admin/admin-log*') || request()->is('admin/system-config*') ? 'display:block;' : '' }}">
        
        <!-- Sao lưu dữ liệu -->
        <a href="{{ route('admin.backup.index') }}" 
           class="submenu-title {{ request()->is('admin/backup*') ? 'active-sub' : '' }}">
            Sao lưu dữ liệu
        </a>

        <!-- Nhật ký Admin -->
        <a href="{{ route('admin.admin-log.index') }}" 
           class="submenu-title {{ request()->is('admin/admin-log*') ? 'active-sub' : '' }}">
            Nhật ký Admin
        </a>

        <!-- Cấu hình hệ thống -->
        <div class="submenu-title {{ request()->is('admin/system-config*') ? 'active-sub' : '' }}" 
             onclick="toggleSubMenu('vh_cauhinhtinh')">
            Cấu hình hệ thống
            <i class="bi bi-chevron-right" style="float: right; margin-top: 2px; font-size: 12px;"></i>
        </div>
        <div class="submenu-child" id="vh_cauhinhtinh" style="{{ request()->is('admin/system-config*') ? 'display:block;' : '' }}">
            <a href="{{ route('admin.system-config.website') }}" class="submenu-link {{ request()->is('admin/system-config/website*') ? 'active-sub' : '' }}">Thông tin Website</a>
            <a href="{{ route('admin.system-config.email') }}" class="submenu-link {{ request()->is('admin/system-config/email*') ? 'active-sub' : '' }}">Email hệ thống</a>
            <a href="{{ route('admin.system-config.logo') }}" class="submenu-link {{ request()->is('admin/system-config/logo*') ? 'active-sub' : '' }}">Logo – Banner</a>
            <a href="{{ route('admin.system-config.points') }}" class="submenu-link {{ request()->is('admin/system-config/points*') ? 'active-sub' : '' }}">Cấu hình điểm hoạt động</a>
        </div>

    </div>

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
function uploadAdminAvatar(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('avatar', input.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        // Hiển thị loading
        const avatar = document.querySelector('.admin-avatar');
        const originalContent = avatar.innerHTML;
        avatar.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;"><div style="width: 20px; height: 20px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite;"></div></div>';

        fetch('{{ route("admin.upload-avatar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật avatar
                if (data.avatar_url) {
                    avatar.innerHTML = `<img src="${data.avatar_url}" alt="{{ Auth::user()->name }}">`;
                } else {
                    avatar.innerHTML = originalContent;
                }
                
                // Thông báo thành công
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: data.message || 'Cập nhật ảnh đại diện thành công!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(data.message || 'Cập nhật ảnh đại diện thành công!');
                }
            } else {
                avatar.innerHTML = originalContent;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message || 'Có lỗi xảy ra khi cập nhật ảnh đại diện'
                    });
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi cập nhật ảnh đại diện');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            avatar.innerHTML = originalContent;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi tải ảnh lên'
                });
            } else {
                alert('Có lỗi xảy ra khi tải ảnh lên');
            }
        });
    }
}

// Thêm CSS cho spinner
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>
