@extends('student.personal-statistics._layout')

@section('club-history-content')

{{-- SUMMARY CARDS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="value">{{ $totalClubs ?? 0 }}</div>
        <div class="label">Tổng CLB đã tham gia</div>
    </div>
    <div class="stat-card">
        <div class="value">{{ $activeClubs ?? 0 }}</div>
        <div class="label">Đang tham gia</div>
    </div>
    <div class="stat-card">
        <div class="value">{{ $leftClubs ?? 0 }}</div>
        <div class="label">Đã rời CLB</div>
    </div>
</div>

{{-- FILTER --}}
<div class="card">
    <form method="GET" class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-bold">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoặc mã CLB..." class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">Trạng thái</label>
            <select name="status" class="form-control">
                <option value="all">-- Tất cả --</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang tham gia</option>
                <option value="left" {{ request('status') == 'left' ? 'selected' : '' }}>Đã rời CLB</option>
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
    <h5 class="mb-3">Danh sách CLB đã và đang tham gia</h5>
    <table class="table-role">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên CLB</th>
                <th>Lĩnh vực</th>
                <th>Vai trò</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clubHistory as $index => $club)
                <tr>
                    <td>{{ ($clubHistory->currentPage() - 1) * $clubHistory->perPage() + $index + 1 }}</td>
                    <td><strong>{{ $club->club_name }}</strong><br><small>{{ $club->club_code }}</small></td>
                    <td>{{ \App\Models\Club::getFieldDisplay($club->club_field) }}</td>
                    <td>
                        @if($club->position == 'chairman')
                            <span class="badge badge-primary">Chủ nhiệm</span>
                        @elseif($club->position == 'vice_chairman')
                            <span class="badge badge-warning">Phó Chủ nhiệm</span>
                        @elseif($club->position == 'secretary')
                            <span class="badge badge-info">Thư ký</span>
                        @elseif($club->position == 'head_expertise')
                            <span class="badge badge-info">Trưởng ban Chuyên môn</span>
                        @elseif($club->position == 'head_media')
                            <span class="badge badge-info">Trưởng ban Truyền thông</span>
                        @elseif($club->position == 'head_events')
                            <span class="badge badge-info">Trưởng ban Sự kiện</span>
                        @else
                            <span class="badge badge-secondary">Thành viên</span>
                        @endif
                    </td>
                    <td>
                        @if($club->joined_at)
                            {{ \Carbon\Carbon::parse($club->joined_at)->format('d/m/Y') }}
                        @endif
                    </td>
                    <td>
                        @if($club->left_at)
                            {{ \Carbon\Carbon::parse($club->left_at)->format('d/m/Y') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($club->status == 'approved' && !$club->left_at)
                            <span class="badge badge-success">Đang tham gia</span>
                        @else
                            <span class="badge badge-secondary">Đã rời CLB</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Chưa có lịch sử tham gia CLB nào</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $clubHistory->links() }}
    </div>
</div>
@endsection
