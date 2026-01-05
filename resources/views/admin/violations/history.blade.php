@extends('layouts.admin')

@section('title', 'Lịch sử kỷ luật')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>✅ Thành công!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-clock-history"></i> Lịch sử kỷ luật
    </h3>

    {{-- THỐNG KÊ --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Thống kê hình thức kỷ luật</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <strong>Tổng số vi phạm đã xử lý:</strong> <span class="badge bg-primary">{{ $stats['total'] ?? 0 }}</span>
                        </div>
                        @if($stats['by_type']->count() > 0)
                            @foreach($stats['by_type'] as $typeStat)
                                <div class="col-md-2 mb-2">
                                    @php
                                        $typeNames = [
                                            'warning' => 'Cảnh cáo',
                                            'reprimand' => 'Khiển trách',
                                            'suspension' => 'Đình chỉ',
                                            'expulsion' => 'Buộc rời',
                                            'ban' => 'Cấm tham gia'
                                        ];
                                        $typeName = $typeNames[$typeStat->discipline_type] ?? $typeStat->discipline_type;
                                    @endphp
                                    <span class="badge bg-info">{{ $typeName }}: {{ $typeStat->count }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" class="mb-4" id="filterForm">
        <div class="row g-2">
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Từ khóa</label>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Tên sinh viên, MSSV, CLB, lý do...">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">CLB</label>
                <select name="club_id" class="form-control">
                    <option value="">-- Tất cả CLB --</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                            {{ $club->code }} - {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Hình thức kỷ luật</label>
                <select name="discipline_type" class="form-control">
                    <option value="">-- Tất cả --</option>
                    <option value="warning" {{ request('discipline_type') == 'warning' ? 'selected' : '' }}>Cảnh cáo</option>
                    <option value="reprimand" {{ request('discipline_type') == 'reprimand' ? 'selected' : '' }}>Khiển trách</option>
                    <option value="suspension" {{ request('discipline_type') == 'suspension' ? 'selected' : '' }}>Đình chỉ</option>
                    <option value="expulsion" {{ request('discipline_type') == 'expulsion' ? 'selected' : '' }}>Buộc rời</option>
                    <option value="ban" {{ request('discipline_type') == 'ban' ? 'selected' : '' }}>Cấm tham gia</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Người xử lý</label>
                <select name="processed_by" class="form-control">
                    <option value="">-- Tất cả --</option>
                    @foreach($processors as $processor)
                        <option value="{{ $processor->id }}" {{ request('processed_by') == $processor->id ? 'selected' : '' }}>
                            {{ $processor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted mb-1">Từ ngày</label>
                <input type="date" name="start_date" class="form-control" 
                       value="{{ request('start_date') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted mb-1">Đến ngày</label>
                <input type="date" name="end_date" class="form-control" 
                       value="{{ request('end_date') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted mb-1">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Tìm
                </button>
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted mb-1">&nbsp;</label>
                <div class="btn-group w-100">
                    <a href="{{ route('admin.violations.export-history', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                       class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </a>
                    <a href="{{ route('admin.violations.export-history', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                       class="btn btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-hover">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Sinh viên vi phạm</th>
                    <th>CLB</th>
                    <th>Nội quy vi phạm</th>
                    <th>Hành vi vi phạm</th>
                    <th>Hình thức kỷ luật</th>
                    <th>Thời gian áp dụng</th>
                    <th>Người xử lý</th>
                    <th>Thời gian xử lý</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($violations as $index => $violation)
                    <tr>
                        <td>{{ $violations->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $violation->user->name ?? 'N/A' }}</strong>
                            @if($violation->user->student_code ?? null)
                                <br><small class="text-muted">({{ $violation->user->student_code }})</small>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $violation->club->code ?? 'N/A' }}</small><br>
                            {{ $violation->club->name ?? 'N/A' }}
                        </td>
                        <td>
                            <strong>{{ $violation->regulation->code ?? 'N/A' }}</strong><br>
                            <small>{{ Str::limit($violation->regulation->title ?? 'N/A', 50) }}</small>
                        </td>
                        <td>{{ Str::limit($violation->description, 60) }}</td>
                        <td>
                            @if($violation->discipline_type)
                                @if($violation->discipline_type == 'warning')
                                    <span class="badge bg-warning text-dark">Cảnh cáo</span>
                                @elseif($violation->discipline_type == 'reprimand')
                                    <span class="badge bg-info">Khiển trách</span>
                                @elseif($violation->discipline_type == 'suspension')
                                    <span class="badge bg-danger">Đình chỉ</span>
                                @elseif($violation->discipline_type == 'expulsion')
                                    <span class="badge bg-danger">Buộc rời CLB</span>
                                @elseif($violation->discipline_type == 'ban')
                                    <span class="badge bg-dark">Cấm tham gia</span>
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($violation->discipline_period_start && $violation->discipline_period_end)
                                <small>
                                    {{ \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') }}<br>
                                    <strong>→</strong><br>
                                    {{ \Carbon\Carbon::parse($violation->discipline_period_end)->format('d/m/Y') }}
                                </small>
                            @elseif($violation->discipline_period_start)
                                <small>Từ: {{ \Carbon\Carbon::parse($violation->discipline_period_start)->format('d/m/Y') }}</small>
                            @else
                                <span class="text-muted">Không giới hạn</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $violation->processor->name ?? 'N/A' }}</strong>
                            <br><small class="text-muted">(Admin)</small>
                        </td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse($violation->processed_at)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.violations.show', $violation->id) }}" 
                               class="btn btn-sm btn-info" title="Xem chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            Chưa có lịch sử kỷ luật nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $violations->links('vendor.pagination.custom') }}
    </div>
</div>

<style>
    .table tbody tr {
        background-color: white;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

@endsection

