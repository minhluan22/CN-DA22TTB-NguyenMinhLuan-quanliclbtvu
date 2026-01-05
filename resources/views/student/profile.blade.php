<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hồ Sơ Cá Nhân</title>
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
            margin-top: 64px;
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
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--text-dark);
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
            word-wrap: break-word;
            white-space: pre-wrap;
            overflow-wrap: break-word;
            word-break: break-word;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: #0a2d6d;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .stat-card {
            text-align: center;
            padding: 20px;
            background: var(--bg);
            border-radius: 12px;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .stat-card .label {
            font-size: 14px;
            color: var(--muted);
        }
        .activity-level {
            text-align: center;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            color: white;
        }
        .activity-level .level {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .activity-level .points {
            opacity: 0.9;
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
        .avatar-upload {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .avatar-preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            border: 2px solid var(--border);
        }
        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
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
            <h1>👤 Hồ Sơ Cá Nhân</h1>
        </div>

        @if(session('success'))
            <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="profile-header">
            <div class="avatar-large">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="profile-info">
                <h2>{{ $user->name }}</h2>
                <div class="meta">
                    MSSV: {{ $user->student_code ?? '---' }} | Email: {{ $user->email }}
                </div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="showTab('info')">📋 Thông tin tài khoản</button>
            <button class="tab" onclick="showTab('edit')">✏️ Chỉnh sửa</button>
            <button class="tab" onclick="showTab('stats')">📊 Thống kê</button>
            <button class="tab" onclick="showTab('history')">📜 Lịch sử hoạt động</button>
        </div>

        <!-- Tab: Thông tin tài khoản -->
        <div id="tab-info" class="tab-content active">
            <div class="card">
                <div class="card-title">📋 Thông tin tài khoản</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Họ tên</div>
                        <div style="font-weight: 600;">{{ $user->name }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Mã sinh viên</div>
                        <div style="font-weight: 600;">{{ $user->student_code ?? '---' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Email</div>
                        <div style="font-weight: 600;">{{ $user->email }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Số điện thoại</div>
                        <div style="font-weight: 600;">{{ $user->phone ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Giới tính</div>
                        <div style="font-weight: 600;">
                            @if($user->gender === 'male') Nam
                            @elseif($user->gender === 'female') Nữ
                            @elseif($user->gender === 'other') Khác
                            @else Chưa cập nhật
                            @endif
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Ngày sinh</div>
                        <div style="font-weight: 600;">{{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('d/m/Y') : 'Chưa cập nhật' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Khoa – Ngành học</div>
                        <div style="font-weight: 600;">{{ $user->department ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Lớp</div>
                        <div style="font-weight: 600;">{{ $user->class ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Giới thiệu bản thân</div>
                        <div style="font-weight: 600; word-wrap: break-word; white-space: pre-wrap; line-height: 1.6;">{{ $user->bio ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Ngày tạo tài khoản</div>
                        <div style="font-weight: 600;">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') : '---' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Vai trò hiện tại</div>
                        <div style="font-weight: 600;">
                            @php
                                $roles = [];
                                if($user->hasRole('Admin')) $roles[] = 'Admin';
                                if($user->hasRole('Chủ nhiệm')) $roles[] = 'Chủ nhiệm';
                                if($user->hasRole('Student')) $roles[] = 'Student';
                                $roleText = !empty($roles) ? implode(' / ', $roles) : 'Student';
                            @endphp
                            {{ $roleText }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">Trạng thái tài khoản</div>
                        <div style="font-weight: 600;">
                            @if($user->status)
                                <span style="color: #166534; background: #dcfce7; padding: 4px 12px; border-radius: 12px; font-size: 12px;">✓ Hoạt động</span>
                            @else
                                <span style="color: #991b1b; background: #fee2e2; padding: 4px 12px; border-radius: 12px; font-size: 12px;">✗ Bị khóa</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Chỉnh sửa -->
        <div id="tab-edit" class="tab-content">
            <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-title">🖼️ Ảnh đại diện</div>
                    <div class="avatar-upload">
                        <div class="avatar-preview">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" id="avatar-preview-img">
                            @else
                                <span id="avatar-preview-text">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div>
                            <input type="file" name="avatar" id="avatar-input" accept="image/*" onchange="previewAvatar(this)">
                            <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">Chọn ảnh đại diện (tối đa 2MB)</div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">📝 Thông tin cá nhân</div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Nhập số điện thoại">
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <select name="gender">
                            <option value="">Chọn giới tính</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                    </div>
                    <div class="form-group">
                        <label>Khoa – Ngành học</label>
                        <input type="text" name="department" value="{{ old('department', $user->department) }}" placeholder="Ví dụ: Khoa Công nghệ thông tin">
                    </div>
                    <div class="form-group">
                        <label>Lớp</label>
                        <input type="text" name="class" value="{{ old('class', $user->class) }}" placeholder="Ví dụ: DH21IT01">
                    </div>
                    <div class="form-group">
                        <label>Giới thiệu bản thân <span style="font-size: 12px; color: var(--muted); font-weight: normal;">(Tối đa 500 ký tự)</span></label>
                        <textarea name="bio" id="bio-textarea" maxlength="500" rows="4" placeholder="Viết một vài dòng giới thiệu về bản thân..." style="word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">{{ old('bio', $user->bio) }}</textarea>
                        <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                            <span id="char-count">0</span>/500 ký tự
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">🔒 Đổi mật khẩu</div>
                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" placeholder="Nhập mật khẩu hiện tại">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" name="new_password" placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)">
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password" name="new_password_confirmation" placeholder="Nhập lại mật khẩu mới">
                    </div>
                    <div style="font-size: 12px; color: var(--muted);">
                        * Để trống nếu không muốn đổi mật khẩu
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
            </form>
        </div>

        <!-- Tab: Thống kê -->
        <div id="tab-stats" class="tab-content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="value">{{ $clubsJoined }}</div>
                    <div class="label">CLB đang tham gia</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $eventsAttended }}</div>
                    <div class="label">Sự kiện đã tham gia</div>
                </div>
                <div class="stat-card">
                    <div class="value">{{ $totalActivityPoints }}</div>
                    <div class="label">Tổng điểm hoạt động</div>
                </div>
            </div>
            <div class="activity-level">
                <div class="level">{{ $activityLevel }}</div>
                <div class="points">{{ $totalActivityPoints }} điểm</div>
            </div>
        </div>

        <!-- Tab: Lịch sử hoạt động -->
        <div id="tab-history" class="tab-content">
            <div class="card">
                <div class="card-title">📜 Lịch sử hoạt động</div>
                @if($activityHistory->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sự kiện</th>
                                <th>CLB</th>
                                <th>Ngày diễn ra</th>
                                <th>Điểm</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityHistory as $activity)
                                <tr>
                                    <td>{{ $activity->title }}</td>
                                    <td>{{ $activity->club_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($activity->start_at)->format('d/m/Y H:i') }}</td>
                                    <td><strong>{{ $activity->activity_points }} điểm</strong></td>
                                    <td>
                                        <span class="badge success">Hoàn thành</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--muted);">
                        <p>Chưa có hoạt động nào.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>
    </div>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });

            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');
        }

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview-img');
                    const text = document.getElementById('avatar-preview-text');
                    if (preview) {
                        preview.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.id = 'avatar-preview-img';
                        img.src = e.target.result;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '50%';
                        const previewDiv = document.querySelector('.avatar-preview');
                        if (text) text.remove();
                        previewDiv.appendChild(img);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Đếm số ký tự trong textarea giới thiệu
        document.addEventListener('DOMContentLoaded', function() {
            const bioTextarea = document.getElementById('bio-textarea');
            const charCount = document.getElementById('char-count');
            
            if (bioTextarea && charCount) {
                // Cập nhật số ký tự ban đầu
                charCount.textContent = bioTextarea.value.length;
                
                // Cập nhật khi người dùng nhập
                bioTextarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }
        });
    </script>

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

