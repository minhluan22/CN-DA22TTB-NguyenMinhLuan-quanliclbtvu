<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .info {
            margin-bottom: 15px;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            display: inline-block;
        }
        .badge-light { background-color: #f0f0f0; }
        .badge-medium { background-color: #ffc107; color: #000; }
        .badge-serious { background-color: #dc3545; color: #fff; }
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-processed { background-color: #28a745; color: #fff; }
        .badge-monitoring { background-color: #17a2b8; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
    </div>

    <div class="info">
        <strong>Ngày xuất:</strong> {{ $generated_at }}<br>
        <strong>Tổng số vi phạm:</strong> {{ $violations->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">STT</th>
                <th style="width: 10%;">Sinh viên</th>
                <th style="width: 8%;">MSSV</th>
                <th style="width: 12%;">CLB</th>
                <th style="width: 15%;">Nội quy vi phạm</th>
                <th style="width: 20%;">Mô tả vi phạm</th>
                <th style="width: 6%;">Mức độ</th>
                <th style="width: 8%;">Thời gian</th>
                <th style="width: 8%;">Người ghi nhận</th>
                <th style="width: 10%;">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @forelse($violations as $index => $violation)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $violation->user->name ?? 'N/A' }}</td>
                    <td>{{ $violation->user->student_code ?? 'N/A' }}</td>
                    <td>{{ $violation->club->name ?? 'N/A' }}<br><small>({{ $violation->club->code ?? 'N/A' }})</small></td>
                    <td>{{ $violation->regulation->title ?? 'N/A' }}<br><small>({{ $violation->regulation->code ?? 'N/A' }})</small></td>
                    <td>{{ \Illuminate\Support\Str::limit($violation->description, 100) }}</td>
                    <td>
                        @if($violation->severity == 'light')
                            <span class="badge badge-light">Nhẹ</span>
                        @elseif($violation->severity == 'medium')
                            <span class="badge badge-medium">Trung bình</span>
                        @else
                            <span class="badge badge-serious">Nghiêm trọng</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y H:i') }}</td>
                    <td>{{ $violation->recorder->name ?? 'N/A' }}</td>
                    <td>
                        @if($violation->status == 'pending')
                            <span class="badge badge-pending">Chưa xử lý</span>
                        @elseif($violation->status == 'processed')
                            <span class="badge badge-processed">Đã xử lý</span>
                        @else
                            <span class="badge badge-monitoring">Đang theo dõi</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Trang 1 - Xuất báo cáo từ hệ thống quản lý CLB</p>
    </div>
</body>
</html>

