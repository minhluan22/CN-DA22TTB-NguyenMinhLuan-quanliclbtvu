@extends('layouts.admin')

@section('title', 'Danh sách thành viên CLB')

@section('content')
<div class="container-fluid mt-3">
    {{-- THÔNG báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- THÔNG báo lỗi --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h3 class="fw-bold mb-4">Quản lý thành viên CLB</h3>

    {{-- DANH SÁCH CLB VỚI SỐ LƯỢNG THÀNH VIÊN --}}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="bi bi-list-ul"></i> Danh sách CLB</h5>
            <div class="row g-3">
                @forelse ($clubs as $club)
                    <div class="col-md-4">
                        <div class="card h-100 club-card-item {{ request('club_id') == $club->id ? 'border-primary' : '' }}" 
                             style="cursor: pointer; transition: all 0.3s; background: white !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                             onclick="window.location.href='{{ route('admin.club-members.index', ['club_id' => $club->id]) }}'">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2 text-primary">{{ $club->code }}</h6>
                                <p class="mb-2 small">{{ $club->name }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary">
                                        <i class="bi bi-people"></i> {{ $memberCounts[$club->id] ?? 0 }} thành viên
                                    </span>
                                    @if($club->status == 'active')
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Không có CLB nào
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- CHỌN CLB & FILTER --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold mb-1">Chọn CLB</label>
                        <select name="club_id" class="form-select form-select-sm" onchange="this.form.submit()" required>
                            <option value="">-- Chọn CLB --</option>
                            @foreach ($clubs as $club)
                                <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->code }} - {{ $club->name }} ({{ $memberCounts[$club->id] ?? 0 }} thành viên)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($selectedClub)
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1">Tìm kiếm</label>
                            <input type="hidden" name="club_id" value="{{ $selectedClub->id }}">
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Tên hoặc MSSV..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1">Trạng thái</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ phê duyệt</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã phê duyệt</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Đình chỉ</option>
                                <option value="left" {{ request('status') == 'left' ? 'selected' : '' }}>Đã rời CLB</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1">Chức vụ</label>
                            <select name="position" class="form-select form-select-sm">
                                <option value="">-- Tất cả --</option>
                                <option value="chairman" {{ request('position') == 'chairman' ? 'selected' : '' }}>Chủ nhiệm</option>
                                <option value="vice_chairman" {{ request('position') == 'vice_chairman' ? 'selected' : '' }}>Phó chủ nhiệm</option>
                                <option value="secretary" {{ request('position') == 'secretary' ? 'selected' : '' }}>Thư ký CLB</option>
                                <option value="head_expertise" {{ request('position') == 'head_expertise' ? 'selected' : '' }}>Trưởng ban Chuyên môn</option>
                                <option value="head_media" {{ request('position') == 'head_media' ? 'selected' : '' }}>Trưởng ban Truyền thông</option>
                                <option value="head_events" {{ request('position') == 'head_events' ? 'selected' : '' }}>Trưởng ban Sự kiện</option>
                                <option value="treasurer" {{ request('position') == 'treasurer' ? 'selected' : '' }}>Trưởng ban Tài chính</option>
                                <option value="member" {{ request('position') == 'member' ? 'selected' : '' }}>Thành viên</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-search"></i> Tìm
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if ($selectedClub)
        {{-- THÔNG TIN CLB --}}
        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle"></i>
            <strong>CLB:</strong> {{ $selectedClub->name }} ({{ $selectedClub->code }}) | 
            <strong>Tổng thành viên:</strong> {{ $memberCount ?? 0 }} | 
            <strong>Trạng thái:</strong> 
            @if ($selectedClub->status == 'active')
                <span class="badge bg-success">Hoạt động</span>
            @else
                <span class="badge bg-warning text-dark">Chờ duyệt</span>
            @endif
        </div>

        {{-- BẢNG DANH SÁCH THÀNH VIÊN --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th>Tên thành viên</th>
                                <th>MSSV</th>
                                <th>Email</th>
                                <th>Chức vụ</th>
                                <th>Trạng thái</th>
                                <th>Ngày tham gia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $member)
                                <tr>
                                    <td>{{ ($members->currentPage() - 1) * $members->perPage() + $loop->iteration }}</td>
                                    <td><strong>{{ $member->name }}</strong></td>
                                    <td>{{ $member->student_code ?? '-' }}</td>
                                    <td class="small">{{ $member->email }}</td>
                                    
                                    {{-- CHỨC VỤ --}}
                                    <td>
                                        @if ($member->position == 'chairman')
                                            <span class="badge" style="background-color: #0033A0; color: white;">Chủ nhiệm</span>
                                        @elseif ($member->position == 'vice_chairman')
                                            <span class="badge" style="background-color: #FFE600; color: #000;">Phó chủ nhiệm</span>
                                        @elseif ($member->position == 'secretary')
                                            <span class="badge" style="background-color: #0B3D91; color: white;">Thư ký CLB</span>
                                        @elseif ($member->position == 'head_expertise')
                                            <span class="badge" style="background-color: #5FB84A; color: white;">Trưởng ban Chuyên môn</span>
                                        @elseif ($member->position == 'head_media')
                                            <span class="badge" style="background-color: #8EDC6E; color: #000;">Trưởng ban Truyền thông</span>
                                        @elseif ($member->position == 'head_events')
                                            <span class="badge" style="background-color: #FFE600; color: #000;">Trưởng ban Sự kiện</span>
                                        @elseif ($member->position == 'treasurer')
                                            <span class="badge" style="background-color: #0066CC; color: white;">Trưởng ban Tài chính</span>
                                        @else
                                            <span class="badge" style="background-color: #6BCB77; color: white;">Thành viên</span>
                                        @endif
                                    </td>
                                    
                                    {{-- TRẠNG THÁI --}}
                                    <td>
                                        @if ($member->status == 'pending')
                                            <span class="badge bg-warning text-dark">Chờ phê duyệt</span>
                                        @elseif ($member->status == 'approved')
                                            <span class="badge bg-success">Đã phê duyệt</span>
                                        @elseif ($member->status == 'rejected')
                                            <span class="badge bg-danger">Bị từ chối</span>
                                        @elseif ($member->status == 'suspended')
                                            <span class="badge bg-danger">Đình chỉ</span>
                                        @elseif ($member->status == 'left')
                                            <span class="badge bg-secondary">Đã rời CLB</span>
                                        @endif
                                    </td>
                                    
                                    <td>{{ $member->joined_date ? \Carbon\Carbon::parse($member->joined_date)->format('d/m/Y') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mb-0 mt-2">Không có thành viên nào</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PHÂN TRANG --}}
        <div class="mt-3 d-flex justify-content-center">
            {{ $members->links('vendor.pagination.custom') }}
        </div>
    @else
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> Vui lòng chọn một CLB để xem danh sách thành viên
        </div>
    @endif
</div>

@endsection

<style>
.club-card-item {
    transition: all 0.3s ease;
}

.club-card-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.club-card-item.border-primary {
    background-color: #f0f7ff;
}
</style>