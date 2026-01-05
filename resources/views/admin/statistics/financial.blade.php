@extends('layouts.admin')

@section('title', 'Báo cáo tài chính CLB')

@section('content')
<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-cash-coin"></i> Báo cáo tài chính CLB
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
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="h3 fw-bold text-success">{{ number_format($totalRevenue, 0, ',', '.') }} đ</div>
                <div class="text-muted">Tổng kinh phí thu</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="h3 fw-bold text-danger">{{ number_format($totalExpenses, 0, ',', '.') }} đ</div>
                <div class="text-muted">Tổng kinh phí chi</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="h3 fw-bold text-primary">{{ number_format($totalRevenue - $totalExpenses, 0, ',', '.') }} đ</div>
                <div class="text-muted">Số dư</div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <h5 class="mb-3">📊 Thu - chi theo thời gian</h5>
                <div style="height: 300px;">
                    <canvas id="financialByMonthChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <h5 class="mb-3">📊 Số dư theo CLB</h5>
                <div style="height: 300px;">
                    <canvas id="balanceByClubChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Events with Budget --}}
    <div class="card">
        <h5 class="mb-3">Danh sách hoạt động có kinh phí</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên hoạt động</th>
                        <th>CLB</th>
                        <th>Kinh phí</th>
                        <th>Thời gian</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventsWithBudget as $index => $event)
                        <tr>
                            <td>{{ $eventsWithBudget->firstItem() + $index }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->club->name ?? 'N/A' }}</td>
                            <td><strong>{{ number_format($event->expected_budget, 0, ',', '.') }} đ</strong></td>
                            <td>{{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.activities.show', $event->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Chưa có hoạt động nào có kinh phí</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $eventsWithBudget->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Thu - chi theo tháng
const financialByMonthCtx = document.getElementById('financialByMonthChart');
if (financialByMonthCtx) {
    const financialByMonth = {!! json_encode($financialByMonth) !!};
    new Chart(financialByMonthCtx, {
        type: 'bar',
        data: {
            labels: financialByMonth.map(item => item.month),
            datasets: [
                {
                    label: 'Thu',
                    data: financialByMonth.map(item => item.revenue),
                    backgroundColor: '#10b981'
                },
                {
                    label: 'Chi',
                    data: financialByMonth.map(item => item.expenses),
                    backgroundColor: '#ef4444'
                }
            ]
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

// Số dư theo CLB
const balanceByClubCtx = document.getElementById('balanceByClubChart');
if (balanceByClubCtx) {
    const balanceByClub = {!! json_encode($balanceByClub) !!};
    new Chart(balanceByClubCtx, {
        type: 'doughnut',
        data: {
            labels: balanceByClub.map(item => item.club_name),
            datasets: [{
                data: balanceByClub.map(item => item.total_budget),
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

