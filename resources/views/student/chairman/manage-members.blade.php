@extends('layouts.chairman')

@section('title', 'Quản lý thành viên CLB - Chủ nhiệm')

@push('styles')
<style>
        :root {
            --primary: #0B3D91;
            --primary-blue: #0B3D91;
            --primary-blue-dark: #072C6A;
            --primary-blue-hover: #0C4CB8;
            --accent-yellow: #FFE600;
            --soft-yellow: #FFF7B0;
            --text-dark: #1f1f1f;
            --text-light: #ffffff;
            --secondary: #2b2f3a;
            --card: #ffffff;
            --muted: #6b7280;
            --border: #e5e7eb;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--soft-yellow);
            color: var(--text-dark);
        }
        .table-role {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        .btn-add-role {
            background-color: #0B3D91;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
        }
        .btn-add-role:hover {
            background-color: #0a2d6d;
        }
        .submenu {
            margin-left: 20px;
            margin-top: 4px;
        }
        .submenu a {
            font-size: 13px;
            padding: 8px 12px;
        }

        /* =========================================================
           CUSTOM PAGINATION STYLE
           → Style cho phân trang tùy chỉnh (giống y hệt trang Danh sách tài khoản Admin)
        ========================================================= */
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

        .pagination .page-item.active .page-link:hover {
            color: white;
            background-color: #0B3D91;
            border-color: #0B3D91;
        }

        .pagination .page-item.disabled .page-link,
        .pagination .page-item.disabled .page-link:hover,
        .pagination .page-item.disabled .page-link:focus {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
        }

        /* Đảm bảo phân trang hiển thị đúng trong container */
        nav[aria-label="Page navigation"] {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        nav[aria-label="Page navigation"] .pagination {
            margin: 0;
        }
    </style>
@endpush

@section('content')
        <h3 class="fw-bold mb-4">Quản lý thành viên CLB</h3>

        {{-- THÔNG BÁO --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>✅ Thành công!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>❌ Lỗi!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- THÔNG TIN CLB --}}
        <div class="alert alert-info mb-4">
            <strong>CLB:</strong> {{ $club->name }} ({{ $club->code }}) | 
            <strong>Tổng thành viên:</strong> {{ $memberCount }} | 
            <strong>Trạng thái:</strong> 
            @if ($club->status == 'active')
                <span class="badge bg-success">Hoạt động</span>
            @else
                <span class="badge bg-warning">Chờ duyệt</span>
            @endif
        </div>

        {{-- TÌM KIẾM & LỌC --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tên hoặc MSSV..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="">-- Tất cả --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ phê duyệt</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đang tham gia</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Bị từ chối</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Đình chỉ</option>
                            <option value="left" {{ request('status') == 'left' ? 'selected' : '' }}>Đã rời CLB</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold">Chức vụ</label>
                        <select name="position" class="form-control">
                            <option value="">-- Tất cả --</option>
                            <option value="chairman" {{ request('position') == 'chairman' ? 'selected' : '' }}>Chủ nhiệm</option>
                            <option value="vice_chairman" {{ request('position') == 'vice_chairman' ? 'selected' : '' }}>Phó chủ nhiệm</option>
                            <option value="secretary" {{ request('position') == 'secretary' ? 'selected' : '' }}>Thư ký CLB</option>
                            <option value="head_expertise" {{ request('position') == 'head_expertise' ? 'selected' : '' }}>Trưởng ban Chuyên môn</option>
                            <option value="head_media" {{ request('position') == 'head_media' ? 'selected' : '' }}>Trưởng ban Truyền thông</option>
                            <option value="head_events" {{ request('position') == 'head_events' ? 'selected' : '' }}>Trưởng ban Hoạt động</option>
                            <option value="treasurer" {{ request('position') == 'treasurer' ? 'selected' : '' }}>Trưởng ban Tài chính</option>
                            <option value="member" {{ request('position') == 'member' ? 'selected' : '' }}>Thành viên</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn w-100" style="background-color: #0B3D91; color: white;">
                            Tìm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- NÚT THÊM THÀNH VIÊN --}}
        <div class="text-end mb-3">
            <button type="button" class="btn-add-role" data-bs-toggle="modal" data-bs-target="#modalAddMember">
                <i class="bi bi-plus-circle"></i> Thêm thành viên
            </button>
        </div>

        {{-- BẢNG DANH SÁCH THÀNH VIÊN --}}
        <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
            <table class="table table-role" style="table-layout: auto; width: 100%;">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên thành viên</th>
                        <th>MSSV</th>
                        <th>Email</th>
                        <th>Chức vụ</th>
                        <th>Trạng thái</th>
                        <th>Ngày tham gia</th>
                        <th style="width: 180px; max-width: 180px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td>{{ ($members->currentPage() - 1) * $members->perPage() + $loop->iteration }}</td>
                            <td><strong>{{ $member->name }}</strong></td>
                            <td>{{ $member->student_code ?? '-' }}</td>
                            <td>{{ $member->email }}</td>
                            
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
                                    <span class="badge" style="background-color: #FFF3A0; color: #000;">Trưởng ban Sự kiện</span>
                                @elseif ($member->position == 'treasurer')
                                    <span class="badge" style="background-color: #0066CC; color: white;">Trưởng ban Tài chính</span>
                                @else
                                    <span class="badge" style="background-color: #6BCB77; color: white;">Thành viên</span>
                                @endif
                            </td>
                            
                            {{-- TRẠNG THÁI --}}
                            <td>
                                @if ($member->status == 'pending')
                                    <span class="badge bg-warning">⏳ Chờ phê duyệt</span>
                                @elseif ($member->status == 'approved')
                                    <span class="badge bg-success">✅ Đang tham gia</span>
                                @elseif ($member->status == 'rejected')
                                    <span class="badge bg-danger">❌ Bị từ chối</span>
                                @elseif ($member->status == 'suspended')
                                    <span class="badge bg-danger">🔒 Đình chỉ</span>
                                @elseif ($member->status == 'left')
                                    <span class="badge bg-secondary">👋 Đã rời CLB</span>
                                @endif
                            </td>
                            
                            <td>{{ $member->joined_date ? \Carbon\Carbon::parse($member->joined_date)->format('d/m/Y') : '-' }}</td>
                            
                            {{-- HÀNH ĐỘNG --}}
                            <td style="width: 180px; max-width: 180px; padding: 8px;">
                                <div class="d-flex flex-row gap-1" style="flex-wrap: nowrap; justify-content: center; align-items: center;">
                                    {{-- NÚT SỬA --}}
                                    @if ($member->position != 'chairman' || $member->user_id != Auth::id())
                                        <button class="btn btn-sm action-btn" style="background-color: #0B3D91; color: white; border: none; font-weight: 500; padding: 3px 6px; font-size: 11px; white-space: nowrap; flex-shrink: 0;"
                                                data-bs-toggle="modal" data-bs-target="#modalEditMember"
                                                onclick="loadMemberToEdit('{{ $member->id }}', '{{ $member->position }}', '{{ $member->status }}')">
                                            Sửa
                                        </button>
                                    @endif

                                    {{-- NÚT PHÊ DUYỆT (nếu chờ phê duyệt) --}}
                                    @if ($member->status == 'pending')
                                        <form action="{{ route('student.chairman.approve-member', $member->id) }}" method="POST" style="display: inline; flex-shrink: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm action-btn" style="padding: 3px 6px; font-size: 11px; white-space: nowrap;" onclick="return confirm('Phê duyệt thành viên này?')">
                                                Phê duyệt
                                            </button>
                                        </form>
                                        <form action="{{ route('student.chairman.reject-member', $member->id) }}" method="POST" style="display: inline; flex-shrink: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm action-btn" style="padding: 3px 6px; font-size: 11px; white-space: nowrap;" onclick="return confirm('Từ chối thành viên này?')">
                                                Từ chối
                                            </button>
                                        </form>
                                    @endif

                                    {{-- NÚT ĐÌNH CHỈ (nếu đã phê duyệt) --}}
                                    @if ($member->status == 'approved' && $member->position != 'chairman')
                                        <form action="{{ route('student.chairman.suspend-member', $member->id) }}" method="POST" style="display: inline; flex-shrink: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm action-btn" style="padding: 3px 6px; font-size: 11px; white-space: nowrap;" onclick="return confirm('Đình chỉ thành viên này?')">
                                                Đình chỉ
                                            </button>
                                        </form>
                                    @endif

                                    {{-- NÚT KÍCH HOẠT LẠI (nếu bị đình chỉ) --}}
                                    @if ($member->status == 'suspended')
                                        <form action="{{ route('student.chairman.activate-member', $member->id) }}" method="POST" style="display: inline; flex-shrink: 0;">
                                            @csrf
                                            <button type="submit" class="btn btn-info btn-sm action-btn" style="padding: 3px 6px; font-size: 11px; white-space: nowrap;" onclick="return confirm('Kích hoạt lại thành viên này?')">
                                                Kích hoạt
                                            </button>
                                        </form>
                                    @endif

                                    {{-- NÚT XÓA --}}
                                    @if ($member->position != 'chairman' || $member->user_id != Auth::id())
                                        <form action="{{ route('student.chairman.remove-member', $member->id) }}" method="POST" style="display: inline; flex-shrink: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm action-btn" style="padding: 3px 6px; font-size: 11px; white-space: nowrap;" onclick="return confirm('Bạn chắc chắn muốn xóa thành viên này?')">
                                                Xóa
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Không có thành viên nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PHÂN TRANG --}}
        @if($members->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                {{ $members->links('vendor.pagination.custom') }}
            </div>
        @endif

    {{-- ===================== MODALS ===================== --}}

    {{-- MODAL THÊM THÀNH VIÊN --}}
    <div class="modal fade" id="modalAddMember" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm thành viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('student.chairman.add-member') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn sinh viên</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- Chọn sinh viên --</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->name }} ({{ $student->student_code }}) - {{ $student->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Chức vụ</label>
                            <select name="position" class="form-control" required>
                                <option value="member">Thành viên</option>
                                @php
                                    $positionLimits = [
                                        'vice_chairman' => 2,
                                        'secretary' => 1,
                                        'head_expertise' => 1,
                                        'head_media' => 1,
                                        'head_events' => 1,
                                        'treasurer' => 1,
                                    ];
                                    // Sử dụng $positionCounts từ controller (đã được truyền vào view)
                                    $currentCounts = $positionCounts ?? [];
                                @endphp
                                <option value="vice_chairman" {{ ($currentCounts['vice_chairman'] ?? 0) >= $positionLimits['vice_chairman'] ? 'disabled' : '' }}>
                                    Phó chủ nhiệm{{ ($currentCounts['vice_chairman'] ?? 0) >= $positionLimits['vice_chairman'] ? ' (Đã đủ - ' . $positionLimits['vice_chairman'] . ' người)' : '' }}
                                </option>
                                <option value="secretary" {{ ($currentCounts['secretary'] ?? 0) >= $positionLimits['secretary'] ? 'disabled' : '' }}>
                                    Thư ký CLB{{ ($currentCounts['secretary'] ?? 0) >= $positionLimits['secretary'] ? ' (Đã đủ - ' . $positionLimits['secretary'] . ' người)' : '' }}
                                </option>
                                <option value="head_expertise" {{ ($currentCounts['head_expertise'] ?? 0) >= $positionLimits['head_expertise'] ? 'disabled' : '' }}>
                                    Trưởng ban Chuyên môn{{ ($currentCounts['head_expertise'] ?? 0) >= $positionLimits['head_expertise'] ? ' (Đã đủ - ' . $positionLimits['head_expertise'] . ' người)' : '' }}
                                </option>
                                <option value="head_media" {{ ($currentCounts['head_media'] ?? 0) >= $positionLimits['head_media'] ? 'disabled' : '' }}>
                                    Trưởng ban Truyền thông{{ ($currentCounts['head_media'] ?? 0) >= $positionLimits['head_media'] ? ' (Đã đủ - ' . $positionLimits['head_media'] . ' người)' : '' }}
                                </option>
                                <option value="head_events" {{ ($currentCounts['head_events'] ?? 0) >= $positionLimits['head_events'] ? 'disabled' : '' }}>
                                    Trưởng ban Hoạt động{{ ($currentCounts['head_events'] ?? 0) >= $positionLimits['head_events'] ? ' (Đã đủ - ' . $positionLimits['head_events'] . ' người)' : '' }}
                                </option>
                                <option value="treasurer" {{ ($currentCounts['treasurer'] ?? 0) >= $positionLimits['treasurer'] ? 'disabled' : '' }}>
                                    Trưởng ban Tài chính{{ ($currentCounts['treasurer'] ?? 0) >= $positionLimits['treasurer'] ? ' (Đã đủ - ' . $positionLimits['treasurer'] . ' người)' : '' }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select name="status" class="form-control" required>
                                <option value="pending">Chờ phê duyệt</option>
                                <option value="approved">Đang tham gia</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ngày tham gia</label>
                            <input type="date" name="joined_date" class="form-control" value="{{ now()->toDateString() }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn" style="background-color: #0B3D91; color: white;">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL CHỈNH SỬA THÀNH VIÊN --}}
    <div class="modal fade" id="modalEditMember" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa thành viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editMemberForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chức vụ</label>
                            <select id="edit_position" name="position" class="form-control" required>
                                <option value="member">Thành viên</option>
                                <option value="vice_chairman">Phó chủ nhiệm</option>
                                <option value="secretary">Thư ký CLB</option>
                                <option value="head_expertise">Trưởng ban Chuyên môn</option>
                                <option value="head_media">Trưởng ban Truyền thông</option>
                                <option value="head_events">Trưởng ban Hoạt động</option>
                                <option value="treasurer">Trưởng ban Tài chính</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái</label>
                            <select id="edit_status" name="status" class="form-control" required>
                                <option value="pending">Chờ phê duyệt</option>
                                <option value="approved">Đang tham gia</option>
                                <option value="rejected">Bị từ chối</option>
                                <option value="suspended">Đình chỉ</option>
                                <option value="left">Đã rời CLB</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn" style="background-color: #0B3D91; color: white;">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
        // Dữ liệu số lượng chức vụ hiện có
        const positionCounts = @json($positionCounts ?? []);
        const positionLimits = {
            'vice_chairman': 2,
            'secretary': 1,
            'head_expertise': 1,
            'head_media': 1,
            'head_events': 1,
            'treasurer': 1
        };

        function loadMemberToEdit(memberId, position, status) {
            const editPositionSelect = document.getElementById('edit_position');
            const currentPosition = position;
            
            // Reset và cập nhật options
            editPositionSelect.innerHTML = '<option value="member">Thành viên</option>';
            
            // Thêm các chức vụ với logic disable
            const positions = [
                { value: 'vice_chairman', name: 'Phó chủ nhiệm' },
                { value: 'secretary', name: 'Thư ký CLB' },
                { value: 'head_expertise', name: 'Trưởng ban Chuyên môn' },
                { value: 'head_media', name: 'Trưởng ban Truyền thông' },
                { value: 'head_events', name: 'Trưởng ban Hoạt động' },
                { value: 'treasurer', name: 'Trưởng ban Tài chính' }
            ];

            positions.forEach(pos => {
                const option = document.createElement('option');
                option.value = pos.value;
                
                // Đếm số lượng (trừ member hiện tại nếu không phải chức vụ này)
                let currentCount = positionCounts[pos.value] || 0;
                if (currentPosition === pos.value) {
                    currentCount = Math.max(0, currentCount - 1); // Trừ member hiện tại
                }
                
                const limit = positionLimits[pos.value];
                const isFull = currentCount >= limit;
                
                if (isFull && currentPosition !== pos.value) {
                    option.disabled = true;
                    option.textContent = pos.name + ' (Đã đủ - ' + limit + ' người)';
                } else {
                    option.textContent = pos.name;
                }
                
                if (currentPosition === pos.value) {
                    option.selected = true;
                }
                
                editPositionSelect.appendChild(option);
            });
            
            document.getElementById('edit_status').value = status;
            document.getElementById('editMemberForm').action = '/student/chairman/update-member/' + memberId;
        }
    </script>
@endpush

