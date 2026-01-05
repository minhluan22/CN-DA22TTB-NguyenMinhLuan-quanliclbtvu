<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0B3D91;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #0B3D91;
            margin: 0;
            font-size: 20px;
        }
        .header h2 {
            color: #666;
            margin: 5px 0;
            font-size: 16px;
            font-weight: normal;
        }
        .info {
            margin-bottom: 20px;
            font-size: 11px;
            color: #666;
        }
        .info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #0B3D91;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <h2>{{ $club->name }} ({{ $club->code }})</h2>
    </div>

    <div class="info">
        <p><strong>Thời gian báo cáo:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        <p><strong>Ngày xuất báo cáo:</strong> {{ date('d/m/Y H:i') }}</p>
        <p><strong>Tổng số bản ghi:</strong> {{ count($data) }}</p>
        <p><strong>Ghi chú:</strong> Dữ liệu chỉ bao gồm các hoạt động đã được duyệt và hợp lệ</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if(count($rows) > 0)
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ count($headers) }}" style="text-align: center; padding: 20px;">
                        Không có dữ liệu trong khoảng thời gian này
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Hệ thống Quản lý Câu lạc bộ - CLB ĐẠI HỌC TRÀ VINH</p>
        <p>Xuất báo cáo tự động - Bản quyền © 2025</p>
    </div>
</body>
</html>

