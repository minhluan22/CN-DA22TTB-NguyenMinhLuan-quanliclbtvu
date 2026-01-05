<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hộp thư thông báo</title>
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
        
        /* CSS cho sidebar collapsed */
        .sidebar-collapsed {
            transform: translateX(-100%);
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
        .filter-card {
            background: var(--card);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .table-card {
            background: var(--card);
            padding: 20px;
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
                <h2><i class="bi bi-inbox"></i> Hộp thư thông báo</h2>
                <p class="text-muted mb-0">Xem thông báo từ Admin và Chủ nhiệm CLB</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>✅ Thành công!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>❌ Lỗi!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="filter-card">
                <form method="GET">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Loại thông báo</label>
                            <select name="type" class="form-control">
                                <option value="">-- Tất cả --</option>
                                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>Thông báo hệ thống</option>
                                <option value="regulation" {{ request('type') == 'regulation' ? 'selected' : '' }}>Thông báo nội quy</option>
                                <option value="administrative" {{ request('type') == 'administrative' ? 'selected' : '' }}>Thông báo hành chính</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Nguồn</label>
                            <select name="source" class="form-control">
                                <option value="">-- Tất cả --</option>
                                <option value="admin" {{ request('source') == 'admin' ? 'selected' : '' }}>Từ Admin</option>
                                <option value="club" {{ request('source') == 'club' ? 'selected' : '' }}>Từ Chủ nhiệm CLB</option>
                            </select>
                        </div>
                        @if(count($clubs) > 0)
                            <div class="col-md-2">
                                <label class="form-label small text-muted mb-1">CLB</label>
                                <select name="club_id" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    @foreach($clubs as $club)
                                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                            {{ $club->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Từ ngày</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">🔍 Lọc</button>
                        </div>
                    </div>
                    @if(request()->hasAny(['type', 'source', 'club_id', 'start_date', 'end_date']))
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <a href="{{ route('student.notifications') }}" class="btn btn-secondary btn-sm">Xóa bộ lọc</a>
                            </div>
                        </div>
                    @endif
                </form>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tiêu đề</th>
                                <th>Người gửi</th>
                                <th>Nguồn</th>
                                <th>CLB</th>
                                <th>Loại</th>
                                <th>Thời gian gửi</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr>
                                    <td>{{ ($notifications->currentPage() - 1) * $notifications->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $notification->title }}</strong>
                                        @if(!$notification->is_read_by_user)
                                            <span class="badge bg-danger ms-2">Mới</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->sender->name ?? 'Hệ thống' }}</td>
                                    <td>
                                        @if($notification->notification_source === 'admin')
                                            <span class="badge bg-primary">Admin</span>
                                        @else
                                            <span class="badge bg-success">CLB</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($notification->club)
                                            {{ $notification->club->name }}
                                        @elseif($notification->notification_source === 'admin' && $notification->target_type === 'clubs')
                                            @php
                                                $clubIds = $notification->target_ids ?? [];
                                                $clubNames = \App\Models\Club::whereIn('id', $clubIds)->pluck('name')->toArray();
                                            @endphp
                                            {{ implode(', ', $clubNames) }}
                                        @else
                                            Toàn hệ thống
                                        @endif
                                    </td>
                                    <td>
                                        @if($notification->type == 'system')
                                            <span class="badge bg-secondary">Hệ thống</span>
                                        @elseif($notification->type == 'regulation')
                                            <span class="badge bg-danger">Nội quy</span>
                                        @elseif($notification->type == 'administrative')
                                            <span class="badge bg-primary">Hành chính</span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->sent_at ? $notification->sent_at->format('d/m/Y H:i') : '—' }}</td>
                                    <td>
                                        @if($notification->is_read_by_user)
                                            <span class="badge bg-success">Đã đọc</span>
                                        @else
                                            <span class="badge bg-warning">Chưa đọc</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('student.notifications.show', $notification->id) }}" 
                                           class="btn btn-sm btn-primary" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Không có thông báo nào
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($notifications->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $notifications->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('student.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

