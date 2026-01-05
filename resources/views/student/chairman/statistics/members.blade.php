@extends('layouts.chairman')

@section('title', 'Thống kê thành viên CLB - Chủ nhiệm CLB')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #FFF3A0;
    }
    
    .dashboard-container {
        padding: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }
    
    /* Page Header */
    .page-header {
        background: white;
        padding: 24px 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #0033A0;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    /* Club Info Card */
    .club-info-card {
        background: linear-gradient(135deg, #0033A0 0%, #0B3D91 100%);
        padding: 24px 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(0,51,160,0.2);
        color: white;
    }
    
    .club-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
    }
    
    .club-info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .club-info-label {
        font-size: 13px;
        opacity: 0.85;
        font-weight: 500;
    }
    
    .club-info-value {
        font-size: 18px;
        font-weight: 700;
    }
    
    .filter-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .chart-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .chart-container {
        height: 300px;
        position: relative;
    }
    
    .table-card {
        background: white;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .table-role tbody tr {
        transition: all 0.3s ease;
    }
    
    .table-role tbody tr:hover {
        background: #f8fafc;
        transform: translateY(-1px);
    }
    
    .table-role thead {
        background: #eaf2ff;
        color: #0B3D91;
    }
    
    .table-role thead th {
        background: #eaf2ff !important;
        color: #0B3D91 !important;
        font-weight: 700;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }
    
    .pagination {
        margin: 20px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0;
        list-style: none;
        padding: 0;
    }

    .pagination .page-item {
        margin: 0 2px;
        list-style: none;
    }

    .pagination .page-link {
        color: #0B3D91;
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 6px 12px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.15s ease;
        min-width: 38px;
        text-align: center;
        display: inline-block;
        text-decoration: none;
        line-height: 1.42857143;
        cursor: pointer;
    }

    .pagination .page-link:hover:not(.disabled):not([aria-disabled="true"]) {
        color: white;
        background-color: #0B3D91;
        border-color: #0B3D91;
        text-decoration: none;
    }

    .pagination .page-item.active .page-link {
        color: white;
        background-color: #0B3D91;
        border-color: #0B3D91;
        font-weight: 600;
        cursor: default;
        z-index: 1;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        opacity: 0.6;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-people"></i>
            Thống kê thành viên CLB
        </h1>
    </div>

    <!-- Club Info Card -->
    @if($chairmanClub)
        @php
            $clubModel = \App\Models\Club::find($chairmanClub->id);
        @endphp
        <div class="club-info-card">
            <div class="club-info-grid">
                <div class="club-info-item">
                    <span class="club-info-label">Tên Câu lạc bộ</span>
                    <span class="club-info-value">{{ $chairmanClub->name }}</span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Mã CLB</span>
                    <span class="club-info-value">{{ $chairmanClub->code }}</span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Trạng thái</span>
                    <span class="club-info-value">
                        @if($clubModel && $clubModel->status === 'active')
                            ✅ Hoạt động
                        @else
                            🔒 Ngừng hoạt động
                        @endif
                    </span>
                </div>
                <div class="club-info-item">
                    <span class="club-info-label">Vai trò của bạn</span>
                    <span class="club-info-value">Chủ nhiệm CLB</span>
                </div>
            </div>
        </div>
    @endif

    {{-- FILTER --}}
    <div class="filter-card">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoặc MSSV..." class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Chức vụ</label>
                <select name="position" class="form-control" onchange="this.form.submit()">
                    <option value="all">-- Tất cả --</option>
                    <option value="chairman" {{ request('position') == 'chairman' ? 'selected' : '' }}>Chủ nhiệm</option>
                    <option value="vice_chairman" {{ request('position') == 'vice_chairman' ? 'selected' : '' }}>Phó Chủ nhiệm</option>
                    <option value="secretary" {{ request('position') == 'secretary' ? 'selected' : '' }}>Thư ký</option>
                    <option value="head_expertise" {{ request('position') == 'head_expertise' ? 'selected' : '' }}>Trưởng ban Chuyên môn</option>
                    <option value="head_media" {{ request('position') == 'head_media' ? 'selected' : '' }}>Trưởng ban Truyền thông</option>
                    <option value="head_events" {{ request('position') == 'head_events' ? 'selected' : '' }}>Trưởng ban Sự kiện</option>
                    <option value="member" {{ request('position') == 'member' ? 'selected' : '' }}>Thành viên</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>

    {{-- CHARTS ROW --}}
    @if(isset($statsByPosition) && $statsByPosition->count() > 0)
        <div class="chart-card">
            <h5 class="mb-3 fw-bold">📊 Phân bố thành viên theo chức vụ</h5>
            <div class="chart-container">
                <canvas id="positionChart"></canvas>
            </div>
        </div>
    @endif

    {{-- DANH SÁCH THÀNH VIÊN --}}
    <div class="table-card">
        <table class="table table-role">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên thành viên</th>
                    <th>MSSV</th>
                    <th>Email</th>
                    <th>Chức vụ</th>
                    <th>Số hoạt động tham gia</th>
                    <th>Ngày tham gia</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $member)
                    <tr>
                        <td>{{ ($members->currentPage() - 1) * $members->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $member->name }}</strong></td>
                        <td>{{ $member->student_code }}</td>
                        <td>{{ $member->email }}</td>
                        <td>
                            @if($member->position == 'chairman')
                                <span class="badge bg-primary">Chủ nhiệm</span>
                            @elseif($member->position == 'vice_chairman')
                                <span class="badge bg-warning text-dark">Phó Chủ nhiệm</span>
                            @elseif($member->position == 'secretary')
                                <span class="badge bg-info">Thư ký</span>
                            @elseif($member->position == 'head_expertise')
                                <span class="badge bg-success">Trưởng ban Chuyên môn</span>
                            @elseif($member->position == 'head_media')
                                <span class="badge bg-success">Trưởng ban Truyền thông</span>
                            @elseif($member->position == 'head_events')
                                <span class="badge bg-success">Trưởng ban Sự kiện</span>
                            @else
                                <span class="badge bg-secondary">Thành viên</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $member->events_attended ?? 0 }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($member->join_date)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Chưa có dữ liệu</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($members->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $members->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(isset($statsByPosition) && $statsByPosition->count() > 0)
<script>
const positionCtx = document.getElementById('positionChart');
if (positionCtx) {
    const positionData = {!! json_encode($statsByPosition) !!};
    const labels = Object.keys(positionData).map(p => {
        const map = {
            'chairman': 'Chủ nhiệm',
            'vice_chairman': 'Phó Chủ nhiệm',
            'secretary': 'Thư ký',
            'head_expertise': 'Trưởng ban Chuyên môn',
            'head_media': 'Trưởng ban Truyền thông',
            'head_events': 'Trưởng ban Sự kiện',
            'member': 'Thành viên'
        };
        return map[p] || p;
    });
    const values = Object.values(positionData);
    
    new Chart(positionCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#0033A0',
                    '#0B3D91',
                    '#FFE600',
                    '#5FB84A',
                    '#8EDC6E',
                    '#FFF3A0',
                    '#dc3545'
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
                    labels: {
                        padding: 12,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12
                }
            }
        }
    });
}
</script>
@endif
@endpush
