<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xuất báo cáo - Chủ nhiệm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-blue: #0B3D91;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF7B0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--soft-yellow);
        }
        .content {
            margin-left: 260px;
            padding: 24px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .fade-in {
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    @include('student.sidebar')

    <div class="content fade-in">
        <div class="page-header mb-4">
            <h3 class="fw-bold mb-0">
                <i class="bi bi-file-earmark-arrow-down"></i> Xuất báo cáo
            </h3>
            <div class="badge bg-primary" style="font-size: 14px; padding: 10px 16px;">
                <i class="bi bi-building"></i> CLB: {{ $club->name }} ({{ $club->code }})
            </div>
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

        <div class="form-card">
            <h5 class="fw-bold mb-4">Chọn loại báo cáo và thời gian</h5>
            
            <form action="{{ route('student.chairman.export-report.generate') }}" method="POST">
                @csrf

                <!-- Loại báo cáo -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-file-text"></i> Loại báo cáo
                    </label>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="report_type" id="type_overview" value="overview" checked>
                                <label class="form-check-label" for="type_overview">
                                    <strong>Báo cáo tổng quan CLB</strong><br>
                                    <small class="text-muted">Tổng hợp thống kê tổng quan về CLB</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="report_type" id="type_members" value="members">
                                <label class="form-check-label" for="type_members">
                                    <strong>Danh sách thành viên</strong><br>
                                    <small class="text-muted">Xuất danh sách thành viên CLB</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="report_type" id="type_activities" value="activities">
                                <label class="form-check-label" for="type_activities">
                                    <strong>Báo cáo hoạt động CLB</strong><br>
                                    <small class="text-muted">Xuất danh sách các hoạt động đã tổ chức</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="report_type" id="type_participations" value="participations">
                                <label class="form-check-label" for="type_participations">
                                    <strong>Báo cáo tham gia hoạt động</strong><br>
                                    <small class="text-muted">Xuất danh sách đăng ký và tham gia</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="report_type" id="type_violations" value="violations">
                                <label class="form-check-label" for="type_violations">
                                    <strong>Báo cáo vi phạm - kỷ luật</strong><br>
                                    <small class="text-muted">Xuất danh sách vi phạm và kỷ luật</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thời gian -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-calendar"></i> Từ ngày
                        </label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ now()->subMonths(1)->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-calendar-check"></i> Đến ngày
                        </label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                <!-- Định dạng -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-file-earmark"></i> Định dạng file
                    </label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="format" id="format_excel" value="excel" checked>
                                <label class="form-check-label" for="format_excel">
                                    <i class="bi bi-file-earmark-spreadsheet text-success"></i>
                                    <strong>Excel (.xlsx)</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="format" id="format_pdf" value="pdf">
                                <label class="form-check-label" for="format_pdf">
                                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                                    <strong>PDF (.pdf)</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nút xuất -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-download"></i> Xuất báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

