<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đề xuất hoạt động mới</title>
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
            pointer-events: auto !important;
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
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        body.sidebar-closed .sidebar-overlay {
            display: block;
        }

        .content {
            margin-left: 240px;
            padding: 24px;
            flex: 1;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-closed .content {
            margin-left: 0;
        }

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1002;
            }

            body:not(.sidebar-closed) .sidebar {
                transform: translateX(0);
            }

            .content {
                margin-left: 0;
                padding: 16px;
            }

            .sidebar-overlay {
                display: none;
            }

            body:not(.sidebar-closed) .sidebar-overlay {
                display: block;
            }
        }

        .form-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .info-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            width: 150px;
            color: var(--muted);
        }
        .info-value {
            flex: 1;
        }
        .badge-position {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-chairman {
            background: #0d6efd;
            color: white;
        }
        .badge-vice {
            background: #0dcaf0;
            color: white;
        }
        .badge-member {
            background: #6c757d;
            color: white;
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
            <div class="page-header mb-4">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-lightbulb"></i>
                    Đề xuất hoạt động mới
                </h3>
            </div>

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

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($userClubs->count() == 0)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Bạn cần tham gia ít nhất một CLB để đề xuất hoạt động.
                    <a href="{{ route('student.all-clubs') }}" class="btn btn-primary btn-sm ms-2">Tham gia CLB</a>
                </div>
            @else
                <div class="form-card">
                    <form action="{{ route('student.store-proposed-event') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- THÔNG TIN NGƯỜI ĐỀ XUẤT (TỰ ĐỘNG) --}}
                        <div class="info-section">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-person-circle"></i> Thông tin người đề xuất
                            </h5>
                            <div class="info-row">
                                <div class="info-label">Họ tên:</div>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">MSSV:</div>
                                <div class="info-value">{{ $user->student_code ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email:</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>
                            @if($selectedClub)
                                <div class="info-row">
                                    <div class="info-label">CLB:</div>
                                    <div class="info-value">{{ $selectedClub->name }} ({{ $selectedClub->code }})</div>
                                </div>
                                @if($userPosition)
                                    <div class="info-row">
                                        <div class="info-label">Chức vụ:</div>
                                        <div class="info-value">
                                            @if($userPosition == 'chairman')
                                                <span class="badge-position badge-chairman">Chủ nhiệm</span>
                                            @elseif($userPosition == 'vice_chairman')
                                                <span class="badge-position badge-vice">Phó Chủ nhiệm</span>
                                            @else
                                                <span class="badge-position badge-member">Thành viên</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- CLB --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">CLB <span class="text-danger">*</span></label>
                            <select name="club_id" class="form-control" required 
                                    {{ $selectedClub ? 'readonly' : '' }} 
                                    style="{{ $selectedClub ? 'background-color: #e9ecef;' : '' }}">
                                @if($selectedClub)
                                    <option value="{{ $selectedClub->id }}" selected>
                                        {{ $selectedClub->name }} ({{ $selectedClub->code }})
                                    </option>
                                @else
                                    <option value="">-- Chọn CLB --</option>
                                    @foreach($userClubs as $club)
                                        <option value="{{ $club->id }}" {{ old('club_id') == $club->id ? 'selected' : '' }}>
                                            {{ $club->name }} ({{ $club->code }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if($selectedClub)
                                <input type="hidden" name="club_id" value="{{ $selectedClub->id }}">
                            @endif
                        </div>

                        {{-- TÊN HOẠT ĐỘNG --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên hoạt động <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" 
                                   placeholder="Nhập tên hoạt động ngắn gọn" required>
                        </div>

                        {{-- LOẠI HOẠT ĐỘNG --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Loại hoạt động <span class="text-danger">*</span></label>
                            <select name="activity_type" class="form-control" required>
                                <option value="">-- Chọn loại hoạt động --</option>
                                <option value="academic" {{ old('activity_type') == 'academic' ? 'selected' : '' }}>Học thuật</option>
                                <option value="arts" {{ old('activity_type') == 'arts' ? 'selected' : '' }}>Văn nghệ</option>
                                <option value="volunteer" {{ old('activity_type') == 'volunteer' ? 'selected' : '' }}>Tình nguyện</option>
                                <option value="other" {{ old('activity_type') == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>

                        {{-- MỤC TIÊU --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mục tiêu <span class="text-danger">*</span></label>
                            <textarea name="goal" class="form-control" rows="3" 
                                      placeholder="Nhập mục đích của hoạt động" required>{{ old('goal') }}</textarea>
                        </div>

                        {{-- NỘI DUNG CHI TIẾT --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung chi tiết <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" 
                                      placeholder="Nhập kế hoạch chi tiết của hoạt động" required>{{ old('description') }}</textarea>
                        </div>

                        {{-- THỜI GIAN DỰ KIẾN --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_at" class="form-control" 
                                       value="{{ old('start_at') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thời gian kết thúc</label>
                                <input type="datetime-local" name="end_at" class="form-control" 
                                       value="{{ old('end_at') }}">
                            </div>
                        </div>

                        {{-- ĐỊA ĐIỂM DỰ KIẾN --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa điểm dự kiến <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" 
                                   value="{{ old('location') }}" 
                                   placeholder="Nhập nơi tổ chức hoạt động" required>
                        </div>

                        {{-- SỐ LƯỢNG DỰ KIẾN (TÙY CHỌN) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Số lượng dự kiến</label>
                            <input type="number" name="expected_participants" class="form-control" 
                                   value="{{ old('expected_participants') }}" 
                                   placeholder="Ước tính số người tham gia" min="1">
                        </div>

                        {{-- KINH PHÍ DỰ KIẾN (TÙY CHỌN) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kinh phí dự kiến</label>
                            <input type="number" name="expected_budget" class="form-control" 
                                   value="{{ old('expected_budget') }}" 
                                   placeholder="Nhập kinh phí dự kiến (VNĐ)" min="0" step="1000">
                        </div>

                        {{-- FILE ĐÍNH KÈM (TÙY CHỌN) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">File đính kèm</label>
                            <input type="file" name="attachment" class="form-control" 
                                   accept=".pdf,.doc,.docx">
                            <small class="text-muted">Chấp nhận file PDF, DOC, DOCX (tối đa 5MB)</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Hoạt động sẽ được gửi đến quản trị viên để phê duyệt. Sau khi được duyệt, hoạt động sẽ hiển thị trong danh sách hoạt động.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Gửi đề xuất
                            </button>
                            @if($selectedClub)
                                <a href="{{ route('student.club-detail', $selectedClub->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Quay lại
                                </a>
                            @else
                                <a href="{{ route('student.profile') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Quay lại
                                </a>
                            @endif
                        </div>
                    </form>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
