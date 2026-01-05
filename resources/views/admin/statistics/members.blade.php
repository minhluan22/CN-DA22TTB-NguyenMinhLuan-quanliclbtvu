@extends('layouts.admin')

@section('title', 'Thống kê thành viên')

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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-people"></i> Thống kê thành viên
        </h2>
        <div class="badge" style="background-color: #0B3D91; color: white; padding: 8px 16px; font-size: 14px;">
            <i class="bi bi-calendar3"></i> Năm học {{ $academicYear }} - {{ $academicYear + 1 }}
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 fw-bold">Năm học</label>
                <select name="academic_year" class="form-control" onchange="this.form.submit()" style="border-color: #0B3D91;">
                    @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                        <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                            {{ $year }} - {{ $year + 1 }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 fw-bold">Khoa</label>
                <select name="department" class="form-control" onchange="this.form.submit()" style="border-color: #0B3D91;">
                    <option value="">-- Tất cả khoa --</option>
                    @foreach($availableDepartments as $dept)
                        <option value="{{ $dept }}" {{ $department == $dept ? 'selected' : '' }}>
                            {{ $dept }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1 fw-bold">Lớp</label>
                <select name="class" class="form-control" onchange="this.form.submit()" style="border-color: #0B3D91;">
                    <option value="">-- Tất cả lớp --</option>
                    @foreach($availableClasses as $cls)
                        <option value="{{ $cls }}" {{ $class == $cls ? 'selected' : '' }}>
                            {{ $cls }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100" style="background-color: #0B3D91; color: white;">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card primary">
                <div class="stat-icon" style="background: #0033A020; color: #0033A0;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-value">{{ number_format($totalMembers) }}</div>
                <div class="stat-label">Tổng số sinh viên tham gia CLB</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card success">
                <div class="stat-icon" style="background: #5FB84A20; color: #5FB84A;">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div class="stat-value">{{ number_format($newMembersThisYear) }}</div>
                <div class="stat-label">Thành viên mới trong năm học</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card info">
                <div class="stat-icon" style="background: #0B3D9120; color: #0B3D91;">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="stat-value">{{ number_format($membersInMultipleClubsCount) }}</div>
                <div class="stat-label">Thành viên tham gia nhiều CLB</div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="chart-title">
                    <i class="bi bi-bar-chart"></i> Tỷ lệ sinh viên theo khoa
                </div>
                <div style="height: 350px;">
                    <canvas id="membersByDepartmentChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="chart-title">
                    <i class="bi bi-pie-chart"></i> Phân bổ vai trò trong CLB
                </div>
                <div style="height: 350px;">
                    <canvas id="rolesDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="chart-title">
                    <i class="bi bi-graph-up"></i> Tăng trưởng thành viên theo tháng
                </div>
                <div style="height: 300px;">
                    <canvas id="monthlyMembersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Rankings Row --}}
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card">
                <div class="chart-title">
                    <i class="bi bi-trophy"></i> Top 10 - Tham gia nhiều CLB
                </div>
                <div>
                    @foreach($membersInMultipleClubs as $index => $member)
                        <div class="ranking-card">
                            <div class="d-flex align-items-center">
                                <div class="ranking-number {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $member->name }}</div>
                                    <small class="text-muted">{{ $member->student_code }}</small>
                                    @if($member->department)
                                        <br><small class="badge" style="background-color: #8EDC6E; color: #000;">{{ $member->department }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: #0033A0;">{{ $member->club_count }}</div>
                                    <small class="text-muted">CLB</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="chart-title">
                    <i class="bi bi-trophy"></i> Top 10 - Thành viên tích cực
                </div>
                <div>
                    @foreach($activeMembers as $index => $member)
                        <div class="ranking-card">
                            <div class="d-flex align-items-center">
                                <div class="ranking-number {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'default')) }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $member->name }}</div>
                                    <small class="text-muted">{{ $member->student_code }}</small>
                                    @if($member->department)
                                        <br><small class="badge" style="background-color: #8EDC6E; color: #000;">{{ $member->department }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold" style="color: #0033A0;">{{ $member->participation_count }}</div>
                                    <small class="text-muted">lần tham gia</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Tỷ lệ sinh viên theo khoa
const membersByDepartmentCtx = document.getElementById('membersByDepartmentChart');
if (membersByDepartmentCtx) {
    const membersByDepartment = {!! json_encode($membersByDepartment) !!};
    new Chart(membersByDepartmentCtx, {
        type: 'bar',
        data: {
            labels: membersByDepartment.map(item => item.department || 'N/A'),
            datasets: [{
                label: 'Số sinh viên',
                data: membersByDepartment.map(item => item.count),
                backgroundColor: '#0033A0',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
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

// Phân bổ vai trò
const rolesDistributionCtx = document.getElementById('rolesDistributionChart');
if (rolesDistributionCtx) {
    const rolesDistribution = {!! json_encode($rolesDistribution) !!};
    const positionMap = {
        'chairman': 'Chủ nhiệm',
        'vice_chairman': 'Phó chủ nhiệm',
        'secretary': 'Thư ký CLB',
        'head_expertise': 'Trưởng ban Chuyên môn',
        'head_media': 'Trưởng ban Truyền thông',
        'head_events': 'Trưởng ban Hoạt động',
        'treasurer': 'Trưởng ban Tài chính',
        'member': 'Thành viên'
    };
    new Chart(rolesDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: rolesDistribution.map(item => positionMap[item.position] || item.position),
            datasets: [{
                data: rolesDistribution.map(item => item.count),
                backgroundColor: [
                    '#0033A0', '#FFE600', '#0B3D91', '#5FB84A', 
                    '#8EDC6E', '#FFF3A0', '#dc3545', '#9333ea'
                ],
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

// Tăng trưởng thành viên theo tháng
const monthlyMembersCtx = document.getElementById('monthlyMembersChart');
if (monthlyMembersCtx) {
    const monthlyMembers = {!! json_encode($monthlyMembers) !!};
    new Chart(monthlyMembersCtx, {
        type: 'line',
        data: {
            labels: monthlyMembers.map(item => item.month),
            datasets: [{
                label: 'Tổng thành viên',
                data: monthlyMembers.map(item => item.count),
                borderColor: '#8EDC6E',
                backgroundColor: 'rgba(142, 220, 110, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#5FB84A',
                pointBorderColor: '#5FB84A',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
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
