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
        .stats {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .stats h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
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
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-reprimand { background-color: #fd7e14; color: #fff; }
        .badge-suspension { background-color: #ffc107; color: #000; }
        .badge-expulsion { background-color: #dc3545; color: #fff; }
        .badge-ban { background-color: #6c757d; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
    </div>

    <div class="info">
        <strong>Ngày xuất:</strong> {{ $generated_at }}<br>
    </div>

    <div class="stats">
        <h3>Thống kê</h3>
        <div class="stats-row">
            <strong>Tổng số vi phạm đã xử lý:</strong> {{ $stats['total'] }}
        </div>
        @if(isset($stats['by_type']) && $stats['by_type']->count() > 0)
            <div class="stats-row">
                <strong>Phân bổ theo hình thức kỷ luật:</strong>
            </div>
            @foreach($stats['by_type'] as $type => $count)
                <div class="stats-row" style="padding-left: 20px;">
                    - {{ ucfirst($type) }}: {{ $count }}
                </div>
            @endforeach
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">STT</th>
                <th style="width: 10%;">Sinh viên</th>
                <th style="width: 8%;">MSSV</th>
                <th style="width: 12%;">CLB</th>
                <th style="width: 15%;">Nội quy vi phạm</th>
                <th style="width: 18%;">Hành vi vi phạm</th>
                <th style="width: 10%;">Hình thức kỷ luật</th>
                <th style="width: 12%;">Lý do xử lý</th>
                <th style="width: 7%;">Thời gian áp dụng</th>
                <th style="width: 5%;">Người xử lý</th>
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
                        @if($violation->discipline_type == 'warning')
                            <span class="badge badge-warning">Cảnh cáo</span>
                        @elseif($violation->discipline_type == 'reprimand')
                            <span class="badge badge-reprimand">Khiển trách</span>
                        @elseif($violation->discipline_type == 'suspension')
                            <span class="badge badge-suspension">Đình chỉ</span>
                        @elseif($violation->discipline_type == 'expulsion')
                            <span class="badge badge-expulsion">Buộc rời</span>
                        @elseif($violation->discipline_type == 'ban')
                            <span class="badge badge-ban">Cấm tham gia</span>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($violation->discipline_reason ?? '', 80) }}</td>
                    <td>
                        @if($violation->discipline_period_start && $violation->discipline_period_end)
                            {{ \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') }}<br>
                            - {{ \Carbon\Carbon::parse($violation->discipline_period_end)->format('d/m/Y') }}
                        @elseif($violation->discipline_period_start)
                            Từ {{ \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') }}
                        @else
                            Không giới hạn
                        @endif
                    </td>
                    <td>{{ $violation->processor->name ?? 'N/A' }}</td>
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

