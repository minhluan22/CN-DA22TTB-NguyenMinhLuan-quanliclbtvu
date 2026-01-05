@extends('layouts.admin')

@section('title', 'Chi tiết hoạt động')

@section('content')

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-info-circle"></i> Chi tiết hoạt động
        </h3>
        <a href="{{ route('admin.activities.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- THÔNG TIN HOẠT ĐỘNG --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Thông tin hoạt động</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tên hoạt động:</strong> {{ $activity->title }}</p>
                    <p><strong>CLB tổ chức:</strong> {{ $activity->club_name }} ({{ $activity->club_code }})</p>
                    <p><strong>Người tạo:</strong> 
                        @if($activity->creator_name)
                            {{ $activity->creator_name }}
                            @if($activity->creator_student_code)
                                ({{ $activity->creator_student_code }})
                            @endif
                            @if($activity->creator_position)
                                - 
                                @if($activity->creator_position == 'chairman')
                                    <span class="badge bg-primary">Chủ nhiệm</span>
                                @elseif($activity->creator_position == 'vice_chairman')
                                    <span class="badge bg-info">Phó chủ nhiệm</span>
                                @elseif($activity->creator_position == 'member' || $activity->creator_position == '' || empty($activity->creator_position))
                                    <span class="badge bg-secondary">Sinh viên</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $activity->creator_position)) }}</span>
                                @endif
                            @elseif($activity->owner_id == $activity->creator_id)
                                - <span class="badge bg-primary">Chủ nhiệm</span>
                            @else
                                - <span class="badge bg-secondary">Sinh viên</span>
                            @endif
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </p>
                    <p><strong>Mô tả:</strong></p>
                    <div class="border p-3 rounded bg-light">
                        {!! nl2br(e($activity->description ?? 'Không có mô tả')) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <p><strong>Thời gian bắt đầu:</strong> 
                        {{ \Carbon\Carbon::parse($activity->start_at)->format('d/m/Y H:i') }}
                    </p>
                    <p><strong>Thời gian kết thúc:</strong> 
                        {{ \Carbon\Carbon::parse($activity->end_at)->format('d/m/Y H:i') }}
                    </p>
                    <p><strong>Địa điểm tổ chức:</strong> 
                        <span class="fw-bold text-primary">{{ $activity->location ?? 'Chưa cập nhật' }}</span>
                    </p>
                    <p><strong>Trạng thái:</strong>
                        @if($activity->status == 'upcoming')
                            <span class="badge bg-secondary">Sắp diễn ra</span>
                        @elseif($activity->status == 'ongoing')
                            <span class="badge bg-primary">Đang diễn ra</span>
                        @elseif($activity->status == 'finished')
                            <span class="badge bg-success">Đã kết thúc</span>
                        @elseif($activity->status == 'cancelled')
                            <span class="badge bg-warning">Đã hủy</span>
                        @elseif($activity->status == 'disabled')
                            <span class="badge bg-danger">Bị vô hiệu hóa</span>
                        @endif
                        @if($activity->approval_status == 'pending')
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @elseif($activity->approval_status == 'approved')
                            <span class="badge bg-success">Đã duyệt</span>
                        @elseif($activity->approval_status == 'rejected')
                            <span class="badge bg-danger">Từ chối</span>
                        @endif
                    </p>
                    @if($activity->violation_notes)
                        <p><strong>Ghi chú vi phạm:</strong></p>
                        <div class="alert alert-danger">
                            {{ $activity->violation_notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- THỐNG KÊ THAM GIA --}}
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-people"></i> Thống kê tham gia</h5>
        </div>
        <div class="card-body">
            @php
                $isFinished = $activity->status === 'finished';
                $endAt = $activity->end_at ? \Carbon\Carbon::parse($activity->end_at) : null;
                if (!$isFinished && $endAt && $endAt->isPast()) {
                    $isFinished = true;
                }
            @endphp
            
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded">
                        <h4 class="text-primary">{{ $registeredCount }}</h4>
                        <p class="mb-0">Đã đăng ký</p>
                        <small class="text-muted">Tổng số người đăng ký</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded">
                        <h4 class="text-warning">{{ $approvedCount }}</h4>
                        <p class="mb-0">Đã duyệt</p>
                        <small class="text-muted">{{ $isFinished ? 'Được phép tham gia' : 'Chờ tham gia' }}</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded">
                        <h4 class="text-success">{{ $attendedCount }}</h4>
                        <p class="mb-0">{{ $isFinished ? 'Đã tham gia' : 'Chưa diễn ra' }}</p>
                        <small class="text-muted">{{ $isFinished ? 'Đã check-in' : 'Chưa thể check-in' }}</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded">
                        <h4 class="text-danger">{{ $registeredCount - $approvedCount - $attendedCount }}</h4>
                        <p class="mb-0">{{ $isFinished ? 'Vắng mặt' : 'Chưa duyệt' }}</p>
                        <small class="text-muted">
                            {{ $isFinished ? 'Đã duyệt nhưng không đến' : 'Đang chờ duyệt' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH NGƯỜI THAM GIA --}}
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i> 
                {{ $isFinished ? 'Danh sách người đã duyệt & tham gia' : 'Danh sách người đã được duyệt' }}
            </h5>
        </div>
        <div class="card-body">
            @if($participants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Họ tên</th>
                                <th>MSSV</th>
                                <th>Email</th>
                                <th>Trạng thái</th>
                                <th>Điểm hoạt động</th>
                                <th>Ngày đăng ký</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $index => $participant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $participant->name }}</td>
                                    <td>{{ $participant->student_code ?? 'N/A' }}</td>
                                    <td>{{ $participant->email ?? 'N/A' }}</td>
                                    <td>
                                        @if($participant->status == 'approved')
                                            <span class="badge bg-warning text-dark">
                                                {{ $isFinished ? 'Đã duyệt (chưa check-in)' : 'Đã duyệt' }}
                                            </span>
                                        @elseif($participant->status == 'attended')
                                            <span class="badge bg-success">Đã tham gia (check-in)</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($participant->activity_points > 0)
                                            <span class="badge bg-primary">{{ $participant->activity_points }} điểm</span>
                                        @else
                                            <span class="text-muted">Chưa có</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($participant->registered_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted py-4">
                    {{ $isFinished ? 'Chưa có người được duyệt hoặc tham gia' : 'Chưa có người được duyệt' }}
                </p>
            @endif
        </div>
    </div>
</div>

@endsection

