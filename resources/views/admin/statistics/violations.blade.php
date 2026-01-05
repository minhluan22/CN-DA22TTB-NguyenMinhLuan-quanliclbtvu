@extends('layouts.admin')

@section('title', 'Thống kê vi phạm - kỷ luật')

@section('content')
<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-exclamation-triangle"></i> Thống kê vi phạm - kỷ luật
    </h3>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">CLB</label>
                    <select name="club_id" class="form-select">
                    <option value="">-- Tất cả CLB --</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ $clubId == $club->id ? 'selected' : '' }}>
                            {{ $club->code }} - {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Từ ngày</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Đến ngày</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary --}}
    <div class="card">
        <div class="h3 fw-bold text-primary mb-0">{{ $totalViolations }}</div>
        <div class="text-muted">Tổng số vụ vi phạm</div>
    </div>

    {{-- Charts --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <h5 class="mb-3">📊 Vi phạm theo CLB</h5>
                <div style="height: 300px;">
                    <canvas id="violationsByClubChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <h5 class="mb-3">📊 Vi phạm theo mức độ</h5>
                <div style="height: 300px;">
                    <canvas id="violationsBySeverityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Violations List --}}
    <div class="card">
        <h5 class="mb-3">Danh sách vi phạm</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Sinh viên</th>
                        <th>CLB</th>
                        <th>Nội quy vi phạm</th>
                        <th>Mức độ</th>
                        <th>Thời gian</th>
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
                            <td>{{ $violation->club->name ?? 'N/A' }}</td>
                            <td>{{ $violation->regulation->title ?? 'N/A' }}</td>
                            <td>
                                @if($violation->severity == 'light')
                                    <span class="badge bg-success">Nhẹ</span>
                                @elseif($violation->severity == 'medium')
                                    <span class="badge bg-warning">Trung bình</span>
                                @else
                                    <span class="badge bg-danger">Nghiêm trọng</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.violations.show', $violation->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Chưa có vi phạm nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $violations->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Vi phạm theo CLB
const violationsByClubCtx = document.getElementById('violationsByClubChart');
if (violationsByClubCtx) {
    const violationsByClub = {!! json_encode($violationsByClub) !!};
    new Chart(violationsByClubCtx, {
        type: 'bar',
        data: {
            labels: violationsByClub.map(item => item.club_name),
            datasets: [{
                label: 'Số vi phạm',
                data: violationsByClub.map(item => item.violation_count),
                backgroundColor: '#ef4444'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// Vi phạm theo mức độ
const violationsBySeverityCtx = document.getElementById('violationsBySeverityChart');
if (violationsBySeverityCtx) {
    const violationsBySeverity = {!! json_encode($violationsBySeverity) !!};
    const severityLabels = {
        'light': 'Nhẹ',
        'medium': 'Trung bình',
        'serious': 'Nghiêm trọng'
    };
    new Chart(violationsBySeverityCtx, {
        type: 'doughnut',
        data: {
            labels: violationsBySeverity.map(item => severityLabels[item.severity] || item.severity),
            datasets: [{
                data: violationsBySeverity.map(item => item.count),
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
</script>
@endpush

