@extends('layouts.admin')

@section('title', 'Danh sách hoạt động')

@section('content')

<div class="container-fluid mt-3">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-calendar-event"></i> Danh sách hoạt động
        </h3>
    </div>

    {{-- FILTER FORM --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
            <div class="row g-2">
                <div class="col-md-3">
                <label class="form-label small fw-bold mb-1">CLB</label>
                <select name="club_id" class="form-select">
                    <option value="">-- Tất cả CLB --</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                            {{ $club->code }} - {{ $club->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Trạng thái diễn ra</label>
                <select name="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                    <option value="finished" {{ request('status') == 'finished' ? 'selected' : '' }}>Đã kết thúc</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Trạng thái duyệt</label>
                <select name="approval_status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="pending" {{ request('approval_status') == 'pending' ? 'selected' : '' }}>Chờ duyệt (Đề xuất)</option>
                    <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    <option value="disabled" {{ request('approval_status') == 'disabled' ? 'selected' : '' }}>Bị vô hiệu hóa</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Từ ngày</label>
                <input type="date" name="start_date" class="form-control form-control-sm" 
                       value="{{ request('start_date') }}" placeholder="Từ ngày">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">Đến ngày</label>
                <input type="date" name="end_date" class="form-control form-control-sm" 
                       value="{{ request('end_date') }}" placeholder="Đến ngày">
            </div>

            <div class="col-md-1">
                <label class="form-label small fw-bold mb-1">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Tìm
                </button>
            </div>
        </div>

        <div class="row g-2 mt-1">
            <div class="col-md-12">
                <label class="form-label small fw-bold mb-1">Từ khóa</label>
                <input type="text" name="search" class="form-control form-control-sm" 
                       value="{{ request('search') }}" placeholder="Tìm kiếm theo tên hoạt động, CLB...">
            </div>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Tên hoạt động</th>
                    <th>CLB</th>
                    <th>Người tạo</th>
                    <th>Thời gian</th>
                    <th>Địa điểm</th>
                    <th>
                        <span data-bs-toggle="tooltip" title="Đã duyệt (nếu sắp diễn ra) hoặc Đã tham gia (nếu đã kết thúc)">
                            Duyệt/Tham gia
                        </span>
                        / Đăng ký
                    </th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $index => $activity)
                    <tr class="{{ $activity->approval_status == 'pending' ? 'table-warning' : '' }}">
                        <td>{{ $activities->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $activity->title }}</strong>
                            @if($activity->violation_notes)
                                <span class="badge bg-danger" title="Có vi phạm">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $activity->club_code }}</small><br>
                            {{ $activity->club_name }}
                        </td>
                        <td>
                            @if($activity->creator_name)
                                <strong>{{ $activity->creator_name }}</strong>
                                @if($activity->creator_student_code)
                                    <br><small class="text-muted">({{ $activity->creator_student_code }})</small>
                                @endif
                                <br>
                                @if($activity->creator_position)
                                    @if($activity->creator_position == 'chairman')
                                        <span class="badge" style="background-color: #0033A0; color: white;">Chủ nhiệm</span>
                                    @elseif($activity->creator_position == 'vice_chairman')
                                        <span class="badge" style="background-color: #FFE600; color: #000;">Phó chủ nhiệm</span>
                                    @elseif($activity->creator_position == 'secretary')
                                        <span class="badge" style="background-color: #0B3D91; color: white;">Thư ký CLB</span>
                                    @elseif($activity->creator_position == 'head_expertise')
                                        <span class="badge" style="background-color: #5FB84A; color: white;">Trưởng ban Chuyên môn</span>
                                    @elseif($activity->creator_position == 'head_media')
                                        <span class="badge" style="background-color: #8EDC6E; color: #000;">Trưởng ban Truyền thông</span>
                                    @elseif($activity->creator_position == 'head_events')
                                        <span class="badge" style="background-color: #FFE600; color: #000;">Trưởng ban Sự kiện</span>
                                    @elseif($activity->creator_position == 'treasurer')
                                        <span class="badge" style="background-color: #0066CC; color: white;">Trưởng ban Tài chính</span>
                                    @elseif($activity->creator_position == 'member' || $activity->creator_position == '' || empty($activity->creator_position))
                                        <span class="badge" style="background-color: #6BCB77; color: white;">Sinh viên</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $activity->creator_position)) }}</span>
                                    @endif
                                @elseif($activity->owner_id == $activity->creator_id)
                                    <span class="badge" style="background-color: #0033A0; color: white;">Chủ nhiệm</span>
                                @else
                                    <span class="badge" style="background-color: #6BCB77; color: white;">Sinh viên</span>
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse($activity->start_at)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $activity->location ?? 'Chưa cập nhật' }}</span>
                        </td>
                        <td>
                            <span class="badge" style="background-color: #5FB84A; color: white;"
                                  data-bs-toggle="tooltip" 
                                  title="{{ $activity->status === 'finished' ? 'Số người đã tham gia (check-in)' : 'Số người đã được duyệt' }}">
                                {{ $activity->participant_count ?? 0 }}
                            </span> /
                            <span class="badge" style="background-color: #0B3D91; color: white;" 
                                  data-bs-toggle="tooltip" title="Số người đã đăng ký">
                                {{ $activity->registered_count ?? 0 }}
                            </span>
                        </td>
                        <td>
                            {{-- Trạng thái duyệt (ưu tiên hiển thị) --}}
                            <div style="margin-bottom: 4px;">
                                @if($activity->approval_status == 'pending')
                                    <span class="badge" style="background-color: #FFE600; color: #000; font-weight: 600;">Chờ duyệt</span>
                                @elseif($activity->approval_status == 'approved')
                                    <span class="badge" style="background-color: #5FB84A; color: white;">Đã duyệt</span>
                                @elseif($activity->approval_status == 'rejected')
                                    <span class="badge" style="background-color: #B84A5F; color: white;">Từ chối</span>
                                @elseif($activity->status == 'disabled')
                                    <span class="badge" style="background-color: #B84A5F; color: white;">Bị vô hiệu hóa</span>
                                @endif
                            </div>
                            {{-- Trạng thái diễn ra (chỉ hiển thị nếu đã duyệt) --}}
                            @if($activity->approval_status == 'approved')
                                <div>
                                    @if($activity->status == 'upcoming')
                                        <span class="badge" style="background-color: #8EDC6E; color: #000;">Sắp diễn ra</span>
                                    @elseif($activity->status == 'ongoing')
                                        <span class="badge" style="background-color: #0B3D91; color: white;">Đang diễn ra</span>
                                    @elseif($activity->status == 'finished')
                                        <span class="badge" style="background-color: #5FB84A; color: white;">Đã kết thúc</span>
                                    @elseif($activity->status == 'cancelled')
                                        <span class="badge" style="background-color: #6c757d; color: #fff;">Đã hủy</span>
                                    @elseif($activity->status == 'disabled')
                                        <span class="badge" style="background-color: #B84A5F; color: white;">Bị vô hiệu hóa</span>
                                    @endif
                                </div>
                            @else
                                {{-- Nếu chờ duyệt, luôn hiển thị "Sắp diễn ra" --}}
                                <div>
                                    <span class="badge" style="background-color: #8EDC6E; color: #000;">Sắp diễn ra</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            {{-- Chỉ có nút Xem chi tiết ở trang danh sách hoạt động --}}
                            <a href="{{ route('admin.activities.show', $activity->id) }}" 
                               class="btn btn-sm" style="background-color: #0B3D91; color: white;" title="Xem chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Không có hoạt động nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

            {{-- PAGINATION --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $activities->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Khởi tạo tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

