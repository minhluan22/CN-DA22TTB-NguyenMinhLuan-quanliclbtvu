<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết thông báo</title>
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
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF9D6;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
            --card: #ffffff;
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
            padding-top: 64px;
        }
        .body-wrapper {
            display: flex;
            flex: 1;
        }
        .content {
            margin-left: 240px;
            padding: 24px;
            padding-top: 88px;
            flex: 1;
            width: calc(100% - 240px);
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        
        /* Khi sidebar đóng */
        body.sidebar-closed .content {
            margin-left: 0;
            width: 100%;
        }
        
        /* Nút hamburger cố định để mở sidebar khi đóng */
        .sidebar-toggle-fixed {
            position: fixed;
            top: 80px;
            left: 16px;
            z-index: 1000;
            background: var(--primary-blue);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        body.sidebar-closed .sidebar-toggle-fixed {
            display: flex;
        }
        
        .sidebar-toggle-fixed:hover {
            background: var(--primary-blue-hover, #0C4CB8);
            transform: scale(1.1);
        }
        
        /* CSS cho sidebar collapsed */
        .sidebar-collapsed {
            transform: translateX(-100%);
        }
        
        /* Overlay khi sidebar mở - chỉ trên mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 997;
        }
        
        /* Chỉ hiển thị overlay trên mobile khi sidebar mở */
        @media (max-width: 900px) {
            body.sidebar-open .sidebar-overlay {
                display: block;
            }
        }
        
        /* Trên desktop, không hiển thị overlay */
        @media (min-width: 901px) {
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        @media (max-width: 900px) {
            .content {
                margin-left: 0;
                width: 100%;
                padding-top: 88px;
            }
            
            body.sidebar-closed .content {
                margin-left: 0;
                width: 100%;
            }
        }
        .page-header {
            background: var(--card);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .detail-card {
            background: var(--card);
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

        <div class="content">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="bi bi-bell"></i> Chi tiết thông báo</h2>
                    <a href="{{ route('student.notifications') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="detail-card">
                <div class="mb-4">
                    <h4 class="fw-bold">{{ $notification->title }}</h4>
                    <div class="text-muted small">
                        <i class="bi bi-person"></i> Người gửi: <strong>{{ $notification->sender->name ?? 'Hệ thống' }}</strong> | 
                        <i class="bi bi-clock"></i> Thời gian: <strong>{{ $notification->sent_at ? $notification->sent_at->format('d/m/Y H:i') : 'Chưa gửi' }}</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nguồn:</label>
                    <div>
                        @if($notification->notification_source === 'admin')
                            <span class="badge bg-primary">Từ Admin</span>
                        @else
                            <span class="badge bg-success">Từ Chủ nhiệm CLB</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">CLB:</label>
                    <div>
                        @if($notification->club)
                            <span class="badge bg-light text-dark">{{ $notification->club->code }} - {{ $notification->club->name }}</span>
                        @elseif($notification->notification_source === 'admin' && $notification->target_type === 'clubs')
                            @php
                                $clubIds = $notification->target_ids ?? [];
                                $selectedClubs = \App\Models\Club::whereIn('id', $clubIds)->get();
                            @endphp
                            @foreach($selectedClubs as $club)
                                <span class="badge bg-light text-dark me-1">{{ $club->code }} - {{ $club->name }}</span>
                            @endforeach
                        @else
                            <span class="badge bg-info">Toàn hệ thống</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Loại thông báo:</label>
                    <div>
                        @if($notification->type == 'system')
                            <span class="badge bg-secondary">Thông báo hệ thống</span>
                        @elseif($notification->type == 'regulation')
                            <span class="badge bg-danger">Thông báo nội quy</span>
                        @elseif($notification->type == 'administrative')
                            <span class="badge bg-primary">Thông báo hành chính</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung:</label>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($notification->body)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('student.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

