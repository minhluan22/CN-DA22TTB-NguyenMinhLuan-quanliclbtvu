@extends('student.personal-statistics._layout')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @if(isset($pointsByYear) && count($pointsByYear) > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('pointsByYearChart');
            if (ctx) {
                const data = {!! json_encode($pointsByYear) !!};
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(data).map(year => 'Năm ' + year),
                        datasets: [{
                            label: 'Điểm hoạt động',
                            data: Object.values(data),
                            backgroundColor: '#0B3D91',
                            borderColor: '#072C6A',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 10
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endif
@endpush

@section('points-content')
<style>
.total-points-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-align: center;
    padding: 40px;
    border-radius: 16px;
    margin-bottom: 24px;
}
.total-points-card .value {
    font-size: 64px;
    font-weight: 700;
    margin-bottom: 8px;
}
.total-points-card .label {
    font-size: 18px;
    opacity: 0.9;
}
.chart-container {
    height: 300px;
    position: relative;
}
</style>

{{-- TOTAL POINTS CARD --}}
<div class="total-points-card">
    <div class="value">{{ number_format($totalPoints ?? 0) }}</div>
    <div class="label">Tổng điểm hoạt động tích lũy</div>
</div>

{{-- CHART --}}
@if(isset($pointsByYear) && count($pointsByYear) > 0)
    <div class="card">
        <h5 class="mb-3">Điểm hoạt động theo năm học</h5>
        <div class="chart-container">
            <canvas id="pointsByYearChart"></canvas>
        </div>
    </div>
@endif

{{-- FILTER --}}
<div class="card">
    <form method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">Năm học</label>
            <select name="year" class="form-control">
                <option value="">-- Tất cả --</option>
                @for($year = date('Y'); $year >= date('Y') - 3; $year--)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        Năm học {{ $year }}-{{ $year + 1 }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">CLB</label>
            <select name="club_id" class="form-control">
                <option value="">-- Tất cả --</option>
                @foreach($clubs as $club)
                    <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                        {{ $club->code }} - {{ $club->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Tìm
            </button>
        </div>
    </form>
</div>

{{-- TABLE --}}
<div class="card">
    <h5 class="mb-3">Chi tiết điểm từng hoạt động</h5>
    <table class="table-role">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên hoạt động</th>
                <th>CLB</th>
                <th>Thời gian</th>
                <th>Điểm</th>
                <th>Ngày ghi nhận</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pointsDetail as $index => $point)
                <tr>
                    <td>{{ ($pointsDetail->currentPage() - 1) * $pointsDetail->perPage() + $index + 1 }}</td>
                    <td><strong>{{ $point->title }}</strong></td>
                    <td>{{ $point->club_name }} ({{ $point->club_code }})</td>
                    <td>
                        @if($point->start_at)
                            {{ \Carbon\Carbon::parse($point->start_at)->format('d/m/Y H:i') }}
                            @if($point->end_at)
                                <br><small>→ {{ \Carbon\Carbon::parse($point->end_at)->format('d/m/Y H:i') }}</small>
                            @endif
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-success">{{ $point->activity_points }} điểm</span>
                    </td>
                    <td>
                        @if($point->point_date)
                            {{ \Carbon\Carbon::parse($point->point_date)->format('d/m/Y H:i') }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Chưa có điểm hoạt động nào</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $pointsDetail->links() }}
    </div>
</div>
@endsection
