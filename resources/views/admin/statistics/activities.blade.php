@extends('layouts.admin')

@section('title', 'Thống kê hoạt động - sự kiện')

@section('content')
<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-calendar-event"></i> Thống kê hoạt động - sự kiện
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

    {{-- Summary Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 fw-bold text-success">{{ $completedEvents->total() }}</div>
                    <div class="text-muted">Hoạt động đã tổ chức</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 fw-bold text-danger">{{ $cancelledEvents }}</div>
                    <div class="text-muted">Hoạt động bị hủy</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h3 fw-bold text-warning">{{ $violatedEvents }}</div>
                    <div class="text-muted">Hoạt động vi phạm nội quy</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">📊 Số hoạt động theo tháng</h5>
                    <div style="height: 300px;">
                        <canvas id="eventsByMonthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">📊 Hoạt động theo CLB</h5>
                    <div style="height: 300px;">
                        <canvas id="eventsByClubChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Events List --}}
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Danh sách hoạt động đã tổ chức</h5>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Tên hoạt động</th>
                            <th>CLB</th>
                            <th>Thời gian</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($completedEvents as $index => $event)
                            <tr>
                                <td>{{ $completedEvents->firstItem() + $index }}</td>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->club->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.activities.show', $event->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có hoạt động nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $completedEvents->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Hoạt động theo tháng
const eventsByMonthCtx = document.getElementById('eventsByMonthChart');
if (eventsByMonthCtx) {
    const eventsByMonth = {!! json_encode($eventsByMonth) !!};
    new Chart(eventsByMonthCtx, {
        type: 'bar',
        data: {
            labels: eventsByMonth.map(item => item.month),
            datasets: [{
                label: 'Số hoạt động',
                data: eventsByMonth.map(item => item.count),
                backgroundColor: '#0B3D91'
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

// Hoạt động theo CLB
const eventsByClubCtx = document.getElementById('eventsByClubChart');
if (eventsByClubCtx) {
    const eventsByClub = {!! json_encode($eventsByClub) !!};
    new Chart(eventsByClubCtx, {
        type: 'doughnut',
        data: {
            labels: eventsByClub.map(item => item.club_name),
            datasets: [{
                data: eventsByClub.map(item => item.event_count),
                backgroundColor: ['#0B3D91', '#10b981', '#f59e0b', '#ef4444', '#9333ea']
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

