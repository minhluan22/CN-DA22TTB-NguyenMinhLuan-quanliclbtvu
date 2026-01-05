<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nhật ký Admin</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            font-size: 10px;
            margin: 5px 0;
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
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>NHẬT KÝ ADMIN</h1>
        <p>Hệ thống quản lý Câu lạc bộ Sinh viên</p>
        <p>Ngày xuất: {{ $generated_at }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Thời gian</th>
                <th>Admin</th>
                <th>Hành động</th>
                <th>Đối tượng</th>
                <th>Mô tả</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->admin->name ?? 'N/A' }}</td>
                    <td>{{ $log->action_name }}</td>
                    <td>{{ $log->model_name }}</td>
                    <td>{{ Str::limit($log->description ?? '—', 50) }}</td>
                    <td>{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Tổng số bản ghi: {{ count($logs) }}</p>
        <p>Trang được tạo tự động bởi hệ thống</p>
    </div>
</body>
</html>

