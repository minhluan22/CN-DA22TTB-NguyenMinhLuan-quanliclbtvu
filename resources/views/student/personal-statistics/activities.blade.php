@extends('student.personal-statistics._layout')

@section('activities-content')


        {{-- SUMMARY CARDS --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="value">{{ $totalRegistered ?? 0 }}</div>
                <div class="label">Tổng đã đăng ký</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $attended ?? 0 }}</div>
                <div class="label">Đã tham gia</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $absent ?? 0 }}</div>
                <div class="label">Đăng ký nhưng không tham gia</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $cancelled ?? 0 }}</div>
                <div class="label">Bị hủy</div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="card">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoạt động..." class="form-control">
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label class="form-label fw-bold">Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="all">-- Tất cả --</option>
                        <option value="attended" {{ request('status') == 'attended' ? 'selected' : '' }}>Đã tham gia</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Từ ngày</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Đến ngày</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="card">
            <h5 class="mb-3">Danh sách hoạt động đã tham gia</h5>
            <table class="table-role">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên hoạt động</th>
                        <th>CLB tổ chức</th>
                        <th>Thời gian</th>
                        <th>Địa điểm</th>
                        <th>Trạng thái tham gia</th>
                        <th>Điểm</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $index => $activity)
                        <tr>
                            <td>{{ ($activities->currentPage() - 1) * $activities->perPage() + $index + 1 }}</td>
                            <td><strong>{{ $activity->title }}</strong></td>
                            <td>{{ $activity->club_name }} ({{ $activity->club_code }})</td>
                            <td>
                                @if($activity->start_at)
                                    {{ \Carbon\Carbon::parse($activity->start_at)->format('d/m/Y H:i') }}
                                    @if($activity->end_at)
                                        <br><small>→ {{ \Carbon\Carbon::parse($activity->end_at)->format('d/m/Y H:i') }}</small>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $activity->location ?? 'Chưa cập nhật' }}</td>
                            <td>
                                @if($activity->registration_status == 'attended')
                                    <span class="badge badge-success">Đã tham gia</span>
                                @elseif($activity->registration_status == 'approved')
                                    <span class="badge badge-info">Đã duyệt</span>
                                @elseif($activity->registration_status == 'pending')
                                    <span class="badge badge-warning">Chờ duyệt</span>
                                @elseif($activity->registration_status == 'rejected')
                                    <span class="badge badge-danger">Từ chối</span>
                                @elseif($activity->event_status == 'cancelled')
                                    <span class="badge badge-secondary">Bị hủy</span>
                                @else
                                    <span class="badge badge-secondary">Đăng ký nhưng không tham gia</span>
                                @endif
                            </td>
                            <td>
                                @if($activity->activity_points > 0)
                                    <span class="badge badge-success">{{ $activity->activity_points }} điểm</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Chưa có hoạt động nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-4">
                {{ $activities->links() }}
            </div>
        </div>
@endsection

