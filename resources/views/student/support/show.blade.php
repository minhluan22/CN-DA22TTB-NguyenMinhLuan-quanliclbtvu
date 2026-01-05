<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết yêu cầu hỗ trợ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #0033A0;
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
            margin-top: 0;
            padding-top: 0;
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
            margin: 0;
            box-sizing: border-box;
        }

        .sidebar-collapsed {
            transform: translateX(-100%);
        }
        
        body.sidebar-closed .content {
            margin-left: 0;
            width: 100%;
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
            padding-top: 24px;
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
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-back {
            padding: 8px 16px;
            border: 2px solid #0B3D91;
            color: #0B3D91;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            box-shadow: 0 1px 4px rgba(11, 61, 145, 0.1);
        }

        .btn-back:hover {
            background: #0B3D91;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.2);
        }

        .btn-back:active {
            transform: translateY(0);
        }

        .btn-back:focus {
            outline: none;
            box-shadow: 0 1px 4px rgba(11, 61, 145, 0.1), 0 0 0 3px rgba(11, 61, 145, 0.1);
        }

        .btn-secondary {
            padding: 12px 24px;
            border: 2px solid #0B3D91;
            color: #0B3D91;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.1);
            position: relative;
            overflow: hidden;
        }

        .btn-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: -1;
        }

        .btn-secondary:hover::before {
            width: 100%;
        }

        .btn-secondary:hover {
            color: white;
            border-color: #0033A0;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(11, 61, 145, 0.25);
        }

        .btn-secondary:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.2);
        }

        .btn-secondary:focus {
            outline: none;
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.1), 0 0 0 4px rgba(11, 61, 145, 0.1);
        }

        .btn-respond {
            padding: 12px 24px;
            background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.2);
        }

        .btn-respond:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(11, 61, 145, 0.3);
            background: linear-gradient(135deg, #0C4CB8 0%, #0B3D91 100%);
        }

        .btn-respond:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.2);
        }

        .btn-respond:focus {
            outline: none;
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.2), 0 0 0 4px rgba(11, 61, 145, 0.1);
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 20px 24px;
            border-bottom: none;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }

        .modal-header .btn-close:hover {
            opacity: 1;
        }

        .modal-title {
            font-weight: 700;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-body {
            padding: 24px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0B3D91;
            box-shadow: 0 0 0 3px rgba(11, 61, 145, 0.1);
        }

        .modal-footer {
            border-top: 2px solid var(--border);
            padding: 16px 24px;
        }

        .btn-modal-cancel {
            padding: 10px 20px;
            border: 2px solid var(--border);
            background: white;
            color: var(--text-dark);
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-modal-cancel:hover {
            background: #f9fafb;
            border-color: var(--muted);
        }

        .btn-modal-submit {
            padding: 10px 20px;
            background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-modal-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(11, 61, 145, 0.3);
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            max-width: 1000px;
            margin: 0 auto;
            width: 100%;
        }

        .info-section {
            margin-bottom: 28px;
            padding-bottom: 28px;
            border-bottom: 2px solid #f3f4f6;
        }

        .info-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-value {
            font-size: 16px;
            color: var(--text-dark);
            line-height: 1.5;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-open { background: #FFE600; color: #000; }
        .badge-in_progress { background: #0B3D91; color: white; }
        .badge-resolved { background: #5FB84A; color: white; }
        .badge-closed { background: #6b7280; color: white; }

        .content-box {
            background: #f9fafb;
            padding: 20px 24px;
            border-radius: 12px;
            margin-top: 10px;
            white-space: pre-wrap;
            line-height: 1.8;
            border: 2px solid #e5e7eb;
            font-size: 15px;
            color: var(--text-dark);
            min-height: 80px;
        }

        .response-box {
            background: #f9fafb;
            padding: 24px 28px;
            border-radius: 12px;
            margin-top: 32px;
            border: 2px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .response-box-header {
            background: linear-gradient(135deg, #E6F0FF 0%, #CCE0FF 100%);
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #0B3D91;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .response-box-header strong {
            color: #0B3D91;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .response-box-content {
            white-space: pre-wrap;
            line-height: 1.8;
            color: var(--text-dark);
            font-size: 15px;
            padding: 0;
        }

        .response-box small {
            color: var(--muted);
            display: block;
            margin-top: 20px;
            font-size: 13px;
            padding-top: 16px;
            border-top: 2px solid #e5e7eb;
        }

        .waiting-box {
            background: linear-gradient(135deg, #FFF3A0 0%, #FFE600 100%);
            padding: 20px 24px;
            border-radius: 12px;
            margin-top: 32px;
            border-left: 4px solid #FFE600;
            display: flex;
            align-items: center;
            gap: 14px;
            color: var(--text-dark);
            box-shadow: 0 2px 8px rgba(255, 230, 0, 0.2);
        }

        .waiting-box i {
            font-size: 24px;
            color: #0B3D91;
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
            .card {
                padding: 24px;
                max-width: 100%;
            }
            .info-section {
                margin-bottom: 20px;
                padding-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    @include('student.header')
    

    
    <div class="body-wrapper">
        @include('student.sidebar')

        <main class="content">
            <div class="header">
                <div class="header-title">
                    <i class="bi bi-headset"></i> Chi tiết yêu cầu hỗ trợ
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    @if(isset($isAdmin) && $isAdmin || isset($isChairman) && $isChairman)
                        @if(!$request->admin_response)
                            <button type="button" class="btn-respond" data-bs-toggle="modal" data-bs-target="#respondModal">
                                <i class="bi bi-reply"></i> Trả lời hỗ trợ
                            </button>
                        @endif
                    @endif
                    <a href="{{ route('student.support.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="info-section">
                    <div class="info-label">
                        <i class="bi bi-card-heading"></i> Tiêu đề
                    </div>
                    <div class="info-value" style="font-size: 22px; font-weight: 700; color: var(--primary-blue); margin-top: 4px;">
                        {{ $request->subject }}
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-label">
                        <i class="bi bi-info-circle"></i> Trạng thái
                    </div>
                    <div class="info-value" style="margin-top: 8px;">
                        <span class="badge badge-{{ $request->status }}">
                            {{ $request->status_label }}
                        </span>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-label">
                        <i class="bi bi-calendar3"></i> Ngày gửi
                    </div>
                    <div class="info-value" style="margin-top: 4px;">{{ $request->created_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="info-section">
                    <div class="info-label">
                        <i class="bi bi-file-text"></i> Nội dung
                    </div>
                    <div class="content-box">
                        {{ $request->content }}
                    </div>
                </div>

                @if($request->admin_response)
                    <div class="response-box">
                        <div class="response-box-header">
                            <strong>
                                <i class="bi bi-reply"></i> Phản hồi từ {{ $request->responder ? $request->responder->name : 'Admin' }}
                            </strong>
                        </div>
                        <div class="response-box-content">
                            {{ $request->admin_response }}
                        </div>
                        @if($request->responded_at)
                            <small>
                                <i class="bi bi-clock"></i> Phản hồi lúc: {{ $request->responded_at->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </div>
                @else
                    <div class="waiting-box">
                        <i class="bi bi-clock" style="font-size: 20px;"></i>
                        <span>Yêu cầu đang được xử lý. Vui lòng chờ phản hồi.</span>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Modal Trả lời hỗ trợ -->
    @if(isset($isAdmin) && $isAdmin || isset($isChairman) && $isChairman)
        <div class="modal fade" id="respondModal" tabindex="-1" aria-labelledby="respondModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="respondModalLabel">
                            <i class="bi bi-reply"></i> Trả lời yêu cầu hỗ trợ
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('student.support.respond', $request->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="admin_response" class="form-label">
                                    <i class="bi bi-chat-text"></i> Nội dung phản hồi <span class="text-danger">*</span>
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="admin_response" 
                                    name="admin_response" 
                                    rows="6" 
                                    required
                                    minlength="10"
                                    placeholder="Nhập nội dung phản hồi (tối thiểu 10 ký tự)..."
                                >{{ old('admin_response') }}</textarea>
                                @error('admin_response')
                                    <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-info-circle"></i> Trạng thái <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="open" {{ old('status', $request->status) == 'open' ? 'selected' : '' }}>Mở</option>
                                    <option value="in_progress" {{ old('status', $request->status) == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                                    <option value="resolved" {{ old('status', $request->status) == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                                    <option value="closed" {{ old('status', $request->status) == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                                </select>
                                @error('status')
                                    <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Hủy
                            </button>
                            <button type="submit" class="btn-modal-submit">
                                <i class="bi bi-send"></i> Gửi phản hồi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @include('student.footer')

    <!-- Bootstrap JS for Modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

        // Khởi tạo sidebar khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const body = document.body;
    
    </script>
</body>
</html>
