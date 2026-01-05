<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Danh sách tham gia hoạt động - Chủ nhiệm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #072C6A;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF7B0;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .sidebar {
            width: 240px;
            background: var(--primary-blue);
            color: var(--text-light);
            padding: 24px 16px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .logo {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 24px;
        }
        .nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .nav a {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.9);
            padding: 10px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, color 0.2s;
        }
        .nav a:hover {
            background: var(--primary-blue-hover);
            color: var(--text-light);
        }
        .nav a.active {
            background: var(--accent-yellow);
            color: var(--text-dark);
        }
        .logout-btn {
            margin-top: auto;
            background: #ef4444;
            color: #fff;
            border: none;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
        }
        .content {
            margin-left: 260px;
            padding: 24px;
        }
        .table-role {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-role thead {
            background: #eaf2ff;
            color: #0B3D91;
        }
        .table-role thead th {
            background: #eaf2ff !important;
            color: #0B3D91 !important;
            font-weight: 700;
        }
    </style>
</head>
<body>
    @include('student.sidebar')

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Danh sách tham gia hoạt động</h3>
            <a href="{{ route('student.chairman.approved-events') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        {{-- THÔNG BÁO --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>✅ Thành công!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- THÔNG TIN SỰ KIỆN --}}
        <div class="alert alert-info mb-4">
            <strong>Hoạt động:</strong> {{ $event->title }} | 
            <strong>Thời gian:</strong> {{ $event->start_at ? \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') : '-' }} | 
            <strong>Địa điểm:</strong> {{ $event->location ?? '-' }}
        </div>

        {{-- BẢNG DANH SÁCH NGƯỜI THAM GIA --}}
        <div class="table-responsive">
            <table class="table table-role">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên thành viên</th>
                        <th>MSSV</th>
                        <th>Email</th>
                        <th>Trạng thái</th>
                        <th>Điểm hoạt động</th>
                        <th>Ngày đăng ký</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($participants as $participant)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $participant->name }}</strong></td>
                            <td>{{ $participant->student_code ?? '-' }}</td>
                            <td>{{ $participant->email }}</td>
                            <td>
                                @if ($participant->status == 'pending')
                                    <span class="badge bg-warning">⏳ Chờ phê duyệt</span>
                                @elseif ($participant->status == 'approved')
                                    <span class="badge bg-success">✅ Đã phê duyệt</span>
                                @elseif ($participant->status == 'rejected')
                                    <span class="badge bg-danger">❌ Bị từ chối</span>
                                @elseif ($participant->status == 'attended')
                                    <span class="badge bg-info">✓ Đã tham gia</span>
                                @elseif ($participant->status == 'absent')
                                    <span class="badge bg-secondary">✗ Vắng mặt</span>
                                @endif
                            </td>
                            <td>{{ $participant->activity_points ?? 0 }}</td>
                            <td>{{ \Carbon\Carbon::parse($participant->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($participant->status == 'pending')
                                    <form action="{{ route('student.chairman.approve-event-participant', $participant->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Phê duyệt tham gia hoạt động?')">
                                            Phê duyệt
                                        </button>
                                    </form>
                                    <form action="{{ route('student.chairman.reject-event-participant', $participant->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Từ chối tham gia sự kiện?')">
                                            Từ chối
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Chưa có người đăng ký tham gia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

