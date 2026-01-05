<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hỗ trợ - Sinh viên</title>
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

        .btn-primary {
            background: linear-gradient(135deg, #0B3D91 0%, #0033A0 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(11, 61, 145, 0.25), 0 2px 4px rgba(11, 61, 145, 0.15);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0033A0 0%, #002280 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(11, 61, 145, 0.35), 0 4px 8px rgba(11, 61, 145, 0.2);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.3);
        }

        .btn-primary:focus {
            outline: none;
            box-shadow: 0 4px 12px rgba(11, 61, 145, 0.25), 0 0 0 4px rgba(11, 61, 145, 0.15);
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .table thead {
            background: #0B3D91;
            color: white;
        }

        .table th {
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }

        .table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-open { background: #FFE600; color: #000; }
        .badge-in_progress { background: #0B3D91; color: white; }
        .badge-resolved { background: #5FB84A; color: white; }
        .badge-closed { background: #6b7280; color: white; }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 16px;
            display: block;
        }

        .alert-success {
            background: #8EDC6E;
            color: #1f1f1f;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
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
                    <i class="bi bi-headset"></i> Hỗ trợ & Yêu cầu
                </div>
                <a href="{{ route('student.support.create') }}" class="btn-primary">
                    <i class="bi bi-plus-circle"></i> Gửi yêu cầu mới
                </a>
            </div>

            @if(session('success'))
                <div class="alert-success">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card">
                @if($requests->count() > 0)
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày gửi</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td>
                                            <strong style="color: var(--text-dark);">{{ $request->subject }}</strong>
                                            <br>
                                            <small style="color: var(--muted);">{{ Str::limit($request->content, 60) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $request->status }}">
                                                {{ $request->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('student.support.show', $request->id) }}" 
                                               style="color: #0B3D91; text-decoration: none; font-weight: 600;">
                                                <i class="bi bi-eye"></i> Xem
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 24px; display: flex; justify-content: center;">
                        {{ $requests->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p style="font-size: 16px; margin-bottom: 8px;">Bạn chưa có yêu cầu hỗ trợ nào.</p>
                        <a href="{{ route('student.support.create') }}" class="btn-primary" style="margin-top: 16px;">
                            <i class="bi bi-plus-circle"></i> Gửi yêu cầu đầu tiên
                        </a>
                    </div>
                @endif
            </div>
        </main>
    </div>

    @include('student.footer')


</body>
</html>
