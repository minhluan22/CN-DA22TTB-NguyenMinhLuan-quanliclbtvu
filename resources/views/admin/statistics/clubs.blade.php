@extends('layouts.admin')

@section('title', 'Thống kê câu lạc bộ')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        height: 100%;
        border-left: 4px solid transparent;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    .stat-card.primary { border-left-color: #0033A0; }
    .stat-card.success { border-left-color: #5FB84A; }
    .stat-card.warning { border-left-color: #FFE600; }
    .stat-card.info { border-left-color: #0B3D91; }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }
    .stat-value {
        font-size: 36px;
        font-weight: 700;
        color: #0033A0;
        margin: 8px 0;
    }
    .stat-label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }
    
    .ranking-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .ranking-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(4px);
    }
    .ranking-number {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 14px;
        margin-right: 12px;
    }
    .ranking-number.gold { background: linear-gradient(135deg, #FFE600, #FFD700); }
    .ranking-number.silver { background: linear-gradient(135deg, #C0C0C0, #A8A8A8); }
    .ranking-number.bronze { background: linear-gradient(135deg, #CD7F32, #B8860B); }
    .ranking-number.default { background: #0B3D91; }
</style>
@endpush

@section('content')
<div class="container-fluid mt-3">
    <h3 class="fw-bold mb-4">
        <i class="bi bi-building"></i> Thống kê câu lạc bộ
    </h3>

    {{-- Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold mb-1">Lĩnh vực</label>
                    <select name="field" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Tất cả lĩnh vực --</option>
                        @foreach($availableFields as $availableField)
                            <option value="{{ $availableField }}" {{ $field == $availableField ? 'selected' : '' }}>
                                {{ $availableField }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card primary">
                <div class="card-body">
                    <div class="stat-icon" style="background: #0033A020; color: #0033A0;">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="stat-value">{{ number_format($totalClubs) }}</div>
                    <div class="stat-label">Tổng số CLB</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card success">
                <div class="card-body">
                    <div class="stat-icon" style="background: #5FB84A20; color: #5FB84A;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-value">{{ number_format($activeClubs) }}</div>
                    <div class="stat-label">CLB đang hoạt động</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card info">
                <div class="card-body">
                    <div class="stat-icon" style="background: #0B3D9120; color: #0B3D91;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-value">{{ number_format($totalMembers) }}</div>
                    <div class="stat-label">Tổng thành viên</div>
                    <small class="text-muted">TB: {{ $avgMembersPerClub }}/CLB</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card warning">
                <div class="card-body">
                    <div class="stat-icon" style="background: #FFE60020; color: #FFE600;">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="stat-value">{{ number_format($totalEvents) }}</div>
                    <div class="stat-label">Tổng hoạt động</div>
                    <small class="text-muted">TB: {{ $avgEventsPerClub }}/CLB</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row g-4 mb-4">
        {{-- Biểu đồ tần suất hoạt động --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-graph-up"></i> Tần suất hoạt động theo tháng</h5>
                    <div style="height: 350px;">
                        <canvas id="activityFrequencyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biểu đồ tỷ lệ CLB --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-pie-chart"></i> Tỷ lệ trạng thái CLB</h5>
                    <div style="height: 350px;">
                        <canvas id="clubStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mb-4">
        {{-- Phân bố CLB theo lĩnh vực --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-diagram-3"></i> Phân bố CLB theo lĩnh vực</h5>
                    <div style="height: 300px;">
                        <canvas id="clubsByFieldChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rankings Row --}}
    <div class="row g-4 mb-4">
        {{-- Top CLB theo thành viên --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-trophy"></i> Top 10 CLB - Thành viên</h5>
                <div>
                    @foreach($topClubsByMembers as $index => $club)
                        <div class="ranking-card">
                            <div class="d-flex align-items-center">
                                <div class="ranking-number {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $club->name }}</div>
                                    <small class="text-muted">{{ $club->code }}</small>
                                    @if($club->field_display)
                                        <br><small class="badge" style="background-color: #8EDC6E; color: #000;">{{ $club->field_display }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: #0033A0;">{{ $club->member_count }}</div>
                                    <small class="text-muted">thành viên</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            </div>
        </div>

        {{-- Top CLB theo hoạt động --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-trophy"></i> Top 10 CLB - Hoạt động</h5>
                <div>
                    @foreach($topClubsByEvents as $index => $club)
                        <div class="ranking-card">
                            <div class="d-flex align-items-center">
                                <div class="ranking-number {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $club->name }}</div>
                                    <small class="text-muted">{{ $club->code }}</small>
                                    @if($club->field_display)
                                        <br><small class="badge" style="background-color: #8EDC6E; color: #000;">{{ $club->field_display }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: #0033A0;">{{ $club->event_count }}</div>
                                    <small class="text-muted">hoạt động</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            </div>
        </div>

        {{-- Top CLB theo tham gia --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-trophy"></i> Top 10 CLB - Tham gia</h5>
                <div>
                    @foreach($topClubsByParticipants as $index => $club)
                        <div class="ranking-card">
                            <div class="d-flex align-items-center">
                                <div class="ranking-number {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $club->name }}</div>
                                    <small class="text-muted">{{ $club->code }}</small>
                                    @if($club->field_display)
                                        <br><small class="badge" style="background-color: #8EDC6E; color: #000;">{{ $club->field_display }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: #0033A0;">{{ $club->participant_count }}</div>
                                    <small class="text-muted">người tham gia</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách tất cả CLB --}}
    <div class="table-card mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Danh sách tất cả CLB</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Mã CLB</th>
                        <th>Tên CLB</th>
                        <th>Lĩnh vực</th>
                        <th>Số thành viên</th>
                        <th>Số hoạt động</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allClubs as $index => $club)
                        <tr>
                            <td>{{ $allClubs->firstItem() + $index }}</td>
                            <td><strong>{{ $club->code }}</strong></td>
                            <td>{{ $club->name }}</td>
                            <td>
                                @if($club->field_display)
                                    <span class="badge" style="background-color: #8EDC6E; color: #000;">{{ $club->field_display }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold" style="color: #0033A0;">{{ $club->member_count }}</span>
                            </td>
                            <td>
                                <span class="fw-bold" style="color: #0B3D91;">{{ $club->event_count }}</span>
                            </td>
                            <td>
                                @if($club->status == 'active')
                                    <span class="badge" style="background-color: #5FB84A; color: white;">Đang hoạt động</span>
                                @elseif($club->status == 'archived')
                                    <span class="badge bg-secondary">Tạm dừng</span>
                                @else
                                    <span class="badge" style="background-color: #FFE600; color: #000;">Chờ duyệt</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.clubs.index') }}?club_id={{ $club->id }}" 
                                   class="btn btn-sm" style="background-color: #0B3D91; color: white;" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Chưa có CLB nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $allClubs->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Biểu đồ tần suất hoạt động
const activityFrequencyCtx = document.getElementById('activityFrequencyChart');
if (activityFrequencyCtx) {
    const activityFrequency = {!! json_encode($activityFrequency) !!};
    new Chart(activityFrequencyCtx, {
        type: 'line',
        data: {
            labels: activityFrequency.map(item => item.month),
            datasets: [{
                label: 'Số hoạt động',
                data: activityFrequency.map(item => item.count),
                borderColor: '#0033A0',
                backgroundColor: 'rgba(0, 51, 160, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#FFE600',
                pointBorderColor: '#0033A0',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });
}

// Biểu đồ tỷ lệ trạng thái CLB
const clubStatusCtx = document.getElementById('clubStatusChart');
if (clubStatusCtx) {
    const clubStatusRatio = {!! json_encode($clubStatusRatio) !!};
    new Chart(clubStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Đang hoạt động', 'Tạm dừng', 'Chờ duyệt'],
            datasets: [{
                data: [
                    clubStatusRatio.active || 0,
                    clubStatusRatio.archived || 0,
                    clubStatusRatio.pending || 0
                ],
                backgroundColor: ['#5FB84A', '#6b7280', '#FFE600'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 12, font: { size: 12 } }
                }
            }
        }
    });
}

// Biểu đồ phân bố CLB theo lĩnh vực
const clubsByFieldCtx = document.getElementById('clubsByFieldChart');
if (clubsByFieldCtx) {
    const clubsByField = {!! json_encode($clubsByField) !!};
    new Chart(clubsByFieldCtx, {
        type: 'bar',
        data: {
            labels: clubsByField.map(item => item.field || 'Khác'),
            datasets: [{
                label: 'Số CLB',
                data: clubsByField.map(item => item.count),
                backgroundColor: [
                    '#0033A0', '#0B3D91', '#5FB84A', '#8EDC6E', 
                    '#FFE600', '#FFF3A0', '#dc3545', '#9333ea'
                ],
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });
}
</script>
@endpush
