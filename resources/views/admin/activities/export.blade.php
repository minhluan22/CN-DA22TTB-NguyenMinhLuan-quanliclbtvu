@extends('layouts.admin')

@section('title', 'Xuất báo cáo hoạt động')

@section('content')

<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-3">
        <i class="bi bi-file-earmark-arrow-down"></i> Xuất báo cáo hoạt động
    </h3>

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

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Chọn loại báo cáo và thời gian</h5>
            
            <form action="{{ route('admin.activities.statistics.export.generate') }}" method="POST">
                @csrf

                {{-- LOẠI BÁO CÁO --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Loại báo cáo</label>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="report_type" 
                                       id="type_activities" value="activities" checked>
                                <label class="form-check-label" for="type_activities">
                                    <strong>Danh sách hoạt động</strong><br>
                                    <small class="text-muted">Xuất danh sách các hoạt động đã tổ chức</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="report_type" 
                                       id="type_violations" value="violations">
                                <label class="form-check-label" for="type_violations">
                                    <strong>Danh sách vi phạm</strong><br>
                                    <small class="text-muted">Xuất danh sách hoạt động vi phạm</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="report_type" 
                                       id="type_statistics" value="statistics">
                                <label class="form-check-label" for="type_statistics">
                                    <strong>Thống kê tổng hợp</strong><br>
                                    <small class="text-muted">Xuất thống kê hoạt động theo CLB</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- THỜI GIAN --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Từ ngày</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ now()->subMonths(1)->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Đến ngày</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                {{-- ĐỊNH DẠNG --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Định dạng file</label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="format" 
                                       id="format_excel" value="excel" checked>
                                <label class="form-check-label" for="format_excel">
                                    <i class="bi bi-file-earmark-spreadsheet text-success"></i>
                                    <strong>Excel (.csv)</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check p-3 border rounded">
                                <input class="form-check-input" type="radio" name="format" 
                                       id="format_pdf" value="pdf">
                                <label class="form-check-label" for="format_pdf">
                                    <i class="bi bi-file-earmark-pdf text-danger"></i>
                                    <strong>PDF (.pdf)</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- NÚT XUẤT --}}
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-download"></i> Xuất báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

